<?php

namespace App\Controller;

use DateTime;
use Dompdf\Dompdf;
use Stripe\Stripe;
use Dompdf\Options;
use App\Entity\Panier;
use App\Entity\Commande;
use App\Entity\Messages;
use App\Entity\Abonnement;
use Stripe\Checkout\Session;
use App\Repository\UserRepository;
use App\Repository\ProduitRepository;
use App\Repository\CommandeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class PaiementController extends AbstractController {
    #[Route('/paiement', name: 'app_paiement')]
    public function index(SessionInterface $session, ProduitRepository $pr, CommandeRepository $cr): Response {
        $panier = $session->get('panier', []);
        Stripe::setApiKey($this->getParameter('stripeSecretKey'));

        if (empty($panier)) {
            return $this->redirectToRoute('app_home');
        }

        $ids = $panier;
        $produits = $pr->getAllProduits($ids);



        $commande = new Commande;
        $commande->setEtat('En cours');
        $commande->setReference('ref_' . uniqid());
        $commande->setCreerA(new DateTime());
        $commande->setUser($this->getUser());
        $commande->setToken(hash('sha256', random_bytes(32)));

        $line_items = [];

        foreach ($panier as $id => $quantite) {
            $produit = $produits[$id];
            $line_items[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => $produit->getNom(),
                        'images' => [$produit->getImg()]
                    ],
                    'unit_amount' => $produit->getPrix() * 100 // Montant en centimes
                ],
                'quantity' => $quantite['quantite'],
            ];
        }
        $cr->add($commande);

        $checkout = Session::create([
            'line_items' => $line_items,
            'mode' => 'payment',
            'success_url' => $this->generateUrl('paiement_success', ['token' => $commande->getToken()], UrlGeneratorInterface::ABSOLUTE_URL),
            'cancel_url' => $this->generateUrl('paiement_cancel', [], UrlGeneratorInterface::ABSOLUTE_URL),
        ]);

        return $this->redirect($checkout->url);
    }



    #[Route('/success_paiement/{token}', name: 'paiement_success')]
    public function success($token, UserRepository $ur, MailerInterface $mailer, SessionInterface $session, CommandeRepository $cr, ProduitRepository $pr, EntityManagerInterface $em): Response {
        $panier1 = $session->get('panier');
        $commande = $cr->findOneBy([
            'token' => $token
        ]);
        $refCommande = $commande->getReference();
        $user = $this->getUser();
        $admin = $ur->findByRole('ROLE_ADMIN');

        $total = 0;

        $ids = $panier1;
        $produits = $pr->getAllProduits($ids);




        if (empty($commande)) throw new AccessDeniedHttpException;

        foreach ($panier1 as $id => $quantite) {
            $panier = new Panier;
            $produit = $produits[$id];
            $panier->setProduit($produit);
            $panier->setCommande($commande);
            $em->persist($panier);
            $em->flush();


            $total += $produit->getPrix() * $quantite['quantite'];


            if ($produit->getEstprogramme() === true) {

                $email = new TemplatedEmail();

                $pathProgramme = $this->getParameter('programmeDirectory') . '/' . $produit->getNom() . '.pdf';

                $email->from('Programme' . ' ' . 'Refuge des Combattants' . ' <' . 'lerefugedescombattants@gmail.com' . '>')
                    ->to($user->getEmail())
                    ->replyTo('lerefugedescombattants@gmail.com')
                    ->subject($produit->getNom())
                    ->htmlTemplate('emails_template/programme.html.twig')
                    ->context([
                        'fromEmail' => 'lerefugedescombattants@gmail.com',
                        'message' => 'Merci pour votre confiance ! Vous trouverez en pièce jointe un programme complet ! Si vous avez ne serait-ce qu\'une seule question n\'hésitez surtout pas à nous contacter !'
                    ])
                    ->attachFromPath($pathProgramme, null, 'application/pdf');



                $mailer->send($email);
            } else {

                /*
                * Messages à tous les coachs prévenant qu'il y a un nouvel adhérent + Message à l'adhérent que son achat du forfait est bien prise en compte 
                */

                $users = $ur->findAll();

                $message = new Messages;

                foreach ($users as $us) {
                    if ($us->getEstcoach() === true) {
                        $message->setDestinataire($us);
                    }

                    $message->setMessage('Un nouvel adhérent à préscrit le ' . $produit->getNom() . ', habite au ' . $user->getAdresse() . ' ' . 'à' . ' ' . strtoupper($user->getVille()) .  ', Allez check votre profil de coach :).');
                    $message->setTitre('Nouvel adhérent');
                    $message->setExpediteur($admin[0]);

                    $em->persist($message);
                    $em->flush();
                }

                $messageForAdherent = new Messages;

                $messageForAdherent->setMessage('Suite à la prise de ton forfait, tu seras mis en contact très rapidement par un de nos coachs professionel. 48h jours ouvrés.');
                $messageForAdherent->setTitre('Achat de forfait');
                $messageForAdherent->setDestinataire($user);
                $messageForAdherent->setExpediteur($admin[0]);

                $user->setAttenteDunCoach(1);

                $abonnement = new Abonnement;

                $abonnement->setUser($user);
                $abonnement->setForfait($produit);
                $abonnement->setEncours(1);
                $abonnement->setRefCommande($refCommande);

                $em->persist($messageForAdherent);
                $em->flush();
                $em->persist($user);
                $em->flush();
                $em->persist($abonnement);
                $em->flush();
            }
        };






        /*
        *  Génération de la facture en pdf + stockage dans public/factures 
        */
        $options = new Options();
        $options->setIsRemoteEnabled(true);
        $dompdf = new Dompdf($options);


        $html = $this->renderView('emails_template/facturePdf.html.twig', [
            'produits' => $produits,
            'user' => $user,
            'total' => $total,
            'commande' => $commande,

        ]);



        $dompdf->loadHtml($html);

        $dompdf->setPaper('A4', 'portrait');

        $dompdf->render();

        $output = $dompdf->output();

        $directoryFacture = $this->getParameter('factureDirectory');
        $pathFacture = $directoryFacture . '/' . $commande->getToken() . '.pdf';

        file_put_contents($pathFacture, $output);




        if ($commande->getId() === $panier->getCommande()->getId()) {
            $commande->setEtat('Payé');
        }

        /*
        * Envoie de la confirmation de la facture par email + PDF 
        */

        if ($commande->getEtat() === 'Payé') {
            $email = new TemplatedEmail();

            $email->from('Facture' . ' ' . 'Refuge des Combattants' . ' <' . 'lerefugedescombattants@gmail.com' . '>')
                ->to($user->getEmail())
                ->replyTo('lerefugedescombattants@gmail.com')
                ->subject('Facture nº' . $commande->getReference())
                ->htmlTemplate('emails_template/facture.html.twig')
                ->context([
                    'produits' => ($produits),
                    'fromEmail' => 'lerefugedescombattants@gmail.com',
                    'message' => 'Vous trouverez en pièce jointe une facture concernant vos achats ! N\'hésitez pas à nous contactez pour quelconque questions. Récapitulatif des produits achetés :'
                ])
                ->attachFromPath($pathFacture, null, 'application/pdf');



            $mailer->send($email);


            $message = new Messages;

            $message->setMessage('Merci pour ta confiance, tu as reçu une confirmation, une facture ainsi le/les programme(s) à cet email : ' . $user->getEmail() . ' .Pour tout soucis veuillez nous contactez.');
            $message->setTitre('Merci pour ta confiance !');
            $message->setExpediteur($admin[0]);
            $message->setDestinataire($user);

            $em->persist($message);
            $em->flush();
        }



        $session->set('panier', []);


        $cr->add($commande);

        return $this->redirectToRoute('app_profil_commande');
    }


    #[Route('/cancel_paiement', name: 'paiement_cancel')]
    public function cancel(): Response {
        return $this->render('home/home.html.twig');
    }
}

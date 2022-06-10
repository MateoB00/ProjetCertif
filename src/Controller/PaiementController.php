<?php

namespace App\Controller;

use DateTime;
use Stripe\Stripe;
use App\Entity\Panier;
use App\Entity\Commande;
use Stripe\Checkout\Session;
use App\Repository\PanierRepository;
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
    public function index(SessionInterface $session, ProduitRepository $pr, CommandeRepository $cr, EntityManagerInterface $em): Response {
        $panier = $session->get('panier', []);
        Stripe::setApiKey($this->getParameter('stripeSecretKey'));

        if (empty($panier)) {
            return $this->redirectToRoute('app_home');
        }

        $ids = $panier;
        $produits = $pr->getAllProduits($ids);

        // $idUser = $this->getUser()->getId();

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
    public function success($token, MailerInterface $mailer, SessionInterface $session, CommandeRepository $cr, ProduitRepository $pr, EntityManagerInterface $em): Response {
        $panier1 = $session->get('panier');
        $commande = $cr->findOneBy([
            'token' => $token
        ]);
        $user = $this->getUser();


        $ids = $panier1;
        $produits = $pr->getAllProduits($ids);

        // dd($produits);

        foreach ($panier1 as $id => $quantite) {
            $panier = new Panier;
            $produit = $produits[$id];
            $panier->setProduit($produit);
            $panier->setCommande($commande);
            $em->persist($panier);
            $em->flush();
            // dd($user->getEmail());
            // $programmes = $produits[$id]->getEstprogramme() === true;

            if ($produit->getEstprogramme() === true) {
                // dd($produit);
                $email = new TemplatedEmail();

                $email->from('Programme' . ' ' . 'Refuge des Combattants' . ' <' . 'lerefugedescombattants@gmail.com' . '>')
                    ->to($user->getEmail())
                    ->replyTo('lerefugedescombattants@gmail.com')
                    ->subject($produit->getNom())
                    ->htmlTemplate('emails_template/contact.html.twig')
                    ->context([
                        // 'fromEmail' => $data['email'],
                        // 'message' => nl2br($data['message']),
                        // 'tel' => ($data['telephone'])
                        'fromEmail' => 'lerefugedescombattants@gmail.com',
                        'message' => 'programme'
                    ]);


                $mailer->send($email);
            }
        };
        // dd($programmes);

        // dd($commande, $panier1, $panier);

        if (empty($commande)) throw new AccessDeniedHttpException;



        if ($commande->getId() === $panier->getCommande()->getId()) {
            $commande->setEtat('Payé');
        }



        $session->set('panier', []);

        $cr->add($commande);

        return $this->render('home/home.html.twig');
    }


    #[Route('/cancel_paiement', name: 'paiement_cancel')]
    public function cancel(): Response {
        return $this->render('home/home.html.twig');
    }
}

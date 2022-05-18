<?php

namespace App\Controller;

use DateTime;
use Stripe\Stripe;
use App\Entity\Panier;
use App\Entity\Commande;
use Stripe\Checkout\Session;
use App\Repository\ProduitRepository;
use App\Repository\CommandeRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use App\Repository\PanierRepository;

class PaiementController extends AbstractController
{
    #[Route('/paiement', name: 'app_paiement')]
    public function index(SessionInterface $session, ProduitRepository $pr, CommandeRepository $cr): Response
    {
        $panier = $session->get('panier', []);

        Stripe::setApiKey($this->getParameter('stripeSecretKey'));

        if (empty($panier)) {
            return $this->redirectToRoute('app_home');
        }

        $ids = array_keys($panier);
        $produits = $pr->getAllProduits($ids);

        $idUser = $this->getUser()->getId();

        $commande = $cr->findOneBy(['user' => $idUser]);

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
                'quantity' => $quantite,
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
    public function success($token, SessionInterface $session, CommandeRepository $cr, PanierRepository $pr): Response
    {
        $commande = $cr->findOneBy([
            'token' => $token
        ]);
        dd($commande);
        $panier = $pr->findOneBy([
            'commande' => $commande
        ]);

        if (empty($commande)) throw new AccessDeniedHttpException;

        // $panier = $pr->findAll();
        // $idsPanier = $panier->getId();
        // dd($panier);

        if ($commande->getId() === $panier->getCommande()->getId()) {
            $panier->setEtat('Payedé');
        }

        $session->set('panier', []);

        $cr->add($commande);
        dd($panier);

        return $this->render('home/home.html.twig');
    }


    #[Route('/cancel_paiement', name: 'paiement_cancel')]
    public function cancel(): Response
    {
        return $this->render('home/home.html.twig');
    }
}

<?php

namespace App\Controller;


use DateTime;
use App\Entity\Panier;
use App\Entity\Produit;
use App\Entity\Commande;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PanierController extends AbstractController
{
    #[Route('/panier', name: 'app_panier')]
    public function index(SessionInterface $session, ProduitRepository $pr): Response
    {

        $panier = $session->get("panier", []);

        // On "fabrique" les données
        $dataPanier = [];
        $total = 0;

        foreach ($panier as $id => $quantite) {
            $product = $pr->find($id);
            // dd($product);
            $dataPanier[] = [
                "produit" => $product,
                "quantite" => $quantite
            ];
            $total += $product->getPrix() * $quantite;
        }

        return $this->render('panier/index.html.twig', compact("dataPanier", "total"));
    }

    /**
     * @Route("/add/{id}", name="add")
     */
    public function add(Produit $produit, SessionInterface $session, EntityManagerInterface $em)
    {
        // On récupère le panier actuel
        $panier = $session->get("panier", []);
        $id = $produit->getId();
        // $panierBdd = $pr->find
        $newCommande = new Commande;
        $user = $this->getUser();
        $commandeUser = $user->getCommandes();
        // dd($panierBd);
        // dd($newCommande);


        if (!empty($panier[$id])
            //  || !empty($panierBa)
        ) {
            $panier[$id]++;
            // dd($panierBa);
            // $panierBa->setProduit($produit);
            // $panierBa->setCommande($commandeUser);

            // $em->persist($panierBa);
            // $em->flush();
            // $panierBd = $newCommande->getPanie+rs();
            // $panierBd = setProduit($id);
        } else {
            $panier[$id] = 1;
            $panierBd = new Panier;
            $panierBd->setProduit($produit);
            $panierBd->setEtat('En cours');
            $commande = $newCommande;
            $panierBd->setCommande($commande);
            $commande->setReference('ref_' . uniqid());
            $commande->setToken(hash('sha256', random_bytes(32)));
            $commande->setCreerA(new DateTime());
            $commande->setUser($this->getUser());

            $em->persist($panierBd);
            $em->flush();
            // dd($panierBd);
        }

        // On sauvegarde dans la session
        $session->set("panier", $panier);

        return $this->redirectToRoute("app_panier");
    }

    /**
     * @Route("/remove/{id}", name="remove")
     */
    public function remove(Produit $produit, SessionInterface $session)
    {
        // On récupère le panier actuel
        $panier = $session->get("panier", []);
        $id = $produit->getId();

        if (!empty($panier[$id])) {
            if ($panier[$id] > 1) {
                $panier[$id]--;
            } else {
                unset($panier[$id]);
            }
        }

        // On sauvegarde dans la session
        $session->set("panier", $panier);

        return $this->redirectToRoute("app_panier");
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(Produit $produit, SessionInterface $session)
    {
        // On récupère le panier actuel
        $panier = $session->get("panier", []);
        $id = $produit->getId();

        if (!empty($panier[$id])) {
            unset($panier[$id]);
        }

        // On sauvegarde dans la session
        $session->set("panier", $panier);

        return $this->redirectToRoute("app_panier");
    }

    /**
     * @Route("/delete", name="delete_all")
     */
    public function deleteAll(SessionInterface $session)
    {

        $session->remove("panier");

        return $this->redirectToRoute("app_panier");
    }
}

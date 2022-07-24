<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Entity\Entreepanier;
use App\Form\AjoutproduitType;
use App\Repository\UserRepository;
use App\Repository\PanierRepository;
use App\Repository\ProduitRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class NosTarifsController extends AbstractController {
    #[Route('/nos_tarifs', name: 'app_nos_tarifs')]
    public function index(ProduitRepository $pr): Response {
        $produits = $pr->findAll();

        return $this->render('nos_tarifs/nostarifs.html.twig', [
            'produits' => $produits,
        ]);
    }


    #[Route('/nos_tarifs/produit/{id}', name: 'app_produit')]
    public function details($id, ProduitRepository $pr): Response {

        $produit = $pr->find($id);

        return $this->render('nos_tarifs/produit.html.twig', [
            'produit' => $produit
        ]);
    }
}

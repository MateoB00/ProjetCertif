<?php

namespace App\Controller;

use App\Repository\ProduitRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProgrammesController extends AbstractController
{
    #[Route('/programmes', name: 'app_programmes')]
    public function index(ProduitRepository $pr): Response
    {
        $programmes = $pr->findAll();

        return $this->render('programmes/programmes.html.twig', [
            'programmes' => $programmes,
        ]);
    }
}

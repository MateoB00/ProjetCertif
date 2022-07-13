<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController {
    #[Route('/', name: 'app_home')]
    public function index(UserRepository $ur): Response {
        $coachs = $ur->findBy([
            'estcoach' => 1
        ]);


        return $this->render('home/home.html.twig', [
            'coachs' => $coachs
        ]);
    }
}

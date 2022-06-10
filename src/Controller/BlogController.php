<?php

namespace App\Controller;

use App\Entity\Blog;
use App\Form\BlogType;
use App\Repository\BlogRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;



class BlogController extends AbstractController
{

    // #[Route('/nos_blogs', name: 'app_blog_index', methods: ['GET'])]
    // public function index(BlogRepository $blogRepository): Response
    // {
    //     return $this->render('blog/index.html.twig', [
    //         'blogs' => $blogRepository->findAll(),
    //     ]);
    // }
    // #[Route('/blog/{id}', name: 'app_blog_show', methods: ['GET'])]
    // public function show(Blog $blog): Response
    // {
    //     return $this->render('blog/show.html.twig', [
    //         'blog' => $blog,
    //     ]);
    // }
}

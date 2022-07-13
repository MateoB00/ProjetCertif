<?php

namespace App\Controller;

use App\Entity\Blog;
use App\Entity\Commentaire;
use App\Form\CommentaireType;
use App\Repository\BlogRepository;
use App\Repository\CategorieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BlogsVueController extends AbstractController {
    #[Route('/blogs', name: 'app_blog')]
    public function liste(BlogRepository $br, CategorieRepository $cr): Response {

        return $this->render('blog/blogs.html.twig', [
            'blogs' => $br->findAll(),
            'categories' => $cr->findAll(),
        ]);
    }

    #[Route('/blog/details/{blog}', name: 'app_blog_details')]
    public function details(Blog $blog, EntityManagerInterface $em, BlogRepository $br, Request $request): Response {


        $commentaire = new Commentaire;
        $form = $this->createForm(CommentaireType::class, $commentaire);
        $form->handleRequest($request);
        $blogs = $br->findAll();


        if ($form->isSubmitted() && $form->isValid()) {
            $commentaire->setAuteur($this->getUser());
            $commentaire->setBlog($blog);
            $idBlog = $commentaire->getBlog()->getId();

            $em->persist($commentaire);
            $em->flush();
        }
        return $this->render('blog/blog_details.html.twig', [
            'blog' => $blog,
            'blogs' => $blogs,
            'form' => $form->createView()
        ]);
    }

    #[Route('/blog/commentaire/delete/{id}', name: 'app_blog_commentaire_delete')]
    public function deleteCommentaire(Commentaire $commentaire, Request $request, EntityManagerInterface $em): Response {

        if ($this->isCsrfTokenValid('deleteCommentaire', $request->request->get('_token'))) {
            $em->remove($commentaire);
            $em->flush();
        }
        return $this->redirectToRoute('app_blog_details', ['blog' => $commentaire->getBlog()->getId()]);
    }
}

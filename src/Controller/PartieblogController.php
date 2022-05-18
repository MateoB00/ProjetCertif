<?php

namespace App\Controller;

use App\Entity\Blog;
use App\Entity\Partieblog;
use App\Form\PartieblogType;
use App\Repository\PartieblogRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/partieblog')]
class PartieblogController extends AbstractController
{
    // #[Route('/', name: 'app_partieblog_index', methods: ['GET'])]
    // public function index(PartieblogRepository $partieblogRepository): Response
    // {
    //     return $this->render('partieblog/index.html.twig', [
    //         'partieblogs' => $partieblogRepository->findAll(),
    //     ]);
    // }



    // #[Route('/{id}', name: 'app_partieblog_show', methods: ['GET'])]
    // public function show(Partieblog $partieblog): Response
    // {
    //     return $this->render('partieblog/show.html.twig', [
    //         'partieblog' => $partieblog,
    //     ]);
    // }

    // #[Route('/{id}/edit/', name: 'app_partieblog_edit', methods: ['GET', 'POST'])]
    // public function edit(Request $request, Partieblog $partieblog, PartieblogRepository $partieblogRepository): Response
    // {
    //     $form = $this->createForm(PartieblogType::class, $partieblog);
    //     $form->handleRequest($request);

    //     if ($form->isSubmitted() && $form->isValid()) {
    //         $partieblogRepository->add($partieblog);
    //         return $this->redirectToRoute('app_partieblog_index', [], Response::HTTP_SEE_OTHER);
    //     }

    //     return $this->renderForm('partieblog/edit.html.twig', [
    //         'partieblog' => $partieblog,
    //         'form' => $form,
    //     ]);
    // }

    // #[Route('/{id}', name: 'app_partieblog_delete', methods: ['POST'])]
    // public function delete(Request $request, Partieblog $partieblog, PartieblogRepository $partieblogRepository): Response
    // {
    //     if ($this->isCsrfTokenValid('delete' . $partieblog->getId(), $request->request->get('_token'))) {
    //         $partieblogRepository->remove($partieblog);
    //     }

    //     return $this->redirectToRoute('app_partieblog_index', [], Response::HTTP_SEE_OTHER);
    // }
}

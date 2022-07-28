<?php

namespace App\Controller;

use DateTime;
use Exception;
use App\Entity\Blog;
use App\Entity\User;
use App\Form\BlogType;
use App\Entity\Produit;
use App\Entity\Commande;
use App\Form\ProduitType;
use App\Entity\Partieblog;
use App\Form\PartieblogType;
use App\Form\CoachUpdateType;
use App\Form\UpdateAdminUserType;
use App\Repository\BlogRepository;
use App\Repository\UserRepository;
use App\Repository\CoachRepository;
use App\Repository\ProduitRepository;
use App\Repository\CommandeRepository;
use App\Repository\PartieblogRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


/**
 * @IsGranted("ROLE_ADMIN")
 */

#[Route('/admin')]

class AdminController extends AbstractController {
    #[Route('/admin', name: 'app_choiceuser')]
    public function index(UserRepository $ur, ProduitRepository $pr, BlogRepository $br): Response {

        $users = $ur->findAll();
        $produits = $pr->findAll();
        $blogs = $br->findAll();



        return $this->render('admin/index.html.twig', [
            'produits' => $produits,
            'users' => $users,
            'blogs' => $blogs,
        ]);
    }




    /**
     * Modifier un user
     */

    #[Route('/user/update/{id}', name: 'app_updateuser')]
    public function updateUser($id, UserRepository $ur, Request $request): Response {
        $user = $ur->find($id);
        // dd($user);
        $formulaire = $this->createForm(UpdateAdminUserType::class, $user);

        $formulaire->handleRequest($request);

        if ($formulaire->isSubmitted() && $formulaire->isValid()) {
            $ur->add($user);


            return $this->redirectToRoute('app_home');
        } else {
            return $this->render('admin/user/update.html.twig', [
                'form' => $formulaire->createView(),
            ]);
        }
    }
    /**
     * Modifier un coach
     */

    #[Route('/coach/update/{id}', name: 'app_updatecoach')]
    public function updateCoach($id, UserRepository $cr, Request $request): Response {
        $coach = $cr->find($id);
        $formulaire = $this->createForm(CoachUpdateType::class, $coach);
        $formulaire->handleRequest($request);

        if ($formulaire->isSubmitted() && $formulaire->isValid()) {
            $image = $formulaire->get('coachpdp')->getData();
            $ok = true;

            if ($image) {
                $newName = 'coachpdp_' . uniqid() . '.' . $image->guessExtension();

                try {
                    $image->move(
                        $this->getParameter('imageDirectoryPDP'),
                        $newName
                    );

                    $coach->setCoachpdp($newName);
                } catch (Exception $e) {
                    $this->addFlash('errors', 'Problème dans l\'upload de l\'image');
                    $ok = false;
                }
            }
            if ($ok) {
                $cr->add($coach);

                return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
            }
        }

        return $this->render('admin/user/update_coach.html.twig', [
            'form' => $formulaire->createView(),
        ]);
    }


    /**
     * ----------------------------- PRODUIT ------------------------ 
     */


    /**
     * Nouveau produit
     */


    #[Route('/nouveau_produit', name: 'app_produit_nouveau', methods: ['GET', 'POST'])]
    public function new(Request $request, ProduitRepository $produitRepository): Response {
        $produit = new Produit();
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $image = $form->get('img')->getData();
            $ok = true;

            if ($image) {
                $newName = 'produit_' . uniqid() . '.' . $image->guessExtension();

                try {
                    $image->move(
                        $this->getParameter('imageDirectoryProduit'),
                        $newName
                    );

                    $produit->setImg($newName);
                } catch (Exception $e) {
                    $this->addFlash('errors', 'Problème dans l\'upload de l\'image');
                    $ok = false;
                }
            }


            if ($ok) {
                $produitRepository->add($produit);
                return $this->redirectToRoute('app_nos_tarifs', [], Response::HTTP_SEE_OTHER);
            }
        }

        return $this->renderForm('admin/produit/new.html.twig', [
            'produit' => $produit,
            'form' => $form,
        ]);
    }

    /**
     * Liste des produits
     */

    #[Route('/produit', name: 'app_produit_index', methods: ['GET'])]
    public function listeProduit(ProduitRepository $pr): Response {
        return $this->render('admin/produit/index.html.twig', [
            'produits' => $pr->findAll(),
        ]);
    }

    /**
     * Details produit
     */

    #[Route('/produit/{id}', name: 'app_produit_show', methods: ['GET'])]
    public function showProduit(Produit $produit): Response {
        return $this->render('admin/produit/show.html.twig', [
            'produit' => $produit,
        ]);
    }

    /**
     * Modifier un produit
     */

    #[Route('/produit/edit/{id}', name: 'app_produit_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Produit $produit, ProduitRepository $produitRepository): Response {
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $image = $form->get('img')->getData();
            $ok = true;


            if ($image) {
                $newName = 'produit_' . uniqid() . '.' . $image->guessExtension();

                try {
                    $image->move(
                        $this->getParameter('imageDirectoryProduit'),
                        $newName
                    );

                    $produit->setImg($newName);
                } catch (Exception $e) {
                    $this->addFlash('errors', 'Problème dans l\'upload de l\'image');
                    $ok = false;
                }
            }
            if ($ok) {
                $produitRepository->add($produit);
                return $this->redirectToRoute('app_nos_tarifs', [], Response::HTTP_SEE_OTHER);
            }
        }

        return $this->renderForm('admin/produit/edit.html.twig', [
            'produit' => $produit,
            'form' => $form,
        ]);
    }


    /**
     * Supprimer un produit
     */

    #[Route('/delete/produit/{id}', name: 'app_produit_delete', methods: ['POST'])]
    public function delete(Request $request, Produit $produit, ProduitRepository $produitRepository): Response {
        if ($this->isCsrfTokenValid('delete' . $produit->getId(), $request->request->get('_token'))) {
            $produitRepository->remove($produit);
        }
        if (!empty($produit->getImg()) && file_exists($this->getParameter('imageDirectory') . '/' . $produit->getImg())) {

            unlink($this->getParameter('imageDirectory') . '/' . $produit->getImg());
        }
        return $this->redirectToRoute('app_nos_tarifs', [], Response::HTTP_SEE_OTHER);
    }


    /**
     * 
     * BLOG
     * 
     */

    /**
     * Nouveau blog
     */

    #[Route('/blog/new/{user}', name: 'app_blog_new', methods: ['GET', 'POST'])]
    public function newBlog(Request $request, BlogRepository $blogRepository, User $user): Response {
        $blog = new Blog();
        $blog->setAuteur($user);
        $blog->setDate(new DateTime);
        $form = $this->createForm(BlogType::class, $blog);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $image = $form->get('image')->getData();
            $ok = true;

            if ($image) {
                $newName = 'blog_' . uniqid() . '.' . $image->guessExtension();

                try {
                    $image->move(
                        $this->getParameter('imageDirectoryBlog'),
                        $newName
                    );

                    $blog->setImage($newName);
                } catch (Exception $e) {
                    $this->addFlash('errors', 'Problème dans l\'upload de l\'image');
                    $ok = false;
                }
            }
            if ($ok) {
                $blogRepository->add($blog);
                return $this->redirectToRoute('app_blog_index', [], Response::HTTP_SEE_OTHER);
            }
        }

        return $this->renderForm('admin/blog/new.html.twig', [
            'blog' => $blog,
            'form' => $form,
        ]);
    }


    /**
     * Liste blogs
     */

    #[Route('/nos_blogs', name: 'app_blog_index', methods: ['GET'])]
    public function listeBlogs(BlogRepository $blogRepository): Response {
        return $this->render('admin/blog/index.html.twig', [
            'blogs' => $blogRepository->findAll(),
        ]);
    }


    /**
     * Details d'un Blog
     */


    #[Route('/blog/{id}', name: 'app_blog_show', methods: ['GET'])]
    public function detailsBlog(Blog $blog): Response {
        return $this->render('admin/blog/show.html.twig', [
            'blog' => $blog,
        ]);
    }


    /**
     * Modifier un blog
     */


    #[Route('/blog/edit/{id}', name: 'app_blog_edit', methods: ['GET', 'POST'])]
    public function editBlog(Request $request, Blog $blog, BlogRepository $blogRepository): Response {
        $form = $this->createForm(BlogType::class, $blog);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $image = $form->get('image')->getData();
            $ok = true;

            if ($image) {
                $newName = 'produit_' . uniqid() . '.' . $image->guessExtension();

                try {
                    $image->move(
                        $this->getParameter('imageDirectoryBlog'),
                        $newName
                    );

                    $blog->setImage($newName);
                } catch (Exception $e) {
                    $this->addFlash('errors', 'Problème dans l\'upload de l\'image');
                    $ok = false;
                }
            }

            if ($ok) {
                $blogRepository->add($blog);
                return $this->redirectToRoute('app_blog_index', [], Response::HTTP_SEE_OTHER);
            }
        }
        return $this->renderForm('admin/blog/edit.html.twig', [
            'blog' => $blog,
            'form' => $form,
        ]);
    }


    /**
     * Supprimer un blog
     */

    #[Route('/delete/blog/{id}', name: 'app_blog_delete', methods: ['POST'])]
    public function deleteBlog(Request $request, Blog $blog, BlogRepository $blogRepository): Response {

        if ($this->isCsrfTokenValid('delete' . $blog->getId(), $request->request->get('_token'))) {
            $blogRepository->remove($blog);
        }

        if (!empty($blog->getImage()) && file_exists($this->getParameter('imageDirectoryBlog') . '/' . $blog->getImage())) {

            unlink($this->getParameter('imageDirectoryBlog') . '/' . $blog->getImage());
        }
        return $this->redirectToRoute('app_blog_index', [], Response::HTTP_SEE_OTHER);
    }


    /**
     * PARTIES - BLOG
     */

    /**
     * Nouvelle partie d'un blog
     */


    #[Route('/parties_blog/new/{blog}', name: 'app_partieblog_new', methods: ['GET', 'POST'])]
    public function newPartieBlog(Request $request, PartieblogRepository $partieblogRepository, Blog $blog, BlogRepository $br): Response {
        $partieblog = new Partieblog();
        $partieblog->setBlog($blog);
        $form = $this->createForm(PartieblogType::class, $partieblog);
        $form->handleRequest($request);
        $partieblogs = $partieblogRepository->findAll();
        $blogs = $br->findAll();

        if ($form->isSubmitted() && $form->isValid()) {
            $partieblogRepository->add($partieblog);
            return $this->redirectToRoute('app_partieblog_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/partieblog/new.html.twig', [
            'partieblog' => $partieblog,
            'form' => $form,
            'blog' => $blog,
            // 'partieblogs' => $partieblogs,
            'blogs' => $blogs
        ]);
    }

    /**
     * Liste des parties de blogs
     */

    #[Route('/parties_blog', name: 'app_partieblog_index', methods: ['GET'])]
    public function listePartiesBlogs(PartieblogRepository $partieblogRepository): Response {
        return $this->render('admin/partieblog/index.html.twig', [
            'partieblogs' => $partieblogRepository->findAll(),
        ]);
    }


    /**
     * Details d'une partie de blog
     */

    #[Route('/partie_blog/{id}', name: 'app_partieblog_show', methods: ['GET'])]
    public function showPartieBlog(Partieblog $partieblog): Response {
        return $this->render('admin/partieblog/show.html.twig', [
            'partieblog' => $partieblog,
        ]);
    }

    /**
     * Modifier une partie de blog
     */

    #[Route('/partie_blog/{id}/edit/', name: 'app_partieblog_edit', methods: ['GET', 'POST'])]
    public function editPartieBlog(Request $request, Partieblog $partieblog, PartieblogRepository $partieblogRepository): Response {
        $form = $this->createForm(PartieblogType::class, $partieblog);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $partieblogRepository->add($partieblog);
            return $this->redirectToRoute('app_partieblog_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/partieblog/edit.html.twig', [
            'partieblog' => $partieblog,
            'form' => $form,
        ]);
    }

    /**
     * Supprimer une partie de blog
     */

    #[Route('/{id}', name: 'app_partieblog_delete', methods: ['POST'])]
    public function deletePartieBlog(Request $request, Partieblog $partieblog, PartieblogRepository $partieblogRepository): Response {
        if ($this->isCsrfTokenValid('delete' . $partieblog->getId(), $request->request->get('_token'))) {
            $partieblogRepository->remove($partieblog);
        }

        return $this->redirectToRoute('app_partieblog_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * COMMANDES
     */

    #[Route('/commandes', name: 'app_commande_index', methods: ['GET'])]
    public function listeCommandes(CommandeRepository $commandeRepository): Response {
        return $this->render('admin/commande/index.html.twig', [
            'commandes' => $commandeRepository->findAll(),
        ]);
    }

    /*
    * Détails commande
    */

    #[Route('/commande/{id}', name: 'app_commande_show', methods: ['GET'])]
    public function showCommandes(Commande $commande): Response {
        return $this->render('admin/commande/show.html.twig', [
            'commande' => $commande,
        ]);
    }

    /*
    * Supprimer commande
    */

    #[Route('/{id}', name: 'app_commande_delete', methods: ['POST'])]
    public function deleteCommande(Request $request, Commande $commande, CommandeRepository $commandeRepository): Response {
        if ($this->isCsrfTokenValid('delete' . $commande->getId(), $request->request->get('_token'))) {
            $commandeRepository->remove($commande);
        }

        return $this->redirectToRoute('app_commande_index', [], Response::HTTP_SEE_OTHER);
    }
}

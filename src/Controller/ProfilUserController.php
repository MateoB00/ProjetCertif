<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Messages;
use App\Form\MessagesType;
use App\Form\UserFormType;
use App\Form\UpdateUserType;
use App\Form\UpdatebyUserType;
use App\Form\MessagescoachType;
use App\Form\ChangePasswordType;
use App\Repository\UserRepository;
use Symfony\Component\Form\FormError;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ProfilUserController extends AbstractController {
    #[Route('/profil', name: 'app_profil')]
    public function index(): Response {

        if (!$this->getUser()) {
            $this->addFlash('warning', 'En forme ? Soit prêt à tout casser !');

            return $this->redirectToRoute('app_login');
        }

        return $this->render('profil_user/profil.html.twig');
    }

    #[Route('/user/update/{id}', name: 'app_update_self_user')]
    public function update($id, UserRepository $ur, Request $request): Response {
        $user = $ur->find($id);
        $formulaire = $this->createForm(UpdatebyUserType::class, $user);
        $formulaire->handleRequest($request);


        if ($formulaire->isSubmitted() && $formulaire->isValid() && $this->isCsrfTokenValid('updateProfil', $request->request->get('_token'))) {

            $ur->add($user);


            $this->addFlash('success', 'Profil modifié !');

            return $this->redirectToRoute('app_profil', ['user' => $user->getId()]);
        } else {
            return $this->render('profil_user/update.html.twig', [
                'form' => $formulaire->createView(),
            ]);
        }
    }


    #[Route('/user/toncoach/{user}', name: 'app_ton_coach')]
    public function toncoach(User $user): Response {
        return $this->render('profil_user/toncoach.html.twig', [
            'coach' => $user
        ]);
    }







    #[Route('/user/update_mdp/{id}', name: 'app_update_self_mdp_user')]
    public function updatemdp(UserPasswordHasherInterface $passwordHasher, $id, UserRepository $ur, Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasher): Response {

        $user = $ur->find($id);
        $formulaire = $this->createForm(ChangePasswordType::class, $user);
        $formulaire->handleRequest($request);

        if ($formulaire->isSubmitted() && $formulaire->isValid() && $this->isCsrfTokenValid('updatePassword', $request->request->get('_token'))) {
            $oldPassword = $formulaire->get("oldPassword")->getData();
            $newPassword = $formulaire->get("plainPassword")->getData();


            //composer require symfony/password-hasher
            //https://lindevs.com/verify-that-password-matches-hash-for-given-user-in-symfony/


            //Si le mot de passe actuel de l'utilisateur est different de l'oldPassword et que le nouveau mot de passe est pareil que l'ancien alors ça passe sinon erreur
            if ($passwordHasher->isPasswordValid($user, $oldPassword) && !$passwordHasher->isPasswordValid($user, $newPassword)) {

                $user->setpassword($userPasswordHasher->hashPassword(
                    $user,
                    $formulaire->get('plainPassword')->getData()
                ));
                $entityManager->persist($user);
                $entityManager->flush();
            } else {
                dd($oldPassword, $newPassword); //erreur addFlash erreur
            }


            $this->addFlash('success', 'Mot de passe changé');

            return $this->redirectToRoute('app_profil', ['user' => $user->getId()]);
        } else {
            return $this->render('profil_user/update_password.html.twig', [
                'form' => $formulaire->createView(),
            ]);
        }
    }





    /**
     * @Route("/send", name="send")
     */
    public function send(Request $request, EntityManagerInterface $em): Response {
        $message = new Messages;
        $form = $this->createForm(MessagesType::class, $message);


        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && $this->isCsrfTokenValid('sendMessage', $request->request->get('_token'))) {
            $message->setExpediteur($this->getUser());

            $em->persist($message);
            $em->flush();

            $this->addFlash("message", "Message envoyé avec succès.");
            return $this->redirectToRoute('sent');
        }

        return $this->render("messages/send.html.twig", [
            "form" => $form->createView()
        ]);
    }
    /**
     * @Route("/send_coach/{user}", name="send_coach")
     */
    public function sendCoach(User $user, Request $request, EntityManagerInterface $em): Response {
        $message = new Messages;
        $form = $this->createForm(MessagescoachType::class, $message);


        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && $this->isCsrfTokenValid('sendMessageCoach', $request->request->get('_token'))) {
            $message->setDestinataire($user);
            $message->setExpediteur($this->getUser());

            $em->persist($message);
            $em->flush();

            $this->addFlash("message", "Message envoyé à ton coach avec succès.");
            return $this->redirectToRoute('sent');
        }

        return $this->render("messages/send_coach.html.twig", [
            "form" => $form->createView(),
            'coach' => $user
        ]);
    }
    /**
     * @Route("/send_answer/{user}", name="send_answer")
     */
    public function sendAnswer(User $user, Request $request, EntityManagerInterface $em): Response {
        $message = new Messages;
        $form = $this->createForm(MessagescoachType::class, $message);


        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && $this->isCsrfTokenValid('sendMessageAnswer', $request->request->get('_token'))) {
            $message->setDestinataire($user);
            $message->setExpediteur($this->getUser());

            $em->persist($message);
            $em->flush();

            $this->addFlash("message", "Message envoyé à ton coach avec succès.");
            return $this->redirectToRoute('sent');
        }

        return $this->render("messages/send_answer.html.twig", [
            "form" => $form->createView(),
            'coach' => $user
        ]);
    }

    /**
     * @Route("/received", name="received")
     */
    public function received(): Response {
        return $this->render('messages/received.html.twig');
    }


    /**
     * @Route("/sent", name="sent")
     */
    public function sent(): Response {
        return $this->render('messages/sent.html.twig');
    }

    /**
     * @Route("/read/{id}", name="read")
     */
    public function read(Messages $message, EntityManagerInterface $em): Response {

        $sender = $this->getUser();
        $expediteur = $message->getExpediteur();
        if ($sender != $expediteur) {
            $message->setIsRead(true);
        }

        $em->persist($message);
        $em->flush();

        return $this->render('messages/read.html.twig', compact("message"));
    }

    /**
     * @Route("/delete/message/{id}", name="delete_message")
     */
    public function deleteMessage(Messages $message, EntityManagerInterface $em, Request $request): Response {

        $user = $this->getUser();

        if ($this->isCsrfTokenValid('deleteMessage', $request->request->get('_token')) && [$user == $message->getExpediteur() || $user == $message->getExpediteur()]) {
            $em->remove($message);
            $em->flush();
        }

        return $this->redirectToRoute("received");
    }
}

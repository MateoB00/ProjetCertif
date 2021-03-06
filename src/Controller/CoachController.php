<?php

namespace App\Controller;

use DateTime;
use App\Entity\Messages;
use App\Repository\UserRepository;
use App\Repository\AbonnementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CoachController extends AbstractController {
    #[Route('/coach', name: 'app_options_coach')]
    public function index(AbonnementRepository $ar, UserRepository $ur): Response {
        $adherents = $ar->findBy([
            'coach' => $this->getUser(),
            'finAbonnement' => null
        ]);

        $abonnements = $ar->findBy([
            'debutAbonnement' => null
        ]);
        return $this->render('profil_coach/profil.html.twig', compact('adherents', 'abonnements'));
    }

    #[Route('/coach/adherent', name: 'app_form_coach')]
    public function formCoach(UserRepository $ur, Request $request, AbonnementRepository $ar, EntityManagerInterface $em): Response {

        $requestAdherentEnAttente = $request->request->get('idAdherentEnAttente');
        $requestAdherent = $request->request->get('idAdherent');





        if ($requestAdherentEnAttente && $this->isCsrfTokenValid('token', $request->request->get('_token'))) {
            $abonnement = $ar->findOneBy([
                'user' => $requestAdherentEnAttente,
                'debutAbonnement' => null
            ]);
            $user = $ur->findOneBy([
                'id' => $requestAdherentEnAttente
            ]);
            $coach = $this->getUser();
            $abonnement->setDebutAbonnement(new DateTime());
            $abonnement->setCoach($coach);
            $abonnement->setEncours(1);

            $user->setAttenteDunCoach(0);

            $message = new Messages;

            $message->setDestinataire($user);
            $message->setExpediteur($coach);
            $message->setTitre('Début d\'abonnement !');
            $message->setMessage('Bonjour, je suis ton nouveau coach, je me présente : ' . $coach->getNom() . ' ' . $coach->getPrenom() . '. N\'hésite pas à m\'envoyer ton numéro de téléphone. Les entraînements seront selon vos disponibilités.');



            $em->persist($abonnement);
            $em->flush();
            $em->persist($message);
            $em->flush();
            $em->persist($user);
            $em->flush();
        }
        if ($requestAdherent && $this->isCsrfTokenValid('token', $request->request->get('_token'))) {
            $abonnement = $ar->findOneBy([
                'user' => $requestAdherent,
                'finAbonnement' => null
            ]);
            $admin = $ur->findByRole('ROLE_ADMIN');
            $userAdherent = $ur->findOneBy([
                'id' => $requestAdherent
            ]);
            $abonnement->setEnCours(0);
            $abonnement->setFinAbonnement(new DateTime());

            $messageForAdherent = new Messages;

            $messageForAdherent->setDestinataire($userAdherent);
            $messageForAdherent->setExpediteur($admin[0]);
            $messageForAdherent->setTitre('Fin de l\'abonnement');
            $messageForAdherent->setMessage('Nous sommes arriver à la fin de ton abonnement n\'hésite pas à garder contact avec ton coach. A très vite et bonne continuation et n\'oublie pas que tu es maître de ton destin');



            $em->persist($abonnement);
            $em->flush();
            $em->persist($messageForAdherent);
            $em->flush();
        }

        return $this->redirectToRoute('app_options_coach');
    }

    #[Route('/coach/conditions', name: 'app_conditions_coach')]
    public function conditions(): Response {

        return $this->render('profil_coach/conditions.html.twig');
    }
}

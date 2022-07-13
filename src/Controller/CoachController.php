<?php

namespace App\Controller;

use DateTime;
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
            // dd($request->request->get('_token'));
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

            $em->persist($abonnement);
            $em->flush();
            $em->persist($user);
            $em->flush();
        }
        if ($requestAdherent && $this->isCsrfTokenValid('token', $request->request->get('_token'))) {
            $abonnement = $ar->findOneBy([
                'user' => $requestAdherent,
                'finAbonnement' => null
            ]);
            $abonnement->setEnCours(0);
            $abonnement->setFinAbonnement(new DateTime());

            $em->persist($abonnement);
            $em->flush();
        }

        return $this->redirectToRoute('app_options_coach');
    }
}

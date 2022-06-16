<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CoachController extends AbstractController {
    #[Route('/coach', name: 'app_options_coach')]
    public function index(UserRepository $ur): Response {
        $usersEnAttente = $ur->findby([
            'attente_dun_coach' => 1
        ]);
        $adherents = $ur->findBy([
            'toncoach' => $this->getUser()
        ]);
        return $this->render('profil_coach/profil.html.twig', compact('usersEnAttente', 'adherents'));
    }
    #[Route('/coach/adherent', name: 'app_form_coach')]
    public function formCoach(Request $request): Response {

        $requestAdherentEnAttente = $request->request->get('idAdherentEnAttente');
        $requestAdherent = $request->request->get('idAdherent');

        if ($requestAdherentEnAttente) {
        }
        if ($requestAdherent) {
        }
        dd($requestAdherent, $requestAdherentEnAttente);

        return $this->redirectToRoute('app_options_coach');
    }
}

<?php

namespace App\Controller;

use Exception;
use App\Form\DeviensCoachType;
use Symfony\Component\Mime\Email;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DeviensCoachController extends AbstractController
{
    #[Route('/deviens_coach', name: 'app_devienscoach')]
    public function index(Request $request, MailerInterface $mailer): Response
    {

        $formulaire = $this->createForm(DeviensCoachType::class);
        $formulaire->handleRequest($request);

        if ($formulaire->isSubmitted() && $formulaire->isValid()) {
            $data = $formulaire->getData();
            $cv = $formulaire->get('cv')->getData();



            if ($cv) {
                $newName = 'cv_' . uniqid() . '.' . $cv->guessExtension();
                $direction = $this->getParameter('imageDirectoryCV');

                try {
                    $cv->move(
                        $direction,
                        $newName
                    );
                } catch (Exception $e) {
                    $this->addFlash('errors', 'Problème dans l\'upload du CV');
                }
            }

            $cvdd = $direction . '/' . $newName;

            $email = new TemplatedEmail();
            $email->from($data['nom'] . ' ' . $data['prenom'] . ' <' . $data['email'] . '>')
                ->to('tpsymfony.91@gmail.com')
                ->replyTo($data['email'])
                ->subject($data['sujet'])
                ->htmlTemplate('emails_template/devienscoach.html.twig')
                ->context([
                    'fromEmail' => $data['email'],
                    'message' => nl2br($data['message']),
                    'tel' => ($data['telephone']),
                    'age' => ($data['age']),
                    'cv' => ($data['cv'])
                ])
                ->attachFromPath($cvdd, null, 'application/pdf');






            $mailer->send($email);


            $this->addFlash('success', 'Candidature envoyé. On vous recontactera par téléphone');

            return $this->redirectToRoute('app_devienscoach');
        }
        return $this->render('deviens_coach/devienscoach.html.twig', [
            'form' => $formulaire->createView(),

        ]);
    }
}

<?php

namespace App\Controller;

use App\Form\ContactType;
use Symfony\Component\Mime\Email;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'app_contact')]
    public function contact(Request $request, MailerInterface $mailer): Response
    {

        $formulaire = $this->createForm(ContactType::class);
        $formulaire->handleRequest($request);

        if ($formulaire->isSubmitted() && $formulaire->isValid()) {
            $data = $formulaire->getData();

            $email = new TemplatedEmail();
            $email->from($data['nom'] . ' ' . $data['prenom'] . ' <' . $data['email'] . '>')
                ->to('tpsymfony.91@gmail.com')
                ->replyTo($data['email'])
                ->subject($data['sujet'])
                ->htmlTemplate('emails_template/contact.html.twig')
                ->context([
                    'fromEmail' => $data['email'],
                    'message' => nl2br($data['message']),
                    'tel' => ($data['telephone'])
                ]);


            $mailer->send($email);


            $this->addFlash('success', 'Message envoyé.');

            return $this->redirectToRoute('app_contact');
        }
        return $this->render('contact/contact.html.twig', [

            'form' => $formulaire->createView(),
        ]);
    }
}

<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\Message;
use App\Entity\User;
use App\Form\MessageType;
use App\Repository\MessageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/app/conversations", name="app_conversations_")
 */
class ConversationController extends AbstractController
{
    /**
     * @return Response
     *
     * @Route("", name="index")
     */
    public function index(): Response
    {
        return $this->render('conversations.html.twig');
    }

    /**
     * @param User                   $receiver
     * @param Request                $request
     * @param EntityManagerInterface $entityManager
     * @param MessageRepository      $messageRepository
     *
     * @return Response
     *
     * @Route("/{receiver}/messages", name="messages")
     */
    public function messages(User $receiver, Request $request, EntityManagerInterface $entityManager, MessageRepository $messageRepository): Response
    {
        /** @var User $sender */
        $sender = $this->getUser();

        $form = $this->createForm(MessageType::class, $message = new Message());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $message->setSender($sender);
            $message->setReceiver($receiver);

            $entityManager->persist($message);
            $entityManager->flush();

            return $this->redirectToRoute('app_conversations_messages', ['receiver' => $receiver->getId()]);
        }

        return $this->render('messages.html.twig', [
            'form' => $form->createView(),
            'messages' => $messageRepository->findDiscussion($receiver, $sender)
        ]);
    }
}
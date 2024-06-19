<?php

namespace App\Controller;

use App\Entity\Conversation;
use App\Entity\Message;
use App\Entity\UserAccount;
use App\Form\ChatType;
use App\Repository\ConversationRepository;
use App\Repository\MessageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;

#[Route('/account')]
class ChatController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $manager,
        private ConversationRepository $conversationRepository,
        private MessageRepository $messageRepository
    ) {}

    #[Route('/chat', name: 'app_account_chat')]
    public function chat(Request $request, HubInterface $hub): Response
    {
        /* @var $user UserAccount */
        $user = $this->getUser();

        return $this->render('chat/index.html.twig', [
            'conversations' => $this->conversationRepository->findConversations($user),
        ]);
    }

    #[Route('/chat/new/{id}', name: 'app_account_chat_new', requirements: ['id' => Requirement::UUID_V4])]
    public function new(UserAccount $userTo): Response
    {
        /* @var $userFrom UserAccount */
        $userFrom = $this->getUser();
        $conversation = $this->conversationRepository->findConversationsWithUsers($userFrom, $userTo);
        if (!empty($conversation)) {
            return $this->redirectToRoute('app_account_chat_conversation', ['id' => $conversation[0]->getId()], Response::HTTP_SEE_OTHER);
        }

        $conversation = new Conversation();
        $conversation->setUserOne($userFrom)
            ->setUserTwo($userTo);

        $this->manager->persist($conversation);
        $this->manager->flush();

        return $this->redirectToRoute('app_account_chat_conversation', ['id' => $conversation->getId()], Response::HTTP_SEE_OTHER);
    }

    #[Route('/chat/{id}', name: 'app_account_chat_conversation', requirements: ['id' => Requirement::UUID_V4])]
    public function conversation(
        Conversation $conversation,
        Request $request,
        HubInterface $hub
    )
    {
        /* @var $user UserAccount */
        $user = $this->getUser();

        $userFrom = $user->getId() === $conversation->getUserOne()->getId() ? $conversation->getUserOne() : $conversation->getUserTwo();
        $userTo = $user->getId() === $conversation->getUserOne()->getId() ? $conversation->getUserTwo() : $conversation->getUserOne();

        $form = $this->createForm(ChatType::class);
        $emptyForm = clone $form;
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $message = new Message();
            $message->setConversation($conversation)
                ->setSender($userTo)
                ->setContent($data['content']);
            ;

            $this->manager->persist($message);
            $this->manager->flush();

            $hub->publish(new Update(
                'chat',
                $this->renderView('chat/message.stream.html.twig', [
                    'message' => $message,
                    'userTo' => $userTo,
                ]),
            ));

            // Force an empty form to be rendered below
            // It will replace the content of the Turbo Frame after a post
            $form = $emptyForm;
        }

        return $this->render('chat/chat.html.twig', [
            'conversations' => $this->conversationRepository->findConversations($user),
            'conversation' => $conversation,
            'userFrom' => $userFrom,
            'userTo' => $userTo,
            'form' => $form,
        ]);
    }
}

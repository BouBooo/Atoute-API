<?php 

namespace App\Queue\Handler;

use App\Entity\User;
use App\Service\MailerService;
use Doctrine\ORM\EntityManagerInterface;
use App\Queue\Message\UserCreatedMessage;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class UserCreatedHandler implements MessageHandlerInterface 
{
    private EntityManagerInterface $em;
    private MailerService $mailer;

    public function __construct(EntityManagerInterface $em, MailerService $mailer)
    {
        $this->em = $em;
        $this->mailer = $mailer;
    }

    public function __invoke(UserCreatedMessage $message)
    {
        $user = $this->em->find(User::class, $message->getUserId());

        $email = $this->mailer->buildEmail($user->getEmail(), $message->getTemplate(), [
            'id' => $user->getId(),
            'token' => $user->getConfirmationToken()
        ]);

        $this->mailer->send($email);
    }
}
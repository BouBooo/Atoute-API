<?php 

namespace App\Queue\Handler;

use App\Entity\User;
use App\Service\MailerService;
use Doctrine\ORM\EntityManagerInterface;
use App\Queue\Message\ResetPasswordMessage;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class ResetPasswordHandler implements MessageHandlerInterface 
{
    private EntityManagerInterface $em;
    private MailerService $mailer;

    public function __construct(EntityManagerInterface $em, MailerService $mailer)
    {
        $this->em = $em;
        $this->mailer = $mailer;
    }

    public function __invoke(ResetPasswordMessage $message)
    {
        $user = $this->em->find(User::class, $message->getUserId());

        $email = $this->mailer->buildEmail($user->getEmail(), $message->getTemplate(), [
            'id' => $user->getId(),
            'token' => $user->getResetPasswordToken()
        ]);

        $this->mailer->send($email);
    }
}
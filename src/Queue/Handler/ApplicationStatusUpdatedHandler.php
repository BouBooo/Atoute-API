<?php 

namespace App\Queue\Handler;

use App\Entity\User;
use App\Entity\Application;
use App\Service\MailerService;
use Doctrine\ORM\EntityManagerInterface;
use App\Queue\Message\UserCreatedMessage;
use App\Queue\Message\ApplicationCreatedMessage;
use App\Queue\Message\ApplicationStatusUpdatedMessage;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class ApplicationStatusUpdatedHandler implements MessageHandlerInterface 
{
    private EntityManagerInterface $em;
    private MailerService $mailer;

    public function __construct(EntityManagerInterface $em, MailerService $mailer)
    {
        $this->em = $em;
        $this->mailer = $mailer;
    }

    public function __invoke(ApplicationStatusUpdatedMessage $message)
    {
        $applicationOwner = $this->em->find(User::class, $message->getOwnerId());
        $application = $this->em->find(Application::class, $message->getApplicationId());
        $content = $message->getMessage();

        $email = $this->mailer->buildEmail($applicationOwner->getEmail(), $message->getTemplate(), [
            'offer' => $application->getOffer(),
            'companyName' => $application->getOffer()->getOwner()->getCompanyName(),
            'message' => $content,
            'isAccepted' => $application->getStatus() === Application::ACCEPTED
        ]);

        $this->mailer->send($email);
    }
}
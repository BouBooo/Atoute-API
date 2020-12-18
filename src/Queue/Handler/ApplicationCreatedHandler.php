<?php 

namespace App\Queue\Handler;

use App\Entity\User;
use App\Entity\Application;
use App\Service\MailerService;
use Doctrine\ORM\EntityManagerInterface;
use App\Queue\Message\ApplicationCreatedMessage;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class ApplicationCreatedHandler implements MessageHandlerInterface 
{
    private EntityManagerInterface $em;
    private MailerService $mailer;

    public function __construct(EntityManagerInterface $em, MailerService $mailer)
    {
        $this->em = $em;
        $this->mailer = $mailer;
    }

    public function __invoke(ApplicationCreatedMessage $message)
    {
        $user = $this->em->find(User::class, $message->getUserId());
        $application = $this->em->find(Application::class, $message->getApplicationId());

        $companyEmail = $this->mailer->buildEmail($user->getEmail(), $message->getTemplate(), [
            'offer' => $application->getOffer(),
            'application' => $application,
            'isParticular' => false
        ]);

        $this->mailer->send($companyEmail);

        $candidateEmail = $this->mailer->buildEmail($application->getCandidate()->getEmail(), $message->getTemplate(), [
            'offer' => $application->getOffer(),
            'application' => $application,
            'isParticular' => true
        ]);

        $this->mailer->send($candidateEmail);
    }
}
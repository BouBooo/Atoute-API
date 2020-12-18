<?php 

namespace App\Queue\Message;

class ApplicationStatusUpdatedMessage 
{
    private int $ownerId;
    private int $applicationId;
    private string $message;
    private string $template = 'applications/update_status.html.twig';

    public function __construct(int $ownerId, int $applicationId, string $message)
    {
        $this->ownerId = $ownerId;
        $this->applicationId = $applicationId;
        $this->message = $message;

    }

    public function getOwnerId(): int
    {
        return $this->ownerId;
    }

    public function getApplicationId(): int 
    {
        return $this->applicationId;
    }

    public function getMessage(): string 
    {
        return $this->message;
    }

    public function getTemplate(): string 
    {
        return $this->template; 
    }
}
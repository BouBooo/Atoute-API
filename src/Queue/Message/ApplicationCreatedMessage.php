<?php 

namespace App\Queue\Message;

class ApplicationCreatedMessage 
{
    private int $userId;
    private int $applicationId;
    private string $template = 'applications/create.html.twig';

    public function __construct(int $userId, int $applicationId)
    {
        $this->userId = $userId;
        $this->applicationId = $applicationId;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getApplicationId(): int 
    {
        return $this->applicationId;
    }

    public function getTemplate(): string 
    {
        return $this->template; 
    }
}
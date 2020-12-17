<?php 

namespace App\Queue\Message;

class UserCreatedMessage 
{
    private int $userId;
    private string $template = 'auth/confirm_account.html.twig';

    public function __construct(int $userId)
    {
        $this->userId = $userId;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getTemplate(): string 
    {
        return $this->template; 
    }
}
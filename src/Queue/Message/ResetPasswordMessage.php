<?php 

namespace App\Queue\Message;

class ResetPasswordMessage 
{
    private int $userId;
    private string $template = 'auth/reset_password.html.twig';

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
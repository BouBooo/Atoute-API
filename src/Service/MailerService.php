<?php

namespace App\Service;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

class MailerService
{
    private string $from;
    private MailerInterface $mailer;

    public function __construct(string $from, MailerInterface $mailer)
    {
        $this->from = $from;
        $this->mailer = $mailer;
    }

    public function buildEmail(string $to, string $template, array $data = []): TemplatedEmail
    {
        return (new TemplatedEmail())
            ->from($this->from)
            ->to($to)
            ->htmlTemplate($template)
            ->context($data);
    }

    public function send(TemplatedEmail $email): void
    {
        $this->mailer->send($email);
    }
}
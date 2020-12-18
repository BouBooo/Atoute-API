<?php 

namespace App\Repository;

use App\Entity\FailedJob;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Transport\Receiver\ListableReceiverInterface;

class FailedJobRepository 
{
    private ListableReceiverInterface $receiver;

    public function __construct(ListableReceiverInterface $receiver)
    {
        $this->receiver = $receiver;
    }

    public function findAll()
    {
        return array_map(fn (Envelope $envelope) => new FailedJob($envelope),
            iterator_to_array($this->receiver->all()));
    }
}
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

    public function find(string $id): FailedJob
    {
        return new FailedJob($this->receiver->find($id));
    }

    public function findAll(): array
    {
        return array_map(static fn (Envelope $envelope) => new FailedJob($envelope),
            iterator_to_array($this->receiver->all()));
    }

    public function reject(string $id): void
    {
        $this->receiver->reject($this->receiver->find($id));
    }
}
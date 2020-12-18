<?php 

namespace App\Entity;

use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Stamp\BusNameStamp;
use Symfony\Component\Messenger\Stamp\RedeliveryStamp;
use Symfony\Component\Messenger\Stamp\SentToFailureTransportStamp;
use Symfony\Component\Messenger\Bridge\Doctrine\Transport\DoctrineReceivedStamp;

class FailedJob 
{
    private Envelope $envelope;

    public function __construct(Envelope $envelope)
    {
        $this->envelope = $envelope;
    }

    public function getTrace($stamp): array
    {
        return $this->envelope->all($stamp);
    }

    public function getId(): int 
    {
        $stamps = $this->getTrace(DoctrineReceivedStamp::class);
        return array_shift($stamps)->getId();
    }

    public function getPath(): string
    {   
        return get_class($this->envelope->getMessage());
    }

    public function getJob(): string 
    {
        $path = explode('\\', $this->getPath());
        return array_pop($path);
    }

    public function getStackTrace(): string
    {
        $stamps = $this->getTrace(RedeliveryStamp::class);
        return array_shift($stamps)->getFlattenException()->getTraceAsString();
    }

    public function getDeliveryMethod(): string
    {
        $stamps = $this->getTrace(SentToFailureTransportStamp::class);
        return array_shift($stamps)->getOriginalReceiverName();
    }

    public function getBusName(): string
    {
        $stamps = $this->getTrace(BusNameStamp::class);
        return array_shift($stamps)->getBusName();
    }

    public function getFailedAt(): string
    {
        $stamps = $this->getTrace(RedeliveryStamp::class);
        return array_shift($stamps)->getRedeliveredAt()->format('H:i:s d-m-Y');
    }

    public function getRetryCount(): int
    {
        $stamps = $this->getTrace(RedeliveryStamp::class);
        return array_shift($stamps)->getRetryCount();
    }
}
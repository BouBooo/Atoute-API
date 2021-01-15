<?php 

namespace App\Service;

use DateTime;
use App\Entity\Offer;
use App\Entity\Resume;
use App\Repository\OfferRepository;
use Symfony\Contracts\Cache\CacheInterface;

class CacheService 
{
    private CacheInterface $cache;
    private OfferRepository $offerRepository;
    
    public function __construct(CacheInterface $cache, OfferRepository $offerRepository)
    {
        $this->cache = $cache;
        $this->offerRepository = $offerRepository;
    }

    // TODO: Create global function to load data from cache, not only relatedOffers
    public function loadRelatedOffers(string $key, Resume $resume, string $expiresAt = '+600 seconds')
    {
        $data = $this->cache->getItem($key);
        if (!$data->get()) {
            $date = new DateTime();
            $date->modify($expiresAt);
            $data->set($this->offerRepository->getRelatedOffer($resume, Offer::FILTERS))
                ->expiresAt($date);
            
            $this->cache->save($data);
        }    
        return $data;   
    }
}
<?php 

namespace App\Service;

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

    /**
     * @return mixed
     */
    public function get(string $key)
    {
        return $this->cache->getItem($key);
    }

    public function set(string $key, $data, string $expiresAt = '+600 seconds'): void
    {
        if (!$this->isCached($key)) {
            $item = $this->get($key);
            $date = (new \DateTime())->modify($expiresAt);
            $item->set($data)->expiresAt($date);

            $this->cache->save($item);
        }
    }

    public function delete(string $key): void
    {
        $this->cache->delete($key);
    }

    public function isCached(string $key): bool
    {
        $data = $this->get($key);
        return $data->isHit();
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
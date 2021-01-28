<?php

namespace App\Repository;

use App\Entity\Offer;
use App\Entity\Resume;
use App\Manager\ResumeManager;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Offer|null find($id, $lockMode = null, $lockVersion = null)
 * @method Offer|null findOneBy(array $criteria, array $orderBy = null)
 * @method Offer[]    findAll()
 * @method Offer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OfferRepository extends ServiceEntityRepository
{
    private ResumeManager $resumeManager;

    public function __construct(ManagerRegistry $registry, ResumeManager $resumeManager)
    {
        parent::__construct($registry, Offer::class);
        $this->resumeManager = $resumeManager;
    }

    public function hasAlreadyApplied(int $offerId, int $userId)
    {
        try {
            $qb = $this->createQueryBuilder('o')
                ->join('o.applications', 'a')
                ->leftJoin('a.candidate', 'c')
                ->where('o.id = :offerId')
                ->andWhere('c.id = :userId')
                ->setParameters([
                    'offerId' => $offerId,
                    'userId' => $userId
                ])
                ->getQuery()
            ;

            return $qb->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            return null;
        }
    }

    /**
     * Return Query
     */
    public function getPublishQuery(?int $limit = null, ?array $filters = null)
    {
        $qb = $this->createQueryBuilder('o')
            ->where('o.status = :status');

        foreach ($filters as $f => $v) {
            if (null !== $v) {
                if ($f === "salary") {
                    $qb->andWhere('o.'.$f.' >= :'.$f)
                        ->setParameter($f, $v);
                } elseif ($f === "startAt") {
                    $qb->andWhere('o.'.$f.' >= :'.$f)
                        ->setParameter($f, $v);
                } elseif ($f === "endAt") {
                    $qb->andWhere('o.'.$f.' <= :'.$f)
                        ->setParameter($f, $v);
                } else {
                    $qb->andWhere('o.'.$f.' = :'.$f)
                        ->setParameter($f, $v);
                }  
            }
        }

        return $qb->orderBy('o.publishedAt', 'DESC')
            ->setParameter('status', Offer::PUBLISHED)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * Filters can be managed from the Offer entity
     */
    public function getRelatedOffer(Resume $resume, array $filters)
    {
        $qb = $this->createQueryBuilder('o')
            ->orderBy('o.publishedAt', 'DESC')
            ->andWhere('o.status = :status')
            ->setParameter('status', Offer::PUBLISHED);

        if (in_array('type', $filters, true)) {
            $qb->andWhere('o.type = :type')
                ->setParameter('type', $resume->getContractType());
        }

        if (in_array('activity', $filters, true)) {
            $qb->andWhere('o.activity = :activity')
                ->setParameter('activity', $resume->getActivityArea());
        }

        if (in_array('words', $filters, true)) {
            $words = $this->resumeManager->extractKeywords($resume);
            if (!empty($words)) {
                foreach ($words as $key => $word) {
                    $sqlCondition[] = "o.description LIKE '%" . $word ."%'" . ($key !== count($words) - 1) ? " OR " : "";
                }

                $qb->andWhere(implode($sqlCondition));
            }
        }

        return $qb->getQuery()->getResult();
    }
}

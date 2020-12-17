<?php

namespace App\Repository;

use App\Entity\Offer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Offer|null find($id, $lockMode = null, $lockVersion = null)
 * @method Offer|null findOneBy(array $criteria, array $orderBy = null)
 * @method Offer[]    findAll()
 * @method Offer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OfferRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Offer::class);
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


        foreach($filters as $f => $v) {
            if (null !== $v) {
                $qb->andWhere('o.'.$f.' = :'.$f)
                ->setParameter($f, $v);
            }
        }
        // if (null !== $type) {
        //     $qb->andWhere('o.type = :type')
        //     ->setParameter('type', $type);
        // }
        // if (null !== $type) {
        //     $qb->andWhere('o.activity = :activity')
        //     ->setParameter('activity', $activity);
        // }

        $qb->orderBy('o.publishedAt', 'DESC')
        ->setParameter('status', Offer::PUBLISHED)
        ->setMaxResults($limit);

        return $qb->getQuery()
        ->getResult();
    }
}

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

    public function getPublish()
    {
        return $this->createQueryBuilder('o')
            ->where('o.status = :status')
            ->orderBy('o.publishedAt', 'DESC')
            ->setParameter('status', Offer::PUBLISHED)
            ->getQuery()
            ->getResult()
        ;
    }
}

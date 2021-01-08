<?php

namespace App\Repository;

use App\Entity\Offer;
use App\Entity\Resume;
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
                if($f === "salary"){
                    $qb->andWhere('o.'.$f.' >= :'.$f)
                    ->setParameter($f, $v);
                }elseif($f === "startAt"){
                    $qb->andWhere('o.'.$f.' >= :'.$f)
                    ->setParameter($f, $v);
                }elseif($f === "endAt"){
                    $qb->andWhere('o.'.$f.' <= :'.$f)
                    ->setParameter($f, $v);
                }
                else {
                    $qb->andWhere('o.'.$f.' = :'.$f)
                    ->setParameter($f, $v);
                }  
            }
        }
        
        $qb->orderBy('o.publishedAt', 'DESC')
        ->setParameter('status', Offer::PUBLISHED)
        ->setMaxResults($limit);

        return $qb->getQuery()
        ->getResult();
    }

    public function getRelatedOffer(Resume $resume)
    {
        return $this->createQueryBuilder('o')
        ->orderBy('o.publishedAt', 'DESC')
        ->andWhere('o.type = :type')
        ->orWhere('o.activity = :activity')
        ->andWhere('o.status = :status')
        ->setParameters([
            'type' => $resume->getContractType(),
            'activity' => $resume->getActivityArea(),
            'status' => Offer::PUBLISHED
        ])

        ->setMaxResults(1)
        ->getQuery()
        ->getOneOrNullResult();
    }
}
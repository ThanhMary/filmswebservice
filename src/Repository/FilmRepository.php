<?php

namespace App\Repository;

use App\Entity\Film;
use App\Entity\Search;
use Doctrine\DBAL\Query;
use Doctrine\ORM\ORMException;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Film|null find($id, $lockMode = null, $lockVersion = null)
 * @method Film|null findOneBy(array $criteria, array $orderBy = null)
 * @method Film[]    findAll()
 * @method Film[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FilmRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Film::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Film $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(Film $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }


    
   /**
     * @return filmsbyCategory[]
     */
    public function findByCategory($idCat)
    {
        $qb = $this->createQueryBuilder('b');

        $filmsbyCategory = $qb
                    ->join('b.category', 'c')
                    ->andWhere(':category_id = c.id')
                    ->setParameter('category_id', $idCat)
                    ->getQuery()
                    ->getResult()
        ;

        return $filmsbyCategory;
    }

    public function findByName($name)
    {
        $qb = $this->createQueryBuilder('f');

        $filmsbyCategory = $qb
                    ->where(':name = f.name')
                    ->setParameter(':name', $name)
                    ->getQuery()
                    ->getResult()
        ;

        return $filmsbyCategory;
    }
    
    // public function findOneBySomeField($value): ?Film
    // {
    //     return $this->createQueryBuilder('f')
    //         ->andWhere('f.exampleField = :val')
    //         ->setParameter('val', $value)
    //         ->getQuery()
    //         ->getOneOrNullResult()
    //     ;
    // }
    
}

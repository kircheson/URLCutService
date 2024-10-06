<?php

namespace App\Repository;

use App\Entity\Url;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Url|null find($id, $lockMode = null, $lockVersion = null)
 * @method Url|null findOneBy(array $criteria, array $orderBy = null)
 * @method Url[]    findAll()
 * @method Url[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UrlRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Url::class);
    }

    /**
     * @throws NonUniqueResultException
     */
    public function findOneByHash(string $value): ?Url
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.hash = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findUnsentUrls(): array
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.sent = :sent')
            ->setParameter('sent', false)
            ->getQuery()
            ->getResult();
    }

    // Метод для поиска уникальных URL за заданный промежуток времени
    public function findUniqueUrlsByDateRange(\DateTimeImmutable $startDate, \DateTimeImmutable $endDate)
    {
        return $this->createQueryBuilder('u')
            ->select('DISTINCT u.url') // Используем DISTINCT для уникальных значений
            ->andWhere('u.createdDate >= :start')
            ->andWhere('u.createdDate <= :end')
            ->setParameter('start', $startDate)
            ->setParameter('end', $endDate)
            ->getQuery()
            ->getScalarResult(); // Используем getScalarResult для получения массива строк
    }

    // Метод для поиска уникальных URL по домену
    public function findUniqueUrlsByDomain(string $domain)
    {
        return $this->createQueryBuilder('u')
            ->select('DISTINCT u.url') // Используем DISTINCT для уникальных значений
            ->andWhere('u.url LIKE :domain')
            ->setParameter('domain', '%' . $domain . '%')
            ->getQuery()
            ->getScalarResult(); // Используем getScalarResult для получения массива строк
    }
}

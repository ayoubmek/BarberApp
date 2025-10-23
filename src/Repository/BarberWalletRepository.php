<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\BarberWallet;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\Persistence\ManagerRegistry;

class BarberWalletRepository extends ServiceEntityRepository
{
    /** @var array<int, BarberWallet>  request-local cache: userId => wallet */
    private array $todayCache = [];

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BarberWallet::class);
    }

    /**
     * Return TODAY wallet for the barber.
     * Creates it only once per request.
     */
    public function getOrCreateToday(User $barber): BarberWallet
    {
        $userId = $barber->getId();
        if (isset($this->todayCache[$userId])) {
            return $this->todayCache[$userId];
        }

        $today = new \DateTimeImmutable('today');

        $wallet = $this->createQueryBuilder('w')
            ->andWhere('w.barber = :barber')
            ->andWhere('w.date   = :date')
            ->setParameter('barber', $barber)
            ->setParameter('date',   $today, Types::DATE_IMMUTABLE)
            ->getQuery()
            ->getOneOrNullResult();

        if ($wallet === null) {
            $wallet = new BarberWallet();
            $wallet->setBarber($barber);
            $wallet->setDate($today);
            $wallet->setBalance('0');
            $wallet->setTotalEarned('0');
            $this->getEntityManager()->persist($wallet);
        }

        $this->todayCache[$userId] = $wallet;
        return $wallet;
    }
}
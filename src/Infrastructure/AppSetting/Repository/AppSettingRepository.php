<?php

declare(strict_types = 1);

namespace App\Infrastructure\AppSetting\Repository;

use App\Application\AppSetting\Repository\AppSettingRepositoryInterface;
use App\Domain\Entity\AppSetting;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\EntityManagerInterface;

final class AppSettingRepository implements AppSettingRepositoryInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {
    }

    public function getByKey(string $appSettingKey): ?AppSetting
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();

        $queryBuilder->select('a')
            ->from(AppSetting::class, 'a')
            ->where('a.key = :key')
            ->setParameter('key', $appSettingKey, Types::STRING);
        
        return $queryBuilder->getQuery()->getOneOrNullResult();
    }
}
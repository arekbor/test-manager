<?php

declare(strict_types = 1);

namespace App\Infrastructure\AppSetting\Repository;

use App\Application\AppSetting\Repository\AppSettingRepositoryInterface;
use App\Domain\Entity\AppSetting;
use App\Infrastructure\Shared\AbstractRepository;

final class AppSettingRepository extends AbstractRepository implements AppSettingRepositoryInterface
{
    public function persistAppSetting(AppSetting $appSetting): void
    {
        $this->persist($appSetting);
    }

    public function getByKey(string $appSettingKey): ?AppSetting
    {
        $queryBuilder = $this->em->createQueryBuilder();

        $queryBuilder->select('a')
            ->from(AppSetting::class, 'a')
            ->where('a.key = :key')
            ->setParameter('key', $appSettingKey);
        
        return $queryBuilder->getQuery()->getOneOrNullResult();
    }
}
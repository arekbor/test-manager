<?php 

declare(strict_types=1);

namespace App\Repository;

use App\Domain\Entity\AppSetting;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class AppSettingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry) 
    {
        parent::__construct($registry, AppSetting::class);
    }

    public function findOneByKey(string $key): ?object
    {
        return $this->findOneBy(['key' => $key]);
    }
}
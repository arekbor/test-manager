<?php declare(strict_types=1);

namespace App\Repository;

use App\Entity\Video;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class VideoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Video::class);
    }

    public function findByModuleId(int $moduleId)
    {
        return $this
            ->createQueryBuilder('v')
            ->innerJoin('v.modules', 'm')
            ->where('m.id = :module_id')
            ->setParameter('module_id', $moduleId);
    }
}

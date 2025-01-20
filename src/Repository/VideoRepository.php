<?php 

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Video;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;

class VideoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Video::class);
    }

    public function findByModuleId(Uuid $moduleId): QueryBuilder
    {
        return $this
            ->createQueryBuilder('v')
            ->innerJoin('v.modules', 'm')
            ->where('m.id = :module_id')
            ->setParameter('module_id', $moduleId);
    }
}

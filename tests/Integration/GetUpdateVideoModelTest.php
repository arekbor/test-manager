<?php

declare(strict_types = 1);

namespace App\Tests\Integration;

use App\Application\Shared\QueryBusInterface;
use App\Application\Video\Query\GetUpdateVideoModel;
use App\Application\Video\Model\UpdateVideoModel;
use App\Domain\Entity\Video;
use App\Tests\DatabaseTestCase;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\Uid\Uuid;

final class GetUpdateVideoModelTest extends DatabaseTestCase
{
    use IntegrationTestTrait;

    private readonly QueryBusInterface $queryBus;

    protected function setUp(): void
    {
        parent::setUp();

        $this->queryBus = self::getContainer()->get(QueryBusInterface::class);
    }

    #[Test]
    #[Group(self::GROUP_NAME)]
    public function testGetUpdateVideoModelQueryReturnsUpdateVideoModelCorrectly(): void
    {   
        //Arrange
        $testVideo = new Video();
        $testVideo->setOriginalName('video.mp4');

        $this->entityManager->persist($testVideo);
        $this->entityManager->flush();
        
        $query = new GetUpdateVideoModel($testVideo->getId());

        //Act
        /**
         * @var UpdateVideoModel $updateVideoModel
         */
        $updateVideoModel = $this->queryBus->query($query);
        
        //Assert
        $this->assertInstanceOf(UpdateVideoModel::class, $updateVideoModel);

        $this->assertEquals('video.mp4', $updateVideoModel->getOriginalName());
    }

    #[Test]
    #[Group(self::GROUP_NAME)]
    public function testGetUpdateVideoModelQueryThrowsExceptionWhenVideoNoFound(): void
    {
        $notExistingVideoId = Uuid::v4();

        $query = new GetUpdateVideoModel($notExistingVideoId);

        $this->expectExceptionMessage(sprintf('App\Domain\Entity\Video {"id":"%s"}', $notExistingVideoId->toString()));

        $this->queryBus->query($query);
    }
}
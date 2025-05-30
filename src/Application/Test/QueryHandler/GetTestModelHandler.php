<?php

declare(strict_types = 1);

namespace App\Application\Test\QueryHandler;

use App\Application\Test\Model\TestModel;
use App\Application\Test\Query\GetTestModel;
use App\Domain\Entity\Test;
use App\Domain\Exception\NotFoundException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'message.bus')]
final class GetTestModelHandler
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {
    }

    public function __invoke(GetTestModel $query): TestModel
    {
        $testId = $query->getTestId();

        /**
         * @var Test $test
         */
        $test = $this->entityManager->find(Test::class, $testId);
        if ($test === null) {
            throw new NotFoundException(Test::class, ['id' => $testId]);
        }
        
        $testModel = new TestModel();
        $testModel->setExpiration($test->getExpiration());

        return $testModel;
    }
}
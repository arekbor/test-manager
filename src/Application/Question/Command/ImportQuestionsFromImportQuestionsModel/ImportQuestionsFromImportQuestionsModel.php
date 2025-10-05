<?php

declare(strict_types=1);

namespace App\Application\Question\Command\ImportQuestionsFromImportQuestionsModel;

use App\Application\Question\Model\ImportQuestionsModel;
use App\Application\Shared\Bus\CommandInterface;
use Symfony\Component\Uid\Uuid;

final class ImportQuestionsFromImportQuestionsModel implements CommandInterface
{
    public function __construct(
        private readonly Uuid $moduleId,
        private readonly ImportQuestionsModel $importQuestionsModel
    ) {}

    public function getModuleId(): Uuid
    {
        return $this->moduleId;
    }

    public function getImportQuestionsModel(): ImportQuestionsModel
    {
        return $this->importQuestionsModel;
    }
}

<?php

declare(strict_types=1);

namespace App\Application\Question\Command;

use App\Application\Question\Model\ImportQuestionsModel;
use Symfony\Component\Uid\Uuid;

final class ImportQuestionsFromImportQuestionsModel
{
    private Uuid $moduleId;
    private ImportQuestionsModel $importQuestionsModel;

    public function __construct(Uuid $moduleId, ImportQuestionsModel $importQuestionsModel)
    {
        $this->moduleId = $moduleId;
        $this->importQuestionsModel = $importQuestionsModel;
    }

    public function getModuleId(): Uuid
    {
        return $this->moduleId;
    }

    public function getImportQuestionsModel(): ImportQuestionsModel
    {
        return $this->importQuestionsModel;
    }
}

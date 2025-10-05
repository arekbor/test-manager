<?php

declare(strict_types=1);

namespace App\Application\Question\Query\GetImportQuestionsModel;

use App\Application\Answer\Model\AnswerModel;
use App\Application\Question\Model\ImportQuestionsModel;
use App\Application\Question\Model\QuestionModel;
use App\Application\Shared\Bus\QueryBusHandlerInterface;
use PhpOffice\PhpSpreadsheet\Reader\Csv;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Exception\ValidatorException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class GetImportQuestionsModelHandler implements QueryBusHandlerInterface
{
    public function __construct(
        private readonly ValidatorInterface $validator
    ) {}

    public function __invoke(GetImportQuestionsModel $query): ImportQuestionsModel
    {
        $csv = $query->getCsv();
        $errors = $this->validator->validate($csv, [
            new File(maxSize: '10M', extensions: ['csv' => [
                'text/plain',
                'text/csv',
                'application/csv',
                'text/x-comma-separated-values',
                'text/x-csv'
            ]])
        ]);

        if ($errors->count() > 0) {
            throw new ValidatorException($errors->get(0)->getMessage());
        }

        $importQuestionsModel = new ImportQuestionsModel();

        $reader = new Csv();
        $spreadsheet = $reader->load($csv->getPathname());
        $rows = $spreadsheet->getActiveSheet()->toArray();

        foreach ($rows as $row) {
            $questionContent = trim($row[0]);
            if ($questionContent === '') {
                continue;
            }

            $questionModel = new QuestionModel();
            $questionModel->setContent($questionContent);

            $answers = array_slice($row, 1);
            for ($i = 0; $i < count($answers); $i += 2) {
                $answerContent = trim($answers[$i]);

                $answerCorrectFlag = trim($answers[$i + 1]);
                if ($answerCorrectFlag === '' || !in_array($answerCorrectFlag, ['0', '1'])) {
                    continue;
                }

                $answerModel = new AnswerModel();
                $answerModel->setContent($answerContent);
                $answerModel->setCorrect($answerCorrectFlag === '1');

                $questionModel->addAnswerModel($answerModel);
            }

            $importQuestionsModel->addQuestionModel($questionModel);
        }

        return $importQuestionsModel;
    }
}

<?php

declare(strict_types = 1);

namespace App\Application\Question\Model;

use Symfony\Component\Validator\Constraints as Assert;
use App\Application\Answer\Model\AnswerModel;
use Symfony\Component\Uid\Uuid;

final class QuestionModel
{
    #[Assert\NotBlank()]
    private string $content;

    /**
     * @var AnswerModel[] $answerModels
     */
    #[Assert\Count(min: 1, max: 10)]
    #[Assert\Valid()]
    private array $answerModels;

    public function __construct() {
        $this->answerModels = [];
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @return AnswerModel[] $answerModels
     */
    public function getAnswerModels(): array
    {
        return $this->answerModels;
    }

    public function addAnswerModel(AnswerModel $answerModel): static
    {
        if (!in_array($answerModel, $this->answerModels, true)) {
            $this->answerModels[] = $answerModel;
        }

        return $this;
    }

    public function removeAnswerModel(AnswerModel $answerModel): static
    {
        $key = array_search($answerModel, $this->answerModels, true);
        if ($key !== false) {
            unset($this->answerModels[$key]);
            $this->answerModels = array_values($this->answerModels);
        }

        return $this;
    }

    public function getAnswerModelByAnswerId(?Uuid $answerId): ?AnswerModel
    {
        foreach ($this->answerModels as $answerModel) {
            if ($answerModel->getAnswerId() === $answerId) {
                return $answerModel;
            }
        }

        return null;
    }
}
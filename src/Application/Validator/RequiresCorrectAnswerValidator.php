<?php

declare(strict_types = 1);

namespace App\Application\Validator;

use App\Application\Answer\Model\AnswerModel;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Contracts\Translation\TranslatorInterface;

final class RequiresCorrectAnswerValidator extends ConstraintValidator
{
    public function __construct(
        private readonly TranslatorInterface $trans,
    ) {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!is_array($value)) {
            return;
        }

        $correctCount = 0;

        foreach($value as $answerModel) {
            if (!$answerModel instanceof AnswerModel) {
                continue;
            }

            if ($answerModel->isCorrect()) {
                $correctCount++;
            }
        }

        if ($correctCount < 1) {
            $this
                ->context
                ->buildViolation($this->trans->trans('validator.requiresCorrectAnswer.message'))
                ->addViolation()
            ;
        }
    }
}
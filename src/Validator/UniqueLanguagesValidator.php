<?php

namespace App\Validator;

use App\Model\TestLanguage;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Contracts\Translation\TranslatorInterface;

class UniqueLanguagesValidator extends ConstraintValidator
{
    public function __construct(
        private TranslatorInterface $trans
    ) {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!is_array($value)) {
            return;
        }

        $seenLanguages = [];

        foreach ($value as $testLanguage) {
            if ($testLanguage instanceof TestLanguage) {
                $language = $this->trans->trans($testLanguage->getLanguage());

                if (in_array($language, $seenLanguages, true)) {
                    $message = $this->trans->trans('validator.uniqueLanguages.message', [
                        '%language%' => $language
                    ]);

                    $this->context
                        ->buildViolation($message)
                        ->addViolation();
                        
                    return;
                }

                $seenLanguages[] = $language;
            }
        }
    }
}

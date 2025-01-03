<?php

namespace App\Validator;

use App\Model\TestMessageAppSetting;
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

        foreach ($value as $testMessage) {
            if ($testMessage instanceof TestMessageAppSetting) {
                $language = $testMessage->getLanguage();

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

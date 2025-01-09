<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Contracts\Translation\TranslatorInterface;

class UniqueValuesInArrayValidator extends ConstraintValidator
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

        $seenValues = [];

        foreach($value as $item) {
            $key = null;

            if (is_object($item) && method_exists($item, $constraint->key)) {
                $key = $item->{$constraint->key}();
            }

            if ($key === null) {
                continue;
            }

            if (in_array($key, $seenValues, true)) {
                $message = $this->trans->trans('validator.uniqueValuesInArray.message', [
                    '%key%' => $key
                ]);

                $this->context
                    ->buildViolation($message)
                    ->addViolation()
                ;

                return;
            }

            $seenValues[] = $key;
        }
    }
}

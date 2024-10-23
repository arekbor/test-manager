<?php declare(strict_types=1);

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Contracts\Translation\TranslatorInterface;

class ContainsAlphanumericValidator extends ConstraintValidator
{
    public function __construct(
        private TranslatorInterface $translator
    ) {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (empty($value)) {
            return;
        }

        if(preg_match('/^[a-zA-Z0-9ąćęłńóśźżĄĆĘŁŃÓŚŹŻ ]+$/', $value)) {
            return;
        }

        $this->context->addViolation(
            $this->translator->trans($constraint->message)
        );
    }
}

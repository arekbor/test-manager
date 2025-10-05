<?php

declare(strict_types=1);

namespace App\Application\SecurityUser\Query\GetLoginErrorMessage;

use App\Application\Shared\Bus\QueryBusHandlerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Contracts\Translation\TranslatorInterface;

final class GetLoginErrorMessageHandler implements QueryBusHandlerInterface
{
    public function __construct(
        private readonly AuthenticationUtils $authenticationUtils,
        private readonly TranslatorInterface $trans
    ) {}

    public function __invoke(GetLoginErrorMessage $query): ?string
    {
        $error = null;

        $lastAuthenticationError = $this->authenticationUtils->getLastAuthenticationError();
        if ($lastAuthenticationError !== null) {
            $error = $this->trans->trans(
                $lastAuthenticationError->getMessageKey(),
                $lastAuthenticationError->getMessageData(),
                'security'
            );
        }

        return $error;
    }
}

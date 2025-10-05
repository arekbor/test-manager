<?php

declare(strict_types=1);

namespace App\Presentation\Controller;

use App\Application\SecurityUser\Query\GetLoginErrorMessage\GetLoginErrorMessage;
use App\Application\Shared\Bus\QueryBusInterface;
use App\Presentation\Attribute\NotLogged;
use App\Presentation\Form\LoginType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/auth')]
final class AuthController extends AbstractController
{
    public function __construct(
        private readonly QueryBusInterface $queryBus,
        private readonly Security $security
    ) {}

    #[Route('/login', name: 'app_auth_login')]
    #[NotLogged]
    public function login(): Response
    {
        /**
         * @var string|null $lastAuthenticationErrorMessage
         */
        $lastAuthenticationErrorMessage = $this->queryBus->ask(new GetLoginErrorMessage());

        $form = $this->createForm(LoginType::class);

        return $this->render('auth/login.html.twig', [
            'form' => $form,
            'lastAuthenticationErrorMessage' => $lastAuthenticationErrorMessage,
        ]);
    }

    #[Route('/logout', name: 'app_auth_logout')]
    public function logout(): Response
    {
        return $this->security->logout();
    }
}

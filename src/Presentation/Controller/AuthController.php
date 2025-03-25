<?php 

declare(strict_types=1);

namespace App\Presentation\Controller;

use App\Domain\Entity\SecurityUser;
use App\Presentation\Attribute\NotLogged;
use App\Presentation\Form\LoginType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/auth')]
class AuthController extends AbstractController
{
    #[Route('/login', name: 'app_auth_login')]
    #[NotLogged]
    public function login(
        AuthenticationUtils $utils,
        TranslatorInterface $trans,
    ): Response
    {
        $lastAuthenticationErrorMessage = null;

        $error = $utils->getLastAuthenticationError();
        if ($error !== null) {
            $lastAuthenticationErrorMessage = $trans->trans($error->getMessageKey(), $error->getMessageData(), 'security');
        }

        $form = $this->createForm(LoginType::class, new SecurityUser());

        return $this->render('auth/login.html.twig', [
            'form' => $form,
            'lastAuthenticationErrorMessage' => $lastAuthenticationErrorMessage,
        ]);
    }

    #[Route('/logout', name: 'app_auth_logout')]
    public function logout(Security $security): Response
    {
        return $security->logout();
    }
}
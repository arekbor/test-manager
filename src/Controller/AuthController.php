<?php declare(strict_types=1);

namespace App\Controller;

use App\Entity\SecurityUser;
use App\Form\LoginType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

#[Route('/auth')]
class AuthController extends AbstractController
{
    #[Route('/login')]
    public function login(AuthenticationUtils $utils): Response
    {
        $error = $utils->getLastAuthenticationError();
        $form = $this->createForm(LoginType::class, new SecurityUser());

        return $this->render('auth/login.html.twig', ['form' => $form, 'error' => $error]);
    }
}
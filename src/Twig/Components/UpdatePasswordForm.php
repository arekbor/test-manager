<?php

declare(strict_types=1);

namespace App\Twig\Components;

use App\Entity\SecurityUser;
use App\Form\UpdatePasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
final class UpdatePasswordForm extends AbstractController
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;

    #[LiveAction]
    public function update(
        Security $security,
        UserPasswordHasherInterface $hasher,
        EntityManagerInterface $em,
    ): Response
    {
        $this->submitForm();

        $updatePassword = $this
            ->getForm()
            ->getData()
        ;

        /**
         * @var SecurityUser
         */
        $user = $this->getUser();
        
        if (!$hasher->isPasswordValid($user, $updatePassword->getCurrentPassword())) {
            throw new AccessDeniedException();
        }

        $hashedNewPassword = $hasher->hashPassword($user, $updatePassword->getPassword());
        $user->setPassword($hashedNewPassword);

        $em->persist($user);
        $em->flush();

        return $security->logout(false);
    }

    protected function instantiateForm(): FormInterface 
    {
        return $this->createForm(UpdatePasswordType::class);
    }
}

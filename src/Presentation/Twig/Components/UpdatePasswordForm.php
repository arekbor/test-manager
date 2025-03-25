<?php

declare(strict_types=1);

namespace App\Presentation\Twig\Components;

use App\Domain\Entity\SecurityUser;
use App\Presentation\Form\UpdatePasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
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
        TranslatorInterface $trans,
    ): Response
    {
        $this->submitForm();

        $updatePassword = $this->getForm()->getData();

        /**
         * @var SecurityUser
         */
        $user = $this->getUser();
        
        if (!$hasher->isPasswordValid($user, $updatePassword->getCurrentPassword())) {
            $this->addFlash('danger', $trans->trans('flash.updatePasswordForm.invalidPassword'));
            
            return $this->redirectToRoute('app_settings_general');
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

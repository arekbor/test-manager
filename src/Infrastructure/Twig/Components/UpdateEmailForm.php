<?php

declare(strict_types=1);

namespace App\Infrastructure\Twig\Components;

use App\Infrastructure\Form\UpdateEmailType;
use App\Repository\SecurityUserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
final class UpdateEmailForm extends AbstractController
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;

    #[LiveAction]
    public function update(
        Security $security,
        EntityManagerInterface $em,
        SecurityUserRepository $securityUserRepository,
        TranslatorInterface $trans,
    ): Response
    {
        $this->submitForm();
        
        $updateEmail = $this->getForm()->getData();

        $updatedEmail = $updateEmail->getEmail();

        $usersWithTheSameEmail = $securityUserRepository->findByEmail($updatedEmail);
        if (count($usersWithTheSameEmail) > 0) {
            $this->addFlash('danger', $trans->trans('flash.updateEmailForm.emailAlreadyExists'));

            return $this->redirectToRoute('app_settings_general');
        }

        /**
         * @var SecurityUser
         */
        $user = $this->getUser();
        
        $user->setEmail($updatedEmail);

        $em->persist($user);
        $em->flush();

        return $security->logout(false);
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(UpdateEmailType::class);
    }
}

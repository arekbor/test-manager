<?php

declare(strict_types=1);

namespace App\Twig\Components;

use App\Form\UpdateEmailType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;
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
    ): Response
    {
        $this->submitForm();
        $updateEmail = $this
            ->getForm()
            ->getData()
        ;

        /**
         * @var SecurityUser
         */
        $user = $this->getUser();
        $updatedEmail = $updateEmail->getEmail();
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

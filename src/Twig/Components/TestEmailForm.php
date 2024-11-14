<?php declare(strict_types=1);

namespace App\Twig\Components;

use App\Form\TestEmailType;
use App\Service\EmailService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\Attribute\LiveProp;

#[AsLiveComponent]
final class TestEmailForm extends AbstractController
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;

    #[LiveProp]
    public ?string $errorMessage = null;

    public function __construct(
        private EmailService $emailService
    ) {
    }

    #[LiveAction]
    public function send(): void
    {
        $this->submitForm();

        $testEmail = $this->getForm()->getData();
        $receiver = $testEmail->getReceiver();

        $this->errorMessage = $this
            ->emailService
            ->sendEmail($receiver, "Test Manager", "Test message")
        ;
    } 

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(TestEmailType::class);
    }
}
<?php 

declare(strict_types=1);

namespace App\Twig\Components;

use App\Exception\NotFoundException;
use App\Form\MailSmtpAppSettingType;
use App\Model\MailSmtpAppSetting;
use App\Repository\AppSettingRepository;
use App\Service\AppSettingService;
use App\Service\EncryptionService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\Attribute\LiveProp;

#[AsLiveComponent]
final class MailSmtpAppSettingForm extends AbstractController
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;

    public function __construct(
        private AppSettingService $appSettingService,
        private EncryptionService $encryptionService,
        private AppSettingRepository $appSettingRepository,
        private EntityManagerInterface $em,
    ) {
    }

    #[LiveProp]
    public MailSmtpAppSetting $mailSmtpAppSetting;

    #[LiveAction]
    public function submit(): Response
    {
        $this->submitForm();

        $mailSmtpAppSetting = $this
            ->getForm()
            ->getData()
        ;

        $plainPassword = $mailSmtpAppSetting->getPassword();
        $encryptedPassword = $this->encryptionService->encrypt($plainPassword);
        $mailSmtpAppSetting->setPassword($encryptedPassword);

        $appSetting = $this
            ->appSettingRepository
            ->findByKey(MailSmtpAppSetting::APP_SETTING_KEY)
        ;

        if ($appSetting === null) {
            throw new NotFoundException(MailSmtpAppSetting::class);
        }

        $appSetting = $this
            ->appSettingService
            ->updateValue($appSetting, $mailSmtpAppSetting)
        ;

        $this->em->persist($appSetting);
        $this->em->flush();

        return $this->redirectToRoute('app_settings_testmail');
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(MailSmtpAppSettingType::class, $this->mailSmtpAppSetting);
    }
}
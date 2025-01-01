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

    #[LiveProp]
    public MailSmtpAppSetting $mailSmtpAppSetting;

    #[LiveAction]
    public function submit(
        AppSettingService $appSettingService,
        EncryptionService $encryptionService,
        AppSettingRepository $appSettingRepository,
        EntityManagerInterface $em,
    ): Response
    {
        $this->submitForm();

        $mailSmtpAppSetting = $this
            ->getForm()
            ->getData()
        ;

        $plainPassword = $mailSmtpAppSetting->getPassword();
        $encryptedPassword = $encryptionService->encrypt($plainPassword);
        $mailSmtpAppSetting->setPassword($encryptedPassword);

        $appSetting = $appSettingRepository->findOneByKey(MailSmtpAppSetting::APP_SETTING_KEY);
        if ($appSetting === null) {
            throw new NotFoundException(MailSmtpAppSetting::class);
        }

        $appSetting = $appSettingService->updateValue($appSetting, $mailSmtpAppSetting);

        $em->persist($appSetting);
        $em->flush();

        return $this->redirectToRoute('app_settings_smtptest');
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(MailSmtpAppSettingType::class, $this->mailSmtpAppSetting);
    }
}
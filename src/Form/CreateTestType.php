<?php

namespace App\Form;

use App\Entity\Test;
use App\Exception\NotFoundException;
use App\Model\TestAppSetting;
use App\Repository\AppSettingRepository;
use App\Service\AppSettingService;
use DateTime;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreateTestType extends AbstractType
{
    public function __construct(
        private AppSettingRepository $appSettingRepository,
        private AppSettingService $appSettingService,
    ) {

    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('takerEmail', EmailType::class, [
                'label' => 'form.type.createTest.takerEmail.label',
                'help' => 'form.type.createTest.takerEmail.help',
            ])
            ->add('expiration', DateTimeType::class, [
                'widget' => 'single_text',
                'data' => $this->getDefaultExpirationDate(),
                'label' => 'form.type.createTest.expiration.label',
                'help' => 'form.type.createTest.expiration.help',
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'form.submit.label',
                'attr' => [
                    'class' => 'btn btn-primary',
                    'data-action' => 'live#action:prevent',
                    'data-live-action-param' => 'submit',
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Test::class,
        ]);
    } 

    private function getDefaultExpirationDate(): DateTime
    {
        $appSetting = $this->appSettingRepository->findOneByKey(TestAppSetting::APP_SETTING_KEY);
        if (!$appSetting) {
            throw new NotFoundException(TestAppSetting::class);
        }

        $testAppSetting = $this->appSettingService->getValue($appSetting, TestAppSetting::class);
        $daysOffset = $testAppSetting->getExpirationDaysOffset();

        return (new DateTime())->modify(sprintf('+%d days', $daysOffset));
    }
}

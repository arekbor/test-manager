<?php

declare(strict_types=1);

namespace App\Presentation\Form;

use App\Domain\Model\TestAppSetting;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\LiveComponent\Form\Type\LiveCollectionType;

class TestAppSettingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('expirationDaysOffset', NumberType::class, [
                'label' => 'form.type.testAppSetting.expirationDaysOffset.label',
                'help' => 'form.type.testAppSetting.expirationDaysOffset.help',
            ])
            ->add('notificationsEnabled', ChoiceType::class, [
                'label' => 'form.type.testAppSetting.notificationsEnabled.label',
                'help' => 'form.type.testAppSetting.notificationsEnabled.help',
                'choices' => [
                    'form.type.testAppSetting.notificationsEnabled.disable' => false,
                    'form.type.testAppSetting.notificationsEnabled.enable' => true
                ]
            ])
            ->add('testMessages', LiveCollectionType::class, [
                'label' => false,
                'entry_type' => TestMessageAppSettingType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'error_bubbling' => false,
                'button_add_options' => [
                    'label' => 'form.type.testAppSetting.button.add_message',
                    'attr' => [
                        'class' => 'btn btn-primary',
                        'data-live-action-param' => 'addCollectionItem',
                        'data-loading' => 'action(addCollectionItem)|addClass(loading)',
                    ]
                ],
                'button_delete_options' => [
                    'label' => 'form.type.testAppSetting.button.delete_message',
                    'attr' => [
                        'class' => 'btn btn-danger',
                        'data-live-action-param' => 'removeCollectionItem',
                        'data-loading' => 'action(removeCollectionItem)|addAttribute(disabled)'
                    ]
                ]
            ])
            ->add('testClauses', LiveCollectionType::class, [
                'label' => false,
                'entry_type' => TestClauseAppSettingType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'error_bubbling' => false,
                'button_add_options' => [
                    'label' => 'form.type.testAppSetting.button.add_clause',
                    'attr' => [
                        'class' => 'btn btn-primary',
                        'data-live-action-param' => 'addCollectionItem',
                        'data-loading' => 'action(addCollectionItem)|addClass(loading)'
                    ]
                ],
                'button_delete_options' => [
                    'label' => 'form.type.testAppSetting.button.delete_clause',
                    'attr' => [
                        'class' => 'btn btn-danger',
                        'data-live-action-param' => 'removeCollectionItem',
                        'data-loading' => 'action(removeCollectionItem)|addAttribute(disabled)'
                    ]
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'form.submit.label',
                'attr' => [
                    'class' => 'btn btn-success',
                    'data-action' => 'live#action:prevent',
                    'data-live-action-param' => 'submit',
                    'data-loading' => 'action(submit)|addClass(loading)'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => TestAppSetting::class
        ]);
    }
}

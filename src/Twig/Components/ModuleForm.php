<?php 

declare(strict_types=1);

namespace App\Twig\Components;

use App\Entity\Module;
use App\Form\ModuleType;
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
final class ModuleForm extends AbstractController
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;

    #[LiveProp]
    public ?Module $moduleProp = null;

    #[LiveAction]
    public function submit(EntityManagerInterface $em): Response
    {
        $this->submitForm();
        
        $module = $this
            ->getForm()
            ->getData()
        ;

        $em->persist($module);
        $em->flush();

        return $this->redirectToRoute('app_module_general', [
            'id' => $module->getId()
        ]);
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(ModuleType::class, $this->moduleProp);
    }
}

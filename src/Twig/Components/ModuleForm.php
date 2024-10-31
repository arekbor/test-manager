<?php declare(strict_types=1);

namespace App\Twig\Components;

use App\Entity\Module;
use App\Form\ModuleType;
use App\Repository\ModuleRepository;
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

    public function __construct(
        private ModuleRepository $moduleRepository
    ) {
    }

    #[LiveAction]
    public function submit(EntityManagerInterface $em): Response
    {
        $this->submitForm();
        $moduleForm = $this->getForm()->getData();

        $em->persist($moduleForm);
        $em->flush();

        return $this->redirectToRoute('app_module_details', [
            'id' => $moduleForm->getId()
        ]);
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(ModuleType::class, $this->moduleProp);
    }
}

<?php

declare(strict_types=1);

namespace App\Presentation\Twig\Components;

use App\Application\Question\Command\ImportQuestionsFromImportQuestionsModel;
use App\Application\Question\Model\ImportQuestionsModel;
use App\Application\Question\Query\GetImportQuestionsModel;
use App\Application\Shared\QueryBusInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Exception\ValidatorException;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\Attribute\LiveProp;

#[AsLiveComponent]
final class ImportQuestionsToModule extends AbstractController
{
    use DefaultActionTrait;

    public function __construct(
        private readonly QueryBusInterface $queryBus,
        private readonly MessageBusInterface $commandBus,
        private readonly TranslatorInterface $trans
    ) {}

    #[LiveProp(useSerializerForHydration: true)]
    public ?ImportQuestionsModel $importQuestionsModel = null;

    #[LiveProp]
    public ?string $error = null;

    #[LiveProp(useSerializerForHydration: true)]
    public Uuid $moduleId;

    private static $importQuestionsModelSessionKey = 'import_questions_model';

    #[LiveAction]
    public function uploadCsvFile(Request $request): void
    {
        $importQuestionsModel = null;
        $this->error = null;

        try {
            /**
             * @var \SplFileInfo $csvFile
             */
            $csvFile = $request->files->get('file');

            /**
             * @var ImportQuestionsModel $importQuestionsModel
             */
            $importQuestionsModel = $this->queryBus->query(new GetImportQuestionsModel($csvFile));
            $this->importQuestionsModel = $importQuestionsModel;

            $session = $request->getSession();
            $session->set(self::$importQuestionsModelSessionKey, serialize($this->importQuestionsModel));
        } catch (\Exception $ex) {
            $errorMessage = $this->trans->trans('flash.importQuestionsToModule.uploadCsvFile.error');

            if ($ex instanceof HandlerFailedException && $ex->getPrevious() instanceof ValidatorException) {
                $errorMessage = $ex->getPrevious()->getMessage();
            }

            $this->error = $errorMessage;
        }
    }

    #[LiveAction]
    public function import(Request $request): Response
    {
        $redirect = $this->redirectToRoute('app_module_questions', [
            'id' => $this->moduleId
        ]);

        try {
            $session = $request->getSession();
            $importQuestionsModelSerialized = $session->get(self::$importQuestionsModelSessionKey);

            /**
             * @var ImportQuestionsModel $importQuestionsModel
             */
            $importQuestionsModel = unserialize($importQuestionsModelSerialized);

            $command = new ImportQuestionsFromImportQuestionsModel($this->moduleId, $importQuestionsModel);

            $this->commandBus->dispatch($command);
        } catch (\Exception) {
            $this->addFlash('danger', $this->trans->trans('flash.importQuestionsToModule.import.error'));

            return $redirect;
        } finally {
            $session->remove(self::$importQuestionsModelSessionKey);
        }

        return $redirect;
    }
}

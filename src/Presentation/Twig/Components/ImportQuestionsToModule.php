<?php

declare(strict_types=1);

namespace App\Presentation\Twig\Components;

use Symfony\Component\Uid\Uuid;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use App\Application\Shared\Bus\QueryBusInterface;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use App\Application\Shared\Bus\CommandBusInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use App\Application\Question\Model\ImportQuestionsModel;
use Symfony\Component\Validator\Exception\ValidatorException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use App\Application\Question\Query\GetImportQuestionsModel\GetImportQuestionsModel;
use App\Application\Question\Command\ImportQuestionsFromImportQuestionsModel\ImportQuestionsFromImportQuestionsModel;

#[AsLiveComponent]
final class ImportQuestionsToModule extends AbstractController
{
    use DefaultActionTrait;

    public function __construct(
        private readonly QueryBusInterface $queryBus,
        private readonly CommandBusInterface $commandBus,
        private readonly TranslatorInterface $trans
    ) {}

    #[LiveProp(useSerializerForHydration: true)]
    public ?ImportQuestionsModel $importQuestionsModel = null;

    #[LiveProp]
    public ?string $error = null;

    #[LiveProp(useSerializerForHydration: true)]
    public Uuid $moduleId;

    private const IMPORT_QUESTIONS_MODEL_SESSION_KEY = 'import_questions_model';

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
            $importQuestionsModel = $this->queryBus->ask(new GetImportQuestionsModel($csvFile));
            $this->importQuestionsModel = $importQuestionsModel;

            $session = $request->getSession();
            $session->set(self::IMPORT_QUESTIONS_MODEL_SESSION_KEY, serialize($this->importQuestionsModel));
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
            $importQuestionsModelSerialized = $session->get(self::IMPORT_QUESTIONS_MODEL_SESSION_KEY);

            /**
             * @var ImportQuestionsModel $importQuestionsModel
             */
            $importQuestionsModel = unserialize($importQuestionsModelSerialized);

            $command = new ImportQuestionsFromImportQuestionsModel($this->moduleId, $importQuestionsModel);

            $this->commandBus->handle($command);
        } catch (\Exception) {
            $this->addFlash('danger', $this->trans->trans('flash.importQuestionsToModule.import.error'));

            return $redirect;
        } finally {
            $session->remove(self::IMPORT_QUESTIONS_MODEL_SESSION_KEY);
        }

        return $redirect;
    }
}

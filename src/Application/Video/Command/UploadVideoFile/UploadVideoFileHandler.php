<?php

declare(strict_types=1);

namespace App\Application\Video\Command\UploadVideoFile;

use App\Application\Shared\Bus\CommandBusHandlerInterface;
use App\Domain\Entity\Module;
use App\Domain\Entity\Video;
use App\Domain\Exception\NotFoundException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Exception\ValidatorException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class UploadVideoFileHandler implements CommandBusHandlerInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly ValidatorInterface $validator
    ) {}

    public function __invoke(UploadVideoFile $command): void
    {
        $moduleId = $command->getModuleId();

        /**
         * @var Module|null $module
         */
        $module = $this->entityManager->find(Module::class, $moduleId);
        if ($module === null) {
            throw new NotFoundException(Module::class, ['id' => $moduleId]);
        }

        $uploadedFile = $command->getUploadedFile();

        $errors = $this->validator->validate($uploadedFile, [
            new File(extensions: 'mp4')
        ]);

        if ($errors->count() > 0) {
            throw new ValidatorException($errors->get(0)->getMessage());
        }

        $video = new Video();
        $video->setFile($uploadedFile);
        $video->addModule($module);

        $this->entityManager->persist($video);
    }
}

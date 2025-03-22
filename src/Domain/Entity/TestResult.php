<?php

namespace App\Domain\Entity;

use App\Repository\TestResultRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: TestResultRepository::class)]
#[Vich\Uploadable]
class TestResult extends BaseEntity
{
    #[Vich\UploadableField(
        mapping: 'testResults', 
        fileNameProperty: 'fileName',
        size: 'size',
        mimeType: 'mimeType',
        originalName: 'originalName'
    )]
    private ?File $file = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $fileName = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $size = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $mimeType = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $originalName = null;

    #[ORM\OneToOne(mappedBy: 'testResult', cascade: ['persist', 'remove'])]
    private ?Test $test = null;

    public function getFile(): ?File
    {
        return $this->file;
    }

    public function setFile(?File $file = null): void
    {
        $this->file = $file;
    }

    public function getFileName(): ?string
    {
        return $this->fileName;
    }

    public function setFileName(?string $fileName): static
    {
        $this->fileName = $fileName;

        return $this;
    }

    public function getSize(): ?string
    {
        return $this->size;
    }

    public function setSize(?string $size): static
    {
        $this->size = $size;

        return $this;
    }

    public function getMimeType(): ?string
    {
        return $this->mimeType;
    }

    public function setMimeType(?string $mimeType): static
    {
        $this->mimeType = $mimeType;

        return $this;
    }

    public function getOriginalName(): ?string
    {
        return $this->originalName;
    }

    public function setOriginalName(?string $originalName): static
    {
        $this->originalName = $originalName;

        return $this;
    }

    public function getTest(): ?Test
    {
        return $this->test;
    }

    public function setTest(?Test $test): static
    {
        // unset the owning side of the relation if necessary
        if ($test === null && $this->test !== null) {
            $this->test->setTestResult(null);
        }

        // set the owning side of the relation if necessary
        if ($test !== null && $test->getTestResult() !== $this) {
            $test->setTestResult($this);
        }

        $this->test = $test;

        return $this;
    }
}

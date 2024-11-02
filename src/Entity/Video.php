<?php declare(strict_types=1);

namespace App\Entity;

use App\Repository\VideoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: VideoRepository::class)]
class Video extends BaseEntity
{   
    #[Assert\File(['extensions' => ['mp4', 'mov']])]
    private ?UploadedFile $file = null;

    #[ORM\Column(length: 255)]
    private ?string $filename = null;

    /**
     * @var Collection<int, Module>
     */
    #[ORM\ManyToMany(targetEntity: Module::class, inversedBy: 'videos')]
    private Collection $modules;

    public function __construct()
    {
        $this->modules = new ArrayCollection();
    }
    
    public function getFilename(): ?string
    {
        return $this->filename;
    }

    public function getFile(): ?UploadedFile
    {
        return $this->file;
    }

    public function setFile(UploadedFile $file): static
    {
        $this->file = $file;

        $fileExtension = $this->file->guessExtension();
        if (empty($fileExtension)) {
            throw new Exception("Could not guess extension file.");
        }

        $this->filename = Uuid::v7()->toString() . '.' . $fileExtension;

        return $this;
    }

    /**
     * @return Collection<int, Module>
     */
    public function getModules(): Collection
    {
        return $this->modules;
    }

    public function addModule(Module $module): static
    {
        if (!$this->modules->contains($module)) {
            $this->modules->add($module);
        }

        return $this;
    }

    public function removeModule(Module $module): static
    {
        $this->modules->removeElement($module);

        return $this;
    }
}

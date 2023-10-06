<?php

namespace App\Entity\Calendar;

use App\Repository\Calendar\ModelBoxRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ModelBoxRepository::class)]
#[ORM\HasLifecycleCallbacks]
class ModelBox
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $path = null;

    #[ORM\Column(length: 30)]
    private ?string $type = null;

    #[ORM\Column]
    private ?int $coordX = null;

    #[ORM\Column]
    private ?int $coordY = null;

    #[ORM\ManyToOne(inversedBy: 'modelBoxes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?ModelCalendar $modelCalendar = null;

    #[ORM\OneToMany(mappedBy: 'modelBox', targetEntity: Box::class)]
    private Collection $boxes;

    #[ORM\Column]
    private ?int $position = null;

    #[ORM\PreRemove]
    public function removeFile(): void
    {
        $filePath = $this->getPath();

        if (file_exists($filePath)) {
            // Supprimez le fichier
            unlink($filePath);
        }
    }

    public function __construct()
    {
        $this->boxes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getCoordX(): ?int
    {
        return $this->coordX;
    }

    public function setCoordX(int $coordX): self
    {
        $this->coordX = $coordX;

        return $this;
    }

    public function getCoordY(): ?int
    {
        return $this->coordY;
    }

    public function setCoordY(int $coordY): self
    {
        $this->coordY = $coordY;

        return $this;
    }

    public function getModelCalendar(): ?ModelCalendar
    {
        return $this->modelCalendar;
    }

    public function setModelCalendar(?ModelCalendar $modelCalendar): self
    {
        $this->modelCalendar = $modelCalendar;

        return $this;
    }

    /**
     * @return Collection<int, Box>
     */
    public function getBoxes(): Collection
    {
        return $this->boxes;
    }

    public function addBox(Box $box): self
    {
        if (!$this->boxes->contains($box)) {
            $this->boxes->add($box);
            $box->setModelBox($this);
        }

        return $this;
    }

    public function removeBox(Box $box): self
    {
        if ($this->boxes->removeElement($box)) {
            // set the owning side to null (unless already changed)
            if ($box->getModelBox() === $this) {
                $box->setModelBox(null);
            }
        }

        return $this;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(int $position): self
    {
        $this->position = $position;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }
}

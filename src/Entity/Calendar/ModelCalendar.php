<?php

namespace App\Entity\Calendar;

use App\Entity\Auth\User;
use Symfony\Component\Uid\Uuid;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Doctrine\Common\Collections\ArrayCollection;
use App\Repository\Calendar\ModelCalendarRepository;

#[ORM\Entity(repositoryClass: ModelCalendarRepository::class)]
class ModelCalendar
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;


    #[ORM\Column(length: 255, nullable: true)]
    private ?string $path = null;

    #[ORM\Column(type: UuidType::NAME)]   
    private Uuid $uuid;

    #[ORM\ManyToOne(inversedBy: 'modelCalendars')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\OneToMany(mappedBy: 'modelCalendar', targetEntity: ModelBox::class,cascade: ["persist"], orphanRemoval: true)]
    private Collection $modelBoxes;

    #[ORM\OneToMany(mappedBy: 'modelCalendar', targetEntity: Calendar::class, orphanRemoval: true)]
    private Collection $calendars;

    #[ORM\Column]
    private ?bool $isPublished = false;

    #[ORM\Column(length: 10)]
    private ?string $color = null;

    public function __construct()
    {
        $this->modelBoxes = new ArrayCollection();
        $this->calendars = new ArrayCollection();

        $uuid = Uuid::v4();
        $this->uuid = $uuid;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(?string $path): self
    {
        $this->path = $path;

        return $this;
    }

    public function getUuid(): Uuid
    {
        return $this->uuid;
    }

    public function setUuid(Uuid $uuid): self
    {
        $this->uuid = $uuid;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection<int, ModelBox>
     */
    public function getModelBoxes(): Collection
    {
        return $this->modelBoxes;
    }

    public function addModelBox(ModelBox $modelBox): self
    {
        if (!$this->modelBoxes->contains($modelBox)) {
            $this->modelBoxes->add($modelBox);
            $modelBox->setModelCalendar($this);
        }

        return $this;
    }

    public function removeModelBox(ModelBox $modelBox): self
    {
        if ($this->modelBoxes->removeElement($modelBox)) {
            // set the owning side to null (unless already changed)
            if ($modelBox->getModelCalendar() === $this) {
                $modelBox->setModelCalendar(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Calendar>
     */
    public function getCalendars(): Collection
    {
        return $this->calendars;
    }

    public function addCalendar(Calendar $calendar): self
    {
        if (!$this->calendars->contains($calendar)) {
            $this->calendars->add($calendar);
            $calendar->setModelCalendar($this);
        }

        return $this;
    }

    public function removeCalendar(Calendar $calendar): self
    {
        if ($this->calendars->removeElement($calendar)) {
            // set the owning side to null (unless already changed)
            if ($calendar->getModelCalendar() === $this) {
                $calendar->setModelCalendar(null);
            }
        }

        return $this;
    }

    public function isPublished(): ?bool
    {
        return $this->isPublished;
    }

    public function setIsPublished(bool $isPublished): self
    {
        $this->isPublished = $isPublished;

        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(string $color): self
    {
        $this->color = $color;

        return $this;
    }
}

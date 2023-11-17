<?php

namespace App\Entity\Calendar;

use Symfony\Component\Uid\Uuid;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\Calendar\BoxRepository;


#[ORM\Entity(repositoryClass: BoxRepository::class)]
class Box
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?bool $isOpen = false;

    #[ORM\ManyToOne(inversedBy: 'boxes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Calendar $calendar = null;

    #[ORM\ManyToOne(inversedBy: 'boxes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?ModelBox $modelBox = null;

    #[ORM\Column(length: 255)]
    private ?string $uuid = null;

    public function __construct() {
        $this->uuid = Uuid::v4();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function isOpen(): ?bool
    {
        return $this->isOpen;
    }

    public function setIsOpen(bool $isOpen): self
    {
        $this->isOpen = $isOpen;

        return $this;
    }

    public function getCalendar(): ?Calendar
    {
        return $this->calendar;
    }

    public function setCalendar(?Calendar $calendar): self
    {
        $this->calendar = $calendar;

        return $this;
    }

    public function getModelBox(): ?ModelBox
    {
        return $this->modelBox;
    }

    public function setModelBox(?ModelBox $modelBox): self
    {
        $this->modelBox = $modelBox;

        return $this;
    }

    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    public function setUuid(string $uuid): self
    {
        $this->uuid = $uuid;

        return $this;
    }
}

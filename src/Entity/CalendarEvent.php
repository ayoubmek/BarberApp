<?php
// src/Entity/CalendarEvent.php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: "App\Repository\CalendarEventRepository")]
#[ORM\Table(name: "calendar_event")]
class CalendarEvent
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\Column(type: "string", length: 255)]
    #[Assert\NotBlank]
    private ?string $title = null;

    #[ORM\Column(type: "text", nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $location = null;

    #[ORM\Column(type: "datetime")]
    #[Assert\NotBlank]
    private ?\DateTimeInterface $start = null;

    #[ORM\Column(type: "datetime")]
    #[Assert\NotBlank]
    private ?\DateTimeInterface $end = null;

    #[ORM\Column(type: "boolean")]
    private bool $allDay = false;

    // Getters and Setters
    public function getId(): ?int { return $this->id; }
    public function getTitle(): ?string { return $this->title; }
    public function setTitle(string $title): self { $this->title = $title; return $this; }
    public function getDescription(): ?string { return $this->description; }
    public function setDescription(?string $description): self { $this->description = $description; return $this; }
    public function getLocation(): ?string { return $this->location; }
    public function setLocation(?string $location): self { $this->location = $location; return $this; }
    public function getStart(): ?\DateTimeInterface { return $this->start; }
    public function setStart(\DateTimeInterface $start): self { $this->start = $start; return $this; }
    public function getEnd(): ?\DateTimeInterface { return $this->end; }
    public function setEnd(\DateTimeInterface $end): self { $this->end = $end; return $this; }
    public function isAllDay(): bool { return $this->allDay; }
    public function setAllDay(bool $allDay): self { $this->allDay = $allDay; return $this; }
}

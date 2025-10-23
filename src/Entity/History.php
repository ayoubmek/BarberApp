<?php

namespace App\Entity;

use App\Repository\HistoryRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HistoryRepository::class)]
class History
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $date;

    #[ORM\Column(type: 'text')]
    private string $contenu;

    #[ORM\Column(type: 'string', length: 30)]
    private string $type = 'default';

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'histories')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?\App\Entity\User $user = null;

    public function __construct()
    {
        $this->date = new \DateTimeImmutable();
    }

    /* ----- getters & setters ----- */
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): \DateTimeImmutable
    {
        return $this->date;
    }

    public function setDate(\DateTimeImmutable $date): self
    {
        $this->date = $date;
        return $this;
    }

    public function getContenu(): string
    {
        return $this->contenu;
    }

    public function setContenu(string $contenu): self
    {
        $this->contenu = $contenu;
        return $this;
    }

    public function getUser(): ?\App\Entity\User
    {
        return $this->user;
    }

    public function setUser(?\App\Entity\User $user): self
    {
        $this->user = $user;
        return $this;
    }
    public function getType(): string
{
    return $this->type;
}

public function setType(string $type): self
{
    $this->type = $type;
    return $this;
}
}
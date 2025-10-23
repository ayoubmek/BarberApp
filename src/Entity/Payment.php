<?php

namespace App\Entity;
use App\Entity\User; 
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: "App\Repository\PaymentRepository")]
class Payment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'payments')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?User $user = null;

    #[ORM\ManyToOne(targetEntity: Invoice::class, inversedBy: 'payments')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'CASCADE')]
    private $invoice;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private $amount;

    #[ORM\Column(type: 'datetime_immutable')]
    private $paymentDate;

    #[ORM\Column(type: 'string', length: 10)]
    private $paymentType;

    #[ORM\ManyToOne(targetEntity: BarberWallet::class, inversedBy: 'payments')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?BarberWallet $wallet = null;

    public function __construct()
    {
        $this->paymentDate = new \DateTimeImmutable();
    }

    public function getWallet(): ?BarberWallet { return $this->wallet; }
    public function setWallet(?BarberWallet $wallet): self { $this->wallet = $wallet; return $this;}
    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;
        return $this;
    }
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getInvoice(): ?Invoice
    {
        return $this->invoice;
    }

    public function setInvoice(?Invoice $invoice): self
    {
        $this->invoice = $invoice;
        return $this;
    }

    public function getAmount(): ?string
    {
        return $this->amount;
    }

    public function setAmount(string $amount): self
    {
        $this->amount = $amount;
        return $this;
    }

    public function getPaymentDate(): ?\DateTimeImmutable
    {
        return $this->paymentDate;
    }

    public function setPaymentDate(\DateTimeImmutable $paymentDate): self
    {
        $this->paymentDate = $paymentDate;
        return $this;
    }

    public function getPaymentType(): ?string
    {
        return $this->paymentType;
    }

    public function setPaymentType(string $paymentType): self
    {
        $this->paymentType = $paymentType;
        return $this;
    }
}
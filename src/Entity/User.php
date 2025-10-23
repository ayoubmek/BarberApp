<?php

namespace App\Entity;

use App\Entity\Payment;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: "App\Repository\UserRepository")]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 100)]
    private $name;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $image = null;

    #[ORM\Column(type: 'string', length: 150, unique: true)]
    private $email;

    #[ORM\Column(type: 'string', length: 255)]
    private $password;

    #[ORM\Column(type: 'string', length: 20, nullable: true)]
    private $phone;

    #[ORM\Column(type: 'string', length: 10)]
    private $role = 'barber';

    #[ORM\Column(type: 'datetime_immutable')]
    private $createdAt;

    #[ORM\Column(type: 'datetime_immutable')]
    private $updatedAt;

    /* ---------------- existing relations ---------------- */

    #[ORM\OneToMany(mappedBy: 'barber', targetEntity: InvoiceItem::class)]
    private $invoiceItems;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: History::class)]
    private $histories;

    /** @var Collection<int, BarberWallet> */
    #[ORM\OneToMany(mappedBy: 'barber', targetEntity: BarberWallet::class)]
    private Collection $wallets;

        public function getWallets(): Collection               { return $this->wallets; }

    public function setWallet(?BarberWallet $wallet): self
    {
        // optional: update the owning side
        if ($wallet !== null && $wallet->getBarber() !== $this) {
            $wallet->setBarber($this);
        }
        $this->wallet = $wallet;

        return $this;
    }

    /* ---------------- NEW relation ---------------- */

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Payment::class, orphanRemoval: true)]
    private Collection $payments;

    public function __construct()
    {
        $this->invoiceItems = new ArrayCollection();
        $this->histories    = new ArrayCollection();
        $this->payments     = new ArrayCollection();
        $this->wallets = new ArrayCollection();
        $this->createdAt    = new \DateTimeImmutable();
        $this->updatedAt    = new \DateTimeImmutable();
    }

    /* =========================================================
       AUTO-GENERATED GETTERS / SETTERS FOR BASIC FIELDS
    ========================================================= */

    public function getId(): ?int
    {
        return $this->id;
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

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;
        return $this;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(string $role): self
    {
        $this->role = $role;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    /* =========================================================
       INVOICEITEMS RELATION
    ========================================================= */

    /**
     * @return Collection|InvoiceItem[]
     */
    public function getInvoiceItems(): Collection
    {
        return $this->invoiceItems;
    }

    public function addInvoiceItem(InvoiceItem $invoiceItem): self
    {
        if (!$this->invoiceItems->contains($invoiceItem)) {
            $this->invoiceItems[] = $invoiceItem;
            $invoiceItem->setBarber($this);
        }
        return $this;
    }

    public function removeInvoiceItem(InvoiceItem $invoiceItem): self
    {
        if ($this->invoiceItems->removeElement($invoiceItem)) {
            if ($invoiceItem->getBarber() === $this) {
                $invoiceItem->setBarber(null);
            }
        }
        return $this;
    }

    /* =========================================================
       HISTORIES RELATION
    ========================================================= */

    /**
     * @return Collection|History[]
     */
    public function getHistories(): Collection
    {
        return $this->histories;
    }

    public function addHistory(History $history): self
    {
        if (!$this->histories->contains($history)) {
            $this->histories[] = $history;
            $history->setUser($this);
        }
        return $this;
    }

    public function removeHistory(History $history): self
    {
        if ($this->histories->removeElement($history)) {
            if ($history->getUser() === $this) {
                $history->setUser(null);
            }
        }
        return $this;
    }

    /* =========================================================
       PAYMENTS RELATION   (newly added)
    ========================================================= */

    /**
     * @return Collection|Payment[]
     */
    public function getPayments(): Collection
    {
        return $this->payments;
    }

    public function addPayment(Payment $payment): self
    {
        if (!$this->payments->contains($payment)) {
            $this->payments[] = $payment;
            $payment->setUser($this);
        }
        return $this;
    }

    public function removePayment(Payment $payment): self
    {
        if ($this->payments->removeElement($payment)) {
            // set the owning side to null (unless already changed)
            if ($payment->getUser() === $this) {
                $payment->setUser(null);
            }
        }
        return $this;
    }
}
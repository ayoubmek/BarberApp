<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: "App\Repository\InvoiceRepository")]
class Invoice
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 100)]
    private $clientName;

    #[ORM\Column(type: 'string', length: 20, nullable: true)]
    private $clientPhone;

    #[ORM\Column(type: 'datetime_immutable')]
    private $date;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private $totalAmount;

    #[ORM\Column(type: 'string', length: 10)]
    private $paymentMethod;

    #[ORM\OneToMany(mappedBy: 'invoice', targetEntity: InvoiceItem::class, orphanRemoval: true)]
    private $items;

    #[ORM\OneToMany(mappedBy: 'invoice', targetEntity: Payment::class, orphanRemoval: true)]
    private $payments;

    public function __construct()
    {
        $this->date = new \DateTimeImmutable();
        $this->items = new ArrayCollection();
        $this->payments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getClientName(): ?string
    {
        return $this->clientName;
    }

    public function setClientName(string $clientName): self
    {
        $this->clientName = $clientName;
        return $this;
    }

    public function getClientPhone(): ?string
    {
        return $this->clientPhone;
    }

    public function setClientPhone(?string $clientPhone): self
    {
        $this->clientPhone = $clientPhone;
        return $this;
    }

    public function getDate(): ?\DateTimeImmutable
    {
        return $this->date;
    }

    public function setDate(\DateTimeImmutable $date): self
    {
        $this->date = $date;
        return $this;
    }

    public function getTotalAmount(): ?string
    {
        return $this->totalAmount;
    }

    public function setTotalAmount(string $totalAmount): self
    {
        $this->totalAmount = $totalAmount;
        return $this;
    }

    public function getPaymentMethod(): ?string
    {
        return $this->paymentMethod;
    }

    public function setPaymentMethod(string $paymentMethod): self
    {
        $this->paymentMethod = $paymentMethod;
        return $this;
    }

    /**
     * @return Collection|InvoiceItem[]
     */
    public function getItems(): Collection
    {
        return $this->items;
    }

    public function addItem(InvoiceItem $item): self
    {
        if (!$this->items->contains($item)) {
            $this->items[] = $item;
            $item->setInvoice($this);
        }
        return $this;
    }

    public function removeItem(InvoiceItem $item): self
    {
        if ($this->items->removeElement($item)) {
            if ($item->getInvoice() === $this) {
                $item->setInvoice(null);
            }
        }
        return $this;
    }

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
            $payment->setInvoice($this);
        }
        return $this;
    }

    public function removePayment(Payment $payment): self
    {
        if ($this->payments->removeElement($payment)) {
            if ($payment->getInvoice() === $this) {
                $payment->setInvoice(null);
            }
        }
        return $this;
    }
}
<?php
declare(strict_types=1);

namespace App\Entity;

use App\Repository\BarberWalletRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BarberWalletRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_BARBER_DATE', columns: ['barber_id', 'date'])]
class BarberWallet
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // Many wallets per barber, one per day
    #[ORM\ManyToOne(inversedBy: 'wallets')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?User $barber = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private string $balance = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private string $totalEarned = '0.00';

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private ?\DateTimeImmutable $date = null;

    /** @var Collection<int, Payment> */
    #[ORM\OneToMany(mappedBy: 'wallet', targetEntity: Payment::class, cascade: ['persist'], orphanRemoval: false)]
    private Collection $payments;

    public function __construct()
    {
        $this->payments = new ArrayCollection();
        $this->date     = new \DateTimeImmutable('today');
    }

    /* ---------- Getters / Setters ---------- */

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBarber(): ?User
    {
        return $this->barber;
    }

    public function setBarber(?User $barber): self
    {
        $this->barber = $barber;
        return $this;
    }

    public function getBalance(): string
    {
        return $this->balance;
    }

    public function setBalance(string $balance): self
    {
        $this->balance = $balance;
        return $this;
    }

    public function getTotalEarned(): string
    {
        return $this->totalEarned;
    }

    public function setTotalEarned(string $totalEarned): self
    {
        $this->totalEarned = $totalEarned;
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

    /** @return Collection<int, Payment> */
    public function getPayments(): Collection
    {
        return $this->payments;
    }

    public function addPayment(Payment $payment): self
    {
        if (!$this->payments->contains($payment)) {
            $this->payments->add($payment);
            $payment->setWallet($this);
        }
        return $this;
    }

    public function removePayment(Payment $payment): self
    {
        if ($this->payments->removeElement($payment) && $payment->getWallet() === $this) {
            $payment->setWallet(null);
        }
        return $this;
    }
}

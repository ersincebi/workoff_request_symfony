<?php

namespace App\Entity;

use App\Repository\EmployeesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EmployeesRepository::class)]
class Employees
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $tc_no = null;

    #[ORM\Column(length: 255)]
    private ?string $sgk_no = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $surname = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $begin_date = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $quit_date = null;

    #[ORM\OneToMany(mappedBy: 'tc_no', targetEntity: Workoffs::class)]
    private Collection $workoffs;

    public function __construct()
    {
        $this->workoffs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTcNo(): ?string
    {
        return $this->tc_no;
    }

    public function setTcNo(string $tc_no): self
    {
        $this->tc_no = $tc_no;

        return $this;
    }

    public function getSgkNo(): ?string
    {
        return $this->sgk_no;
    }

    public function setSgkNo(string $sgk_no): self
    {
        $this->sgk_no = $sgk_no;

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

    public function getSurname(): ?string
    {
        return $this->surname;
    }

    public function setSurname(string $surname): self
    {
        $this->surname = $surname;

        return $this;
    }

    public function getBeginDate(): ?\DateTimeInterface
    {
        return $this->begin_date;
    }

    public function setBeginDate(\DateTimeInterface $begin_date): self
    {
        $this->begin_date = $begin_date;

        return $this;
    }

    public function getQuitDate(): ?\DateTimeInterface
    {
        return $this->quit_date;
    }

    public function setQuitDate(?\DateTimeInterface $quit_date): self
    {
        $this->quit_date = $quit_date;

        return $this;
    }

    /**
     * @return Collection<int, Workoffs>
     */
    public function getWorkoffs(): Collection
    {
        return $this->workoffs;
    }

    public function addWorkoff(Workoffs $workoff): self
    {
        if (!$this->workoffs->contains($workoff)) {
            $this->workoffs->add($workoff);
            $workoff->setTcNo($this);
        }

        return $this;
    }

    public function removeWorkoff(Workoffs $workoff): self
    {
        if ($this->workoffs->removeElement($workoff)) {
            // set the owning side to null (unless already changed)
            if ($workoff->getTcNo() === $this) {
                $workoff->setTcNo(null);
            }
        }

        return $this;
    }
}

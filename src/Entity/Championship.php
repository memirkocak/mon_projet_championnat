<?php

namespace App\Entity;

use App\Repository\ChampionshipRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ChampionshipRepository::class)]
#[ORM\Table(name: 'championship')]
class Championship
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $startDate = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $endDate = null;

    #[ORM\Column]
    private ?int $wonPoint = null;

    #[ORM\Column]
    private ?int $lostPoint = null;

    #[ORM\Column]
    private ?int $drawPoint = null;

    #[ORM\Column(length: 255)]
    private ?string $typeRanking = null;

    #[ORM\OneToMany(targetEntity: Day::class, mappedBy: 'championship', cascade: ['persist', 'remove'])]
    private Collection $days;

    #[ORM\OneToMany(targetEntity: TeamChampionShip::class, mappedBy: 'championship', cascade: ['persist', 'remove'])]
    private Collection $teamChampionShips;

    public function __construct()
    {
        $this->days = new ArrayCollection();
        $this->teamChampionShips = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): static
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTimeInterface $endDate): static
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getWonPoint(): ?int
    {
        return $this->wonPoint;
    }

    public function setWonPoint(int $wonPoint): static
    {
        $this->wonPoint = $wonPoint;

        return $this;
    }

    public function getLostPoint(): ?int
    {
        return $this->lostPoint;
    }

    public function setLostPoint(int $lostPoint): static
    {
        $this->lostPoint = $lostPoint;

        return $this;
    }

    public function getDrawPoint(): ?int
    {
        return $this->drawPoint;
    }

    public function setDrawPoint(int $drawPoint): static
    {
        $this->drawPoint = $drawPoint;

        return $this;
    }

    public function getTypeRanking(): ?string
    {
        return $this->typeRanking;
    }

    public function setTypeRanking(string $typeRanking): static
    {
        $this->typeRanking = $typeRanking;

        return $this;
    }

    /**
     * @return Collection<int, Day>
     */
    public function getDays(): Collection
    {
        return $this->days;
    }

    public function addDay(Day $day): static
    {
        if (!$this->days->contains($day)) {
            $this->days->add($day);
            $day->setChampionship($this);
        }

        return $this;
    }

    public function removeDay(Day $day): static
    {
        if ($this->days->removeElement($day)) {
            // set the owning side to null (unless already changed)
            if ($day->getChampionship() === $this) {
                $day->setChampionship(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, TeamChampionShip>
     */
    public function getTeamChampionShips(): Collection
    {
        return $this->teamChampionShips;
    }

    public function addTeamChampionShip(TeamChampionShip $teamChampionShip): static
    {
        if (!$this->teamChampionShips->contains($teamChampionShip)) {
            $this->teamChampionShips->add($teamChampionShip);
            $teamChampionShip->setChampionship($this);
        }

        return $this;
    }

    public function removeTeamChampionShip(TeamChampionShip $teamChampionShip): static
    {
        if ($this->teamChampionShips->removeElement($teamChampionShip)) {
            // set the owning side to null (unless already changed)
            if ($teamChampionShip->getChampionship() === $this) {
                $teamChampionShip->setChampionship(null);
            }
        }

        return $this;
    }
}


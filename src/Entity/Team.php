<?php

namespace App\Entity;

use App\Repository\TeamRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TeamRepository::class)]
#[ORM\Table(name: 'team')]
class Team
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $creationDate = null;

    #[ORM\Column(length: 255)]
    private ?string $stade = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $logo = null;

    #[ORM\Column(length: 255)]
    private ?string $president = null;

    #[ORM\Column(length: 255)]
    private ?string $coach = null;

    #[ORM\ManyToOne(inversedBy: 'teams')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Country $country = null;

    #[ORM\OneToMany(targetEntity: Game::class, mappedBy: 'team1')]
    private Collection $gamesAsTeam1;

    #[ORM\OneToMany(targetEntity: Game::class, mappedBy: 'team2')]
    private Collection $gamesAsTeam2;

    #[ORM\OneToMany(targetEntity: TeamChampionShip::class, mappedBy: 'team', cascade: ['persist', 'remove'])]
    private Collection $teamChampionShips;

    public function __construct()
    {
        $this->gamesAsTeam1 = new ArrayCollection();
        $this->gamesAsTeam2 = new ArrayCollection();
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

    public function getCreationDate(): ?\DateTimeInterface
    {
        return $this->creationDate;
    }

    public function setCreationDate(\DateTimeInterface $creationDate): static
    {
        $this->creationDate = $creationDate;

        return $this;
    }

    public function getStade(): ?string
    {
        return $this->stade;
    }

    public function setStade(string $stade): static
    {
        $this->stade = $stade;

        return $this;
    }

    public function getLogo(): ?string
    {
        return $this->logo;
    }

    public function setLogo(?string $logo): static
    {
        $this->logo = $logo;

        return $this;
    }

    public function getPresident(): ?string
    {
        return $this->president;
    }

    public function setPresident(string $president): static
    {
        $this->president = $president;

        return $this;
    }

    public function getCoach(): ?string
    {
        return $this->coach;
    }

    public function setCoach(string $coach): static
    {
        $this->coach = $coach;

        return $this;
    }

    public function getCountry(): ?Country
    {
        return $this->country;
    }

    public function setCountry(?Country $country): static
    {
        $this->country = $country;

        return $this;
    }

    /**
     * @return Collection<int, Game>
     */
    public function getGamesAsTeam1(): Collection
    {
        return $this->gamesAsTeam1;
    }

    public function addGamesAsTeam1(Game $gamesAsTeam1): static
    {
        if (!$this->gamesAsTeam1->contains($gamesAsTeam1)) {
            $this->gamesAsTeam1->add($gamesAsTeam1);
            $gamesAsTeam1->setTeam1($this);
        }

        return $this;
    }

    public function removeGamesAsTeam1(Game $gamesAsTeam1): static
    {
        if ($this->gamesAsTeam1->removeElement($gamesAsTeam1)) {
            // set the owning side to null (unless already changed)
            if ($gamesAsTeam1->getTeam1() === $this) {
                $gamesAsTeam1->setTeam1(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Game>
     */
    public function getGamesAsTeam2(): Collection
    {
        return $this->gamesAsTeam2;
    }

    public function addGamesAsTeam2(Game $gamesAsTeam2): static
    {
        if (!$this->gamesAsTeam2->contains($gamesAsTeam2)) {
            $this->gamesAsTeam2->add($gamesAsTeam2);
            $gamesAsTeam2->setTeam2($this);
        }

        return $this;
    }

    public function removeGamesAsTeam2(Game $gamesAsTeam2): static
    {
        if ($this->gamesAsTeam2->removeElement($gamesAsTeam2)) {
            // set the owning side to null (unless already changed)
            if ($gamesAsTeam2->getTeam2() === $this) {
                $gamesAsTeam2->setTeam2(null);
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
            $teamChampionShip->setTeam($this);
        }

        return $this;
    }

    public function removeTeamChampionShip(TeamChampionShip $teamChampionShip): static
    {
        if ($this->teamChampionShips->removeElement($teamChampionShip)) {
            // set the owning side to null (unless already changed)
            if ($teamChampionShip->getTeam() === $this) {
                $teamChampionShip->setTeam(null);
            }
        }

        return $this;
    }
}


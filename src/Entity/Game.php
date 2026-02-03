<?php

namespace App\Entity;

use App\Repository\GameRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GameRepository::class)]
#[ORM\Table(name: 'game')]
class Game
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $team1Point = null;

    #[ORM\Column]
    private ?int $team2Point = null;

    #[ORM\ManyToOne(inversedBy: 'gamesAsTeam1')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Team $team1 = null;

    #[ORM\ManyToOne(inversedBy: 'gamesAsTeam2')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Team $team2 = null;

    #[ORM\ManyToOne(inversedBy: 'games')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Day $day = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTeam1Point(): ?int
    {
        return $this->team1Point;
    }

    public function setTeam1Point(int $team1Point): static
    {
        $this->team1Point = $team1Point;

        return $this;
    }

    public function getTeam2Point(): ?int
    {
        return $this->team2Point;
    }

    public function setTeam2Point(int $team2Point): static
    {
        $this->team2Point = $team2Point;

        return $this;
    }

    public function getTeam1(): ?Team
    {
        return $this->team1;
    }

    public function setTeam1(?Team $team1): static
    {
        $this->team1 = $team1;

        return $this;
    }

    public function getTeam2(): ?Team
    {
        return $this->team2;
    }

    public function setTeam2(?Team $team2): static
    {
        $this->team2 = $team2;

        return $this;
    }

    public function getDay(): ?Day
    {
        return $this->day;
    }

    public function setDay(?Day $day): static
    {
        $this->day = $day;

        return $this;
    }
}


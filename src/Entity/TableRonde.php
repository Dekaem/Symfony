<?php

namespace App\Entity;

use App\Entity\Table;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\TableRondeRepository;

/**
 * @ORM\Entity(repositoryClass=TableRondeRepository::class)
 */
class TableRonde
{
    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity=Round::class, inversedBy="tableRondes", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $roundNumber;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity=Table::class, inversedBy="tableRondes", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $tableNumber;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="tableRondes", cascade={"persist"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $users;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRoundNumber(): ?Round
    {
        return $this->roundNumber;
    }

    public function setRoundNumber(?Round $roundNumber): self
    {
        $this->roundNumber = $roundNumber;

        return $this;
    }

    public function getTableNumber(): ?Table
    {
        return $this->tableNumber;
    }

    public function setTableNumber(?Table $tableNumber): self
    {
        $this->tableNumber = $tableNumber;

        return $this;
    }

    public function getUsers(): ?User
    {
        return $this->users;
    }

    public function setUsers(?User $users): self
    {
        $this->users = $users;

        return $this;
    }

}

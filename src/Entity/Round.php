<?php

namespace App\Entity;

use App\Repository\RoundRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=RoundRepository::class)
 */
class Round
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToMany(targetEntity=TableRonde::class, mappedBy="roundNumber", cascade={"persist"})
     */
    private $tableRondes;

    public function __construct()
    {
        $this->tableRondes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection|TableRonde[]
     */
    public function getTableRondes(): Collection
    {
        return $this->tableRondes;
    }

    public function addTableRonde(TableRonde $tableRonde): self
    {
        if (!$this->tableRondes->contains($tableRonde)) {
            $this->tableRondes[] = $tableRonde;
            $tableRonde->setRoundNumber($this);
        }

        return $this;
    }

    public function removeTableRonde(TableRonde $tableRonde): self
    {
        if ($this->tableRondes->removeElement($tableRonde)) {
            // set the owning side to null (unless already changed)
            if ($tableRonde->getRoundNumber() === $this) {
                $tableRonde->setRoundNumber(null);
            }
        }

        return $this;
    }

}

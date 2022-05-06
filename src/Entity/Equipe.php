<?php

namespace App\Entity;

use App\Repository\EquipeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="Equipe")
 * @ORM\Entity(repositoryClass=EquipeRepository::class)
 */
class Equipe
{

    /**
     * @ORM\Id
     * @ORM\Column(type="string", length=3, name="id_equipe")
     * @Assert\Regex(
     * "/^[a-zA-Z]{3}$/",
     * message="Une équipe est caractérisée par son code CIO composé de trois lettres."
     * )
     */
    private $id_equipe;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     * @Assert\Regex(
     * "/^[\s\p{L}-]*$/u",
     * message="Le nom d'une équipe est seulement composé de lettres."
     * )
     */
    private $nat_equipe;

    /**
     * @ORM\Column(type="integer")
     * @Assert\PositiveOrZero(message="Il n'est pas possible d'avoir moins de 0 victoire.")
     */
    private $nbr_victoire;

    /**
     * @ORM\Column(type="integer")
     * @Assert\PositiveOrZero(message="Il n'est pas possible d'avoir moins de 0 défaite.")
     */
    private $nbr_defaite;


    /**
     * @return string
     */
    public function getIdEquipe(): ?string
    {
        return $this->id_equipe;
    }

    /**
     * @param string $id_equipe
     * @return $this
     */
    public function setIdEquipe(string $id_equipe): self
    {
        $this->id_equipe = strtoupper($id_equipe);

        return $this;
    }

    /**
     * @return string
     */
    public function getNatEquipe(): string
    {
        return $this->nat_equipe;
    }

    /**
     * @param string $nat_equipe
     * @return $this
     */
    public function setNatEquipe(string $nat_equipe): self
    {
        $this->nat_equipe = ucfirst($nat_equipe);

        return $this;
    }

    /**
     * @return int
     */
    public function getNbrVictoire(): ?int
    {
        return $this->nbr_victoire;
    }

    /**
     * @param int $nbr_victoire
     * @return $this
     */
    public function setNbrVictoire(int $nbr_victoire): self
    {
        $this->nbr_victoire = $nbr_victoire;

        return $this;
    }

    /**
     * @return int
     */
    public function getNbrDefaite(): ?int
    {
        return $this->nbr_defaite;
    }

    /**
     * @param int $nbr_defaite
     * @return $this
     */
    public function setNbrDefaite(int $nbr_defaite): self
    {
        $this->nbr_defaite = $nbr_defaite;

        return $this;
    }
}

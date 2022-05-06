<?php

namespace App\Entity;

use App\Repository\MembrePersonnelRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Equipe;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="MembrePersonnel")
 * @ORM\Entity(repositoryClass=MembrePersonnelRepository::class)
 */
class MembrePersonnel
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id_memper;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     * @Assert\Regex(
     * "/^[\s\p{L}-]*$/u",
     * message="Le nom ne peut être composé que de lettres."
     * )
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     * @Assert\Regex(
     * "/^[\s\p{L}-]*$/u",
     * message="Le prénom ne peut être composé que de lettres."
     * )
     */
    private $prenom;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     * @Assert\Regex(
     * "/^[\s\p{L}-]*$/u",
     * message="Le rôle ne peut être composé que de lettres."
     * )
     */
    private $role;

    /**
     * @ORM\ManyToOne(targetEntity=Equipe::class)
     * @ORM\JoinColumn(name="id_equipe", referencedColumnName="id_equipe", nullable=false)
     */
    private $equipe;

    /**
     * @return int
     */
    public function getIdMemper(): ?int
    {
        return $this->id_memper;
    }

    /**
     * @param int $id_memper
     * @return $this
     */
    public function setIdMemper(int $id_memper): self
    {
        $this->id_memper = $id_memper;

        return $this;
    }

    /**
     * @return string
     */
    public function getNom(): ?string
    {
        return $this->nom;
    }

    /**
     * @param string $nom
     * @return $this
     */
    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * @return string
     */
    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    /**
     * @param string $prenom
     * @return $this
     */
    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    /**
     * @return string
     */
    public function getRole(): ?string
    {
        return $this->role;
    }

    /**
     * @param string $role
     * @return $this
     */
    public function setRole(string $role): self
    {
        $this->role = $role;

        return $this;
    }

    /**
     * @return Equipe
     */
    public function getEquipe(): ?Equipe
    {
        return $this->equipe;
    }

    /**
     * @param $equipe
     * @return $this
     */
    public function setEquipe(Equipe $equipe): self
    {
        $this->equipe = $equipe;

        return $this;
    }
}

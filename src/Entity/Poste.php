<?php

namespace App\Entity;

use App\Repository\PosteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="Poste")
 * @ORM\Entity(repositoryClass=PosteRepository::class)
 */
class Poste
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id_poste;

    /**
     * @ORM\Column(type="string", length=200, nullable=false)
     * @Assert\Regex(
     * "/^[\s\p{L}-]*$/u",
     * message="Le nom de poste ne peut être composé que de lettres."
     * )
     */
    private $nom;

    /**
     * @return int
     */
    public function getIdPoste(): ?int
    {
        return $this->id_poste;
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


}

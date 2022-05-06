<?php

namespace App\Entity;

use App\Repository\JoueurRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\TypeValidator;
use Symfony\Component\Validator\Constraints\Type;

/**
 * @ORM\Table(name="Joueur")
 * @ORM\Entity(repositoryClass=JoueurRepository::class)
 */
class Joueur
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id_joueur;


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
     * @ORM\Column(type="integer", nullable=false)
     * @Assert\GreaterThanOrEqual(20,
     * message="Le joueur doit avoir au minimum 20 ans pour jouer en FIBA"
     * )
     */
    private $age;

    /**
     * @ORM\Column(type="float", nullable=false)
     * @Assert\GreaterThanOrEqual(100,
     * message="La taille minimale est de 100cm"
     * )
     */
    private $taille;


    /**
     * @ORM\ManyToOne(targetEntity=Equipe::class)
     * @ORM\JoinColumn(name="id_equipe", referencedColumnName="id_equipe", nullable=false)
     */
    private $equipe;

    /**
     * @ORM\ManyToOne(targetEntity=Poste::class)
     * @ORM\JoinColumn(name="id_poste", referencedColumnName="id_poste", nullable=false)
     */
    private $poste;


    /**
     * @return int
     */
    public function getIdJoueur(): ?int
    {
        return $this->id_joueur;
    }

    /**
     * @param int $id_joueur
     * @return $this
     */
    public function setIdJoueur(int $id_joueur): self
    {
        $this->id_joueur = $id_joueur;

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
        $this->nom = ucfirst($nom);

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
        $this->prenom = ucfirst($prenom);

        return $this;
    }

    /**
     * @return int
     */
    public function getAge(): ?int
    {
        return $this->age;
    }

    /**
     * @param int $age
     * @return $this
     */
    public function setAge(int $age): self
    {
        $this->age = $age;

        return $this;
    }

    /**
     * @return float
     */
    public function getTaille(): ?float
    {
        return $this->taille;
    }

    /**
     * @param float $taille
     * @return $this
     */
    public function setTaille(float $taille): self
    {
        $this->taille = $taille;

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
     * @param Equipe $equipe
     * @return $this
     */
    public function setEquipe(Equipe $equipe): self
    {
        $this->equipe = $equipe;

        return $this;
    }

    /**
     * @return Poste
     */
    public function getPoste(): ?Poste
    {
        return $this->poste;
    }

    /**
     * @param Poste $poste
     * @return $this
     */
    public function setPoste(Poste $poste): self
    {
        $this->poste = $poste;

        return $this;
    }




}

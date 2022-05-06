<?php

namespace App\Entity;

use App\Repository\MatchRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="`Match`")
 * @ORM\Entity(repositoryClass=MatchRepository::class)
 * @ORM\Table(name="match")
 *
 */
class Match
{
    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity=Equipe::class)
     * @ORM\JoinColumn(name="id_equipe1", referencedColumnName="id_equipe", nullable=false)
     */
    private $equipe1;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity=Equipe::class)
     * @ORM\JoinColumn(name="id_equipe2", referencedColumnName="id_equipe", nullable=false)
     * @Assert\Expression(
     *     "this.getEquipe1() != this.getEquipe2()",
     *     message="Une équipe ne peut pas jouer contre-elle même!"
     * )
     */
    private $equipe2;

    /**
     * @ORM\Id
     * @ORM\Column(type="string", length=255, nullable=false)
     * @Assert\Date(message="Vous devez saisir une date au format Y-m-d ou au format indiqué par le navigateur.")
     */
    private $date;


    /**
     * @ORM\Column(type="integer", nullable=false)
     * @Assert\PositiveOrZero(message="Un score ne peut pas être négatif.")
     */
    private $score_equipe1;
    /**
     * @ORM\Column(type="integer", nullable=false)
     * @Assert\PositiveOrZero(message="Un score ne peut pas être négatif.")
     */
    private $score_equipe2;

    /**
     * @return Equipe
     */
    public function getEquipe1(): ?Equipe
    {
        return $this->equipe1;
    }

    /**
     * @param Equipe $equipe1
     * @return $this
     */
    public function setEquipe1(Equipe $equipe1): self
    {
        $this->equipe1 = $equipe1;
        return $this;
    }

    /**
     * @return Equipe
     */
    public function getEquipe2(): ?Equipe
    {
        return $this->equipe2;
    }

    /**
     * @param Equipe $equipe2
     * @return $this
     */
    public function setEquipe2(Equipe $equipe2): self
    {
        $this->equipe2 = $equipe2;

        return $this;
    }

    /**
     * @return int
     */
    public function getScoreEquipe1(): ?int
    {
        return $this->score_equipe1;
    }

    /**
     * @param int $scoreEquipe1
     * @return $this
     */
    public function setScoreEquipe1(int $scoreEquipe1): self
    {
        $this->score_equipe1 = $scoreEquipe1;
        return $this;
    }

    /**
     * @return int
     */
    public function getScoreEquipe2(): ?int
    {
        return $this->score_equipe2;
    }

    /**
     * @param int $scoreEquipe2
     * @return $this
     */
    public function setScoreEquipe2(int $scoreEquipe2): self
    {
        $this->score_equipe2 = $scoreEquipe2;
        return $this;
    }

    /**
     * @param string $date
     */
    public function setDate(string $date) {
        $this->date = $date;
    }

    /**
     * @return string
     */
    public function getDate(): ?string
    {
        return $this->date;
    }
}

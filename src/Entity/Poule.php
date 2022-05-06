<?php

namespace App\Entity;

use App\Repository\PouleRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="Poule")
 * @ORM\Entity(repositoryClass=PouleRepository::class)
 */
class Poule
{
    /**
     * @ORM\Id
     * @ORM\Column(type="string", length=1)
     * @Assert\Regex(
     * "/^[a-zA-Z]$/",
     * message="Une poule est caractérisée par une lettre unique."
     * )
     */
    private $id_poule;

    /**
     * @ORM\Column(type="integer", nullable=false)
     * @Assert\Range(
     *     min=1,
     *     max=3,
     *     notInRangeMessage="Une phase est comprise entre {{ min }} et {{ max }}."
     * )
     */
    private $phase;

    public function getIdPoule(): ?string
    {
        return $this->id_poule;
    }

    public function setIdPoule(string $id_poule): self
    {
        $this->id_poule = ucfirst($id_poule);

        return $this;
    }

    public function getPhase(): ?int
    {
        return $this->phase;
    }

    public function setPhase(int $phase): self
    {
        $this->phase = $phase;

        return $this;
    }
}

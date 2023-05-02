<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
/**
 * Formation
 *
 * @ORM\Table(name="formation")
 * @ORM\Entity
 */
class Formation
{
    /**
     * @var int
     *
     * @ORM\Column(name="id_mat", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idMat;

    /**
     * @var string
     *
     * @ORM\Column(name="titrefr", type="string", length=255, nullable=false)
     * @Assert\NotBlank(message="Le titre doit etre non vide")
     */
    private $titrefr;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255, nullable=false)
     * @Assert\NotBlank(message=" Le type doit etre non vide")
     * @Assert\Length(
     *      min = 5,
     *      minMessage=" Entrer un type au minimuim de 5 caracteres"
     *
     *     )
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="contenu", type="string", length=1000, nullable=false)
     * @Assert\NotBlank(message=" Le contenu doit etre non vide")
     */
    private $contenu;

    /**
     * @var string
     *
     * @ORM\Column(name="niveau", type="string", length=255, nullable=false)

     */
    private $niveau;

    private   $inscription;

    #[ORM\ManyToMany(targetEntity: Inscription::class, inversedBy: 'inscriptions')]
    private ArrayCollection $idinscri;

    public function __construct()
    {
        $this->idinscri = new ArrayCollection();
    }

    public function getIdMat(): ?int
    {
        return $this->idMat;
    }

    public function getTitrefr(): ?string
    {
        return $this->titrefr;
    }

    public function setTitrefr(string $titrefr): self
    {
        $this->titrefr = $titrefr;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getContenu(): ?string
    {
        return $this->contenu;
    }

    public function setContenu(string $contenu): self
    {
        $this->contenu = $contenu;

        return $this;
    }

    public function getNiveau(): ?string
    {
        return $this->niveau;
    }

    public function setNiveau(string $niveau): self
    {
        $this->niveau = $niveau;

        return $this;
    }


}

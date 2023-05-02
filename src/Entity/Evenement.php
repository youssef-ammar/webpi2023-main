<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\ParticipationEv;
use Doctrine\ORM\Mapping\OneToMany;


/**
 * Evenement
 *
 * @ORM\Table(name="evenement")
 * @ORM\Entity
 */
class Evenement
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="nom_ev", type="string", length=255, nullable=false)
     */
    private $nomEv;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_ev", type="date", nullable=false)
     */
    private $dateEv;

    /**
     * @var int
     *
     * @ORM\Column(name="heure_ev", type="integer", nullable=false)
     */
    private $heureEv;

    /**
     * @var string
     *
     * @ORM\Column(name="emplacement", type="string", length=255, nullable=false)
     */
    private $emplacement;

    /**
     * @var string
     *
     * @ORM\Column(name="image", type="string", length=255, nullable=false)
     */
    private $image;

    /**
     * @var string
     *
     * @ORM\Column(name="region", type="string", length=255, nullable=false)
     */
    private $region;
    /**
     * @OneToMany(targetEntity="ParticipationEv", mappedBy="idEv")
     */
    private $participations;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="events")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;
    public function __construct()
    {
        $this->participations = new ArrayCollection();
    }

    public function addParticipation(ParticipationEv $participation)
    {
        $this->participations[] = $participation;
        $participation->setIdEv($this);
    }

    public function removeParticipation(ParticipationEv $participation)
    {
        $this->participations->removeElement($participation);
        $participation->setIdEv(null);
    }

    public function getParticipations(): Collection
    {
        return $this->participations;
    }
    /**
     * @ORM\ManyToOne(targetEntity="TypeEvenement")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_type_id", referencedColumnName="id")
     * })
     */
   private $idType;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomEv(): ?string
    {
        return $this->nomEv;
    }

    public function setNomEv(string $nomEv): self
    {
        $this->nomEv = $nomEv;

        return $this;
    }

    public function getDateEv(): ?\DateTimeInterface
    {
        return $this->dateEv;
    }

    public function setDateEv(\DateTimeInterface $dateEv): self
    {
        $this->dateEv = $dateEv;

        return $this;
    }

    public function getHeureEv(): ?int
    {
        return $this->heureEv;
    }

    public function setHeureEv(int $heureEv): self
    {
        $this->heureEv = $heureEv;

        return $this;
    }

    public function getEmplacement(): ?string
    {
        return $this->emplacement;
    }

    public function setEmplacement(string $emplacement): self
    {
        $this->emplacement = $emplacement;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getRegion(): ?string
    {
        return $this->region;
    }

    public function setRegion(string $region): self
    {
        $this->region = $region;

        return $this;
    }

    public function getIdType(): ?TypeEvenement
    {
        return $this->idType;
    }

    public function setIdType(?TypeEvenement $idType): self
    {
        $this->idType = $idType;

        return $this;
    }
}

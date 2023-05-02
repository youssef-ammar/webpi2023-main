<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Reclamation
 *
 * @ORM\Table(name="reclamation")
 * @ORM\Entity
 */
class Reclamation
{
    /**
     * @var int
     *
     * @ORM\Column(name="Idrec", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idrec;

    /**
     * @var string
     *
     * @ORM\Column(name="nomuser", type="string", length=255, nullable=false)
     */
    private $nomuser;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=false)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="objet", type="string", length=255, nullable=false)
     */
    private $objet;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255, nullable=false)
     */
    private $description;

    /**
     * @var string|null
     *
     * @ORM\Column(name="etatrec", type="string", length=20, nullable=true, options={"default"="En attente "})
     */
    private $etatrec = 'En attente ';

    public function getIdrec(): ?int
    {
        return $this->idrec;
    }

    public function getNomuser(): ?string
    {
        return $this->nomuser;
    }

    public function setNomuser(string $nomuser): self
    {
        $this->nomuser = $nomuser;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getObjet(): ?string
    {
        return $this->objet;
    }

    public function setObjet(string $objet): self
    {
        $this->objet = $objet;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getEtatrec(): ?string
    {
        return $this->etatrec;
    }

    public function setEtatrec(?string $etatrec): self
    {
        $this->etatrec = $etatrec;

        return $this;
    }


}

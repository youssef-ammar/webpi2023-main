<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Service
 *
 * @ORM\Table(name="service")
 * @ORM\Entity
 */
class Service
{
    /**
     * @var int
     *
     * @ORM\Column(name="ServId", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $servid;

    /**
     * @var string
     *
     * @ORM\Column(name="ServLib", type="string", length=20, nullable=false)
     */
    private $servlib;

    /**
     * @var string
     *
     * @ORM\Column(name="ServDesc", type="string", length=200, nullable=false)
     */
    private $servdesc;

    /**
     * @var int
     *
     * @ORM\Column(name="ServDispo", type="integer", nullable=false)
     */
    private $servdispo;

    /**
     * @var string
     *
     * @ORM\Column(name="ServImg", type="string", length=255, nullable=false)
     */
    private $servimg;

    /**
     * @var float
     *
     * @ORM\Column(name="ServPrix", type="float", precision=10, scale=0, nullable=false)
     */
    private $servprix;

    /**
     * @var string
     *
     * @ORM\Column(name="ServVille", type="string", length=20, nullable=false)
     */
    private $servville;

    /**
     * @var string
     *
     * @ORM\Column(name="CatLib", type="string", length=20, nullable=false)
     */
    private $catlib;

    /**
     * @var string
     *
     * @ORM\Column(name="QrCode", type="string", length=255, nullable=false)
     */
    private $qrcode;

    /**
     * @var int|null
     *
     * @ORM\Column(name="note", type="integer", nullable=true)
     */
    private $note;

    /*
     * @var \User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_user", referencedColumnName="id_user")
     * })
     */
//    private $idUser;

    public function getServid(): ?int
    {
        return $this->servid;
    }

    public function getServlib(): ?string
    {
        return $this->servlib;
    }

    public function setServlib(string $servlib): self
    {
        $this->servlib = $servlib;

        return $this;
    }

    public function getServdesc(): ?string
    {
        return $this->servdesc;
    }

    public function setServdesc(string $servdesc): self
    {
        $this->servdesc = $servdesc;

        return $this;
    }

    public function getServdispo(): ?int
    {
        return $this->servdispo;
    }

    public function setServdispo(int $servdispo): self
    {
        $this->servdispo = $servdispo;

        return $this;
    }

    public function getServimg(): ?string
    {
        return $this->servimg;
    }

    public function setServimg(string $servimg): self
    {
        $this->servimg = $servimg;

        return $this;
    }

    public function getServprix(): ?float
    {
        return $this->servprix;
    }

    public function setServprix(float $servprix): self
    {
        $this->servprix = $servprix;

        return $this;
    }

    public function getServville(): ?string
    {
        return $this->servville;
    }

    public function setServville(string $servville): self
    {
        $this->servville = $servville;

        return $this;
    }

    public function getCatlib(): ?string
    {
        return $this->catlib;
    }

    public function setCatlib(string $catlib): self
    {
        $this->catlib = $catlib;

        return $this;
    }

    public function getQrcode(): ?string
    {
        return $this->qrcode;
    }

    public function setQrcode(string $qrcode): self
    {
        $this->qrcode = $qrcode;

        return $this;
    }

    public function getNote(): ?int
    {
        return $this->note;
    }

    public function setNote(?int $note): self
    {
        $this->note = $note;

        return $this;
    }

    public function getIdUser(): ?User
    {
        return $this->idUser;
    }

    public function setIdUser(?User $idUser): self
    {
        $this->idUser = $idUser;

        return $this;
    }


}

<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Categorie
 *
 * @ORM\Table(name="categorie")
 * @ORM\Entity
 */
class Categorie
{
    /**
     * @var int
     *
     * @ORM\Column(name="CatId", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $catid;

    /**
     * @var string
     *
     * @ORM\Column(name="CatLib", type="string", length=20, nullable=false)
     */
    private $catlib;

    public function getCatid(): ?int
    {
        return $this->catid;
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


}

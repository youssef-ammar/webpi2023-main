<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\Collection;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\Table(name="user")
 */
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @var int
     *@ORM\Column(name="id",type="integer")
     *@ORM\Id
     *@ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * * @ORM\Column(length=180, unique=true)
     * @Assert\NotBlank (message="Please enter your email!")
     * @Assert\Email (message="Invalid mail!")
     */
    private ?string $email = null;
    /**
     * @ORM\Column
     */
    private int $state =0 ;
    /**
     * @ORM\Column
     */
    private array $roles=[] ;


    private string $role;
    /**
     * @var string The hashed password
     **
     * @Assert\NotBlank (message="Please enter your password!")
     * @Assert\Regex(
     *     pattern="/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{1,8}$/",
     *     message="The password should contain at least one uppercase letter, numbers, lowercase letters, and special characters, and be no longer than 8 characters"
     * )
     * @ORM\Column
     */
    private ?string $password = null;


    /**
     *
     *  @ORM\Column(length=255)
     * @Assert\NotBlank (message="Please enter Firstname!")
     * @Assert\Regex(
     *     pattern="/^[a-zA-Z]+$/",
     *     message="Firstname should contain only alphabetical letters"
     * )
     */
    private ?string $prenom = null;


    /**
     * @ORM\Column(length=255)
     * @Assert\NotBlank (message="Please enter your Lastname!")
     * @Assert\Regex(
     *     pattern="/^[a-zA-Z]+$/",
     *     message="Lastname should contain only alphabetical letters"
     * )
     */
    private ?string $nom = null;


    /**
     * @ORM\Column(type=Types::DATE_MUTABLE)
     * @Assert\LessThanOrEqual("-18 years", message="You must be at least 18 years old.")
     * @Assert\NotBlank(message="Please enter your birth date")
     */
    private ?\DateTimeInterface $date_naissance = null;




    /**
     * @ORM\Column
     * @Assert\Regex(
     *     pattern="/^\d{8}$/",
     *     message="The phone number should be exactly 8 digits"
     * )
     * @Assert\NotBlank(message="Please enter your phone number")
     */
    private ?int $numTel = null;


    /**
     *@ORM\Column(length=255)
     *
     *@Assert\NotBlank(message="Please enter your adress")
     */
    private ?string $adresse = null;
    /**
     * @ORM\Column(nullable=true)
     */
    private ?float $latitude = null;

    /**
     * @ORM\Column(nullable=true)
     */
    private ?float $longitude = null;

    /**
     * @ORM\Column(type="boolean",nullable=true,name="is_verified")
     *
     */
    private $isVerified = false;
    /**
     *@ORM\Column(length=255)
     */
    private ?string $block = 'unBlocked';
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Evenement", mappedBy="user")
     */
    private Collection  $events;
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Evenement", mappedBy="user")
     */
    private Collection $participations;

    public function __construct()
    {
        $this->participations = new ArrayCollection();
        $this->events = new ArrayCollection();

    }

    /**
     * @return Collection<int, Evenement>
     */
    public function getEvents(): Collection
    {
        return $this->events;
    }
    /**
     * @return Collection<int, ParticipationEv>
     */

    public function getBlock(): ?string
    {
        return $this->block;
    }

    public function setBlock(string $block): self
    {
        $this->block = $block;

        return $this;
    }
    public function getId(): ?int
    {
        return $this->id;
    }
    public function getState(): ?int
    {
        return $this->state;
    }
    public function setState(int $state): self
    {
         $this->state=$state;
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

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }
    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(string $role): self
    {
        $this->role = $role;

        return $this;
    }
    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getDateNaissance(): ?\DateTimeInterface
    {
        return $this->date_naissance;
    }

    public function setDateNaissance(\DateTimeInterface $date_naissance): self
    {
        $this->date_naissance = $date_naissance;

        return $this;
    }


    public function getNumTel(): ?int
    {
        return $this->numTel;
    }

    public function setNumTel(int $numTel): self
    {
        $this->numTel = $numTel;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): self
    {
        $this->adresse = $adresse;

        return $this;
    }
    /**
     * @return float|null
     */
    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    /**
     * @param float|null $latitude
     */
    public function setLatitude(?float $latitude): void
    {
        $this->latitude = $latitude;
    }

    /**
     * @return float|null
     */
    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    /**
     * @param float|null $longitude
     */
    public function setLongitude(?float $longitude): void
    {
        $this->longitude = $longitude;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }
    public function addEvent(Evenement $event): self
    {
        if (!$this->events->contains($event)) {
            $this->events[] = $event;
            $event->setUser($this);
        }

        return $this;
    }

    public function removeEvent(Evenement $event): self
    {
        if ($this->events->contains($event)) {
            $this->events->removeElement($event);
            // set the owning side to null (unless already changed)
            if ($event->getUser() === $this) {
                $event->setUser(null);
            }
        }

        return $this;
    }
    public function addParticipation(ParticipationEv $participation): self
    {
        if (!$this->participations->contains($participation)) {
            $this->participations[] = $participation;
            $participation->setUser($this);
        }

        return $this;
    }

    public function removeParticipation(ParticipationEv $participation): self
    {
        if ($this->participations->contains($participation)) {
            $this->participations->removeElement($participation);
            // set the owning side to null (unless already changed)
            if ($participation->getUser() === $this) {
                $participation->setUser(null);
            }
        }

        return $this;
    }
}

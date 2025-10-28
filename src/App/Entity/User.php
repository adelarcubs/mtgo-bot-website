<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\UserRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'users')]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255, unique: true, nullable: false)]
    private string $email;

    #[ORM\Column(type: 'string', length: 100, nullable: false)]
    private string $name;

    #[ORM\Column(type: 'string', length: 255, nullable: false)]
    private string $password;

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'string', length: 100, nullable: false)]
    private string $defaultLocation;

    public function __construct(string $email, string $name, string $password, string $defaultLocation)
    {
        $this->email           = $email;
        $this->name            = $name;
        $this->password        = $password;
        $this->defaultLocation = $defaultLocation;
        $this->createdAt       = new DateTimeImmutable();
    }

    // Getters and Setters
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getDefaultLocation(): string
    {
        return $this->defaultLocation;
    }

    public function setDefaultLocation(string $defaultLocation): self
    {
        $this->defaultLocation = $defaultLocation;
        return $this;
    }
}

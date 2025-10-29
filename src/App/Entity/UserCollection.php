<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\UserCollectionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Laminas\Permissions\Rbac\RoleInterface;

#[ORM\Entity(repositoryClass: UserCollectionRepository::class)]
class UserCollection
{
    public function __construct(
        #[ORM\Id]
        #[ORM\GeneratedValue]
        #[ORM\Column(type: Types::INTEGER)]
        private ?int $id = null,

        #[ORM\Column(type: Types::STRING, length: 255)]
        private string $name = '',

        #[ORM\Column(type: Types::TEXT)]
        private string $code = '',

        #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'collections')]
        private ?User $user = null,
    ) {
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;
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
}

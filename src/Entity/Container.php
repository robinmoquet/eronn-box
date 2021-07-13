<?php

namespace App\Entity;

use App\Config\VeraCrypt;
use App\Repository\ContainerRepository;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\ArrayShape;

/**
 * @ORM\Entity(repositoryClass=ContainerRepository::class)
 */
class Container
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="integer", type="bigint")
     */
    private $size;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="containers")
     */
    private $user;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $keysecure;
    private VeraCrypt $veraCryptConfig;

    /**
     * Container constructor.
     * @param VeraCrypt $veraCryptConfig
     */
    public function __construct(VeraCrypt $veraCryptConfig)
    {
        $this->veraCryptConfig = $veraCryptConfig;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getSize(): int
    {
        return $this->size;
    }

    public function setSize(int $size): self
    {
        $this->size = $size;

        return $this;
    }

    public function getExt(): string
    {
        return 'hc';
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

    public function getKeysecure(): ?string
    {
        return $this->keysecure;
    }

    public function setKeysecure(string $keysecure): self
    {
        $this->keysecure = $keysecure;

        return $this;
    }

    public function getDownloadDestDir(): string
    {
        $user = $this->getUser();

        return dirname(dirname(__DIR__)) . '/tmpStorage/container/' . $user->getKeysecure();
    }

    public function getNameWithExt(): string
    {
        return $this->getName() . '.' . $this->getExt();
    }

    public function toArray(): array
    {
        return [
            "name" => $this->getName(),
            "size" => $this->getSize(),
            "ext" => $this->getExt(),
            "keysecure" => $this->getKeysecure()
        ];
    }

}

<?php

namespace App\Entity;

use App\Repository\ContainerRepository;
use Doctrine\ORM\Mapping as ORM;

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
     * @ORM\Column(type="string", length=255)
     */
    private $path;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $mountDirectory;

    /**
     * @ORM\Column(type="integer")
     */
    private $size;

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

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    public function getMountDirectory(): ?string
    {
        return $this->mountDirectory;
    }

    public function setMountDirectory(string $mountDirectory): self
    {
        $this->mountDirectory = $mountDirectory;

        return $this;
    }

    public function getSize(): ?string
    {
        $size = $this->size;
        $unit = 'Ko';
        if ($size > 1000) {
            $size = round($size / 1000);
            $unit = 'Mo';
        }
        if ($size > 1000) {
            $size = round($size / 1000);
            $unit = 'Go';
        }
        return $size . " " . $unit;
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
}

<?php

namespace App\Entity;

use App\Repository\TaskOwnerRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TaskOwnerRepository::class)]
class TaskOwner
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $idTask = null;

    #[ORM\Column]
    private ?int $idProject = null; // Anciennement idOwner

    #[ORM\Column]
    private ?int $idUser = null; // Nouveau champ ajouté

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdTask(): ?int
    {
        return $this->idTask;
    }

    public function setIdTask(int $idTask): static
    {
        $this->idTask = $idTask;

        return $this;
    }

    public function getIdProject(): ?int
    {
        return $this->idProject;
    }

    public function setIdProject(int $idProject): static
    {
        $this->idProject = $idProject;

        return $this;
    }

    public function getIdUser(): ?int
    {
        return $this->idUser;
    }

    public function setIdUser(int $idUser): static
    {
        $this->idUser = $idUser;

        return $this;
    }
}


<?php

namespace App\Entity;

use App\Repository\ProgramRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProgramRepository::class)]
class Program
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $durationInDays = null;

    #[ORM\ManyToOne(inversedBy: 'programs')]
    private ?Course $course = null;

    #[ORM\ManyToOne(inversedBy: 'programs')]
    private ?Session $session = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDurationInDays(): ?int
    {
        return $this->durationInDays;
    }

    public function setDurationInDays(int $durationInDays): static
    {
        $this->durationInDays = $durationInDays;

        return $this;
    }

    public function getCourse(): ?Course
    {
        return $this->course;
    }

    public function setCourse(?Course $course): static
    {
        $this->course = $course;

        return $this;
    }

    public function getSession(): ?Session
    {
        return $this->session;
    }

    public function setSession(?Session $session): static
    {
        $this->session = $session;

        return $this;
    }
}

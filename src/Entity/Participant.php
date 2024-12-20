<?php

namespace App\Entity;

use App\Controller\EventController;
use App\Repository\ParticipantRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ParticipantRepository::class)]
class Participant
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank]
    #[Assert\Length(
        min: 3,
        max: 50,
        minMessage: 'Le nom doit contenir au moins {{ limit }} caractères.',
        maxMessage: 'Le nom ne peut pas dépasser {{ limit }} caractères.'
    )]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Email(message: 'L\'adresse email "{{ value }}" n\'est pas valide.')]
    private ?string $email = null;

    #[ORM\ManyToOne(inversedBy: 'participants')]
    private ?Event $event = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private string $city;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $distanceToEvent = null;

    public function getCity(): string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;
        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getEvent(): ?event
    {
        return $this->event;
    }

    public function setEvent(?event $event): static
    {
        $this->event = $event;

        return $this;
    }

    public function getDistanceToEvent(): ?string
    {
        return $this->distanceToEvent;
    }

    public function setDistanceToEvent(?string $distanceToEvent): static
    {
        $this->distanceToEvent = $distanceToEvent;

        return $this;
    }
}

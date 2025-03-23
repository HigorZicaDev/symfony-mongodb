<?php

namespace App\Document;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

#[MongoDB\Document]
class Project
{
    #[MongoDB\Id]
    private ?string $id = null;

    #[MongoDB\Field(type: "string")]
    private string $title;

    // Relacionamento com EmbedMany para Diagram
    #[MongoDB\EmbedMany(targetDocument: Diagram::class)]
    private Collection $diagrams;

    public function __construct()
    {
        $this->diagrams = new ArrayCollection();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function getDiagrams(): Collection
    {
        return $this->diagrams;
    }

    public function addDiagram(Diagram $diagram): self
    {
        $this->diagrams->add($diagram);
        return $this;
    }
}

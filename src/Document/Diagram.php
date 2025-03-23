<?php

namespace App\Document;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Symfony\Component\Serializer\Annotation\Groups;

#[MongoDB\EmbeddedDocument]
class Diagram
{
    #[MongoDB\Field(type: "string")]
    #[Groups(["project:read", "project:write"])] // Definindo os grupos para a serialização
    private string $title;

    #[MongoDB\EmbedMany(targetDocument: Diagram::class)] // Relacionamento EmbedMany com Diagram
    #[Groups(["project:read", "project:write"])] // Definindo os grupos para a serialização
    private Collection $childs;

    public function __construct()
    {
        $this->childs = new ArrayCollection();
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getChilds(): Collection
    {
        return $this->childs;
    }

    public function setChilds(Collection $childs): void
    {
        $this->childs = $childs;
    }

    public function addChild(Diagram $child): void
    {
        $this->childs->add($child);
    }
}

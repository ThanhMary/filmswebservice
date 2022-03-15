<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\FilmRepository;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * 
 * @ORM\Entity(repositoryClass=FilmRepository::class)
 * @ApiResource(
 *      collectionOperations={"get", "post"},
 *      itemOperations={"get", "put", "delete"},
 * )
 * @ApiFilter(SearchFilter::class, properties={"name": "word_start", "description": "word_start"})
 * 
 */
class Film
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * 
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=128)
     *
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=2048)
     * 
     */
    private $description;

    /**
     * @ORM\Column(type="datetime")
     *
     */
    private $released;

    /**
     * @ORM\Column(type="integer", nullable=true)
     *
     */
    private $note;

    /**
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="films")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id")
     * @ApiSubresource
     * 
     */
    private $category;

    /**
     * @ORM\Column(type="blob", nullable=true)
     */
    private $Image;

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getReleased(): ?\DateTimeInterface
    {
        return $this->released;
    }

    public function setReleased(\DateTimeInterface $released): self
    {
        $this->released = $released;

        return $this;
    }

    public function getNote(): ?int
    {
        return $this->note;
    }

    public function setNote(?int $note): self
    {
        $this->note = $note;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getImage()
    {
        return $this->Image;
    }

    public function setImage($Image): self
    {
        $this->Image = $Image;

        return $this;
    }
}

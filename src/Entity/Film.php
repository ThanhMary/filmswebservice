<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
//use Vich\UploaderBundle\Mapping\Annotation\Uploadable  as Vich;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
/**
 * 
 * @ORM\Entity(repositoryClass=FilmRepository::class)
 * @ApiResource(
 *      collectionOperations={
 *     "get",
 *     "post" = {
 *       "controller" = FilmController::class,
 *       "deserialize" = false,
 *       "openapi_context" = {
 *         "requestBody" = {
 *           "description" = "File upload to an existing resource (Film)",
 *           "required" = true,
 *           "content" = {
 *             "multipart/form-data" = {
 *               "schema" = {
 *                 "type" = "object",
 *                 "properties" = {
 *                   "Name" = {
 *                     "description" = "The name of the movie",
 *                     "type" = "string",
 *                     "example" = "Clark Kent",
 *                   },
 *                     "Description" = {
 *                     "description" = "The description of the movie",
 *                     "type" = "string",
 *                     "example" = "Lorem Loremm.....",
 *                   },
 *                     "Note" = {
 *                     "description" = "The note for the movie",
 *                     "type" = "integer",
 *                     "example" = "1 ou 2...5",
 *                   },
 *                      "Date de released" = {
 *                     "description" = "Date of release of the movie",
 *                     "type" = "integer",
 *                     "example" = "16-03-2022",
 *                   },
 *                  
 *                   "Image" = {
 *                     "type" = "string",
 *                      "format" = "binary",
 *                     "description" = "Upload a cover image of the movie",
 *                   },
 *                 },
 *               },
 *             },
 *           },
 *         },
 *       },
 *     },
 *   },
 *      itemOperations={"get", "put", "delete"},
 * )
 *
 * @ApiFilter(SearchFilter::class, properties={"name": "word_start", "description": "word_start"})
 * @Vich\Uploadable
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
    public $name;

    /**
     * @ORM\Column(type="string", length=2048)
     * 
     */
    public $description;

    /**
     * @ORM\Column(type="datetime")
     *
     */
    public $released;

    /**
     * @ORM\Column(type="integer", nullable=true)
     *
     */
    public $note;

    /**
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="films")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id")
     * @ApiSubresource
     * 
     */
    public $category;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    public $Image;

     /**
     * @Vich\UploadableField(mapping="film_images", fileNameProperty="Image")
     * @var File
     */

    public $imageFile;

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

    public function getImageFile(){
        return $this->imageFile;
    }

    public function setImageFile(File $file = null){

        $this->imageFile = $file;

      
    }

}

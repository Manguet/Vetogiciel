<?php

namespace App\Entity\Contents;

use App\Entity\Structure\Veterinary;
use App\Interfaces\DateTime\EntityDateInterface;
use App\Traits\DateTime\EntityDateTrait;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Contents\ArticleRepository")
 * @ORM\HasLifecycleCallbacks
 * @Vich\Uploadable()
 *
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 */
class Article implements EntityDateInterface
{
    use EntityDateTrait;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $titleUrl;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isActivated;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $priority;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @var string|null
     */
    private $image;

    /**
     * @Vich\UploadableField(mapping="articles", fileNameProperty="image")
     * @Assert\File(
     *      maxSize="1000k",
     *      maxSizeMessage="Le fichier excède 1000Ko.",
     *      mimeTypes={"image/png", "image/jpeg", "image/jpg", "image/gif"},
     *      mimeTypesMessage= "formats autorisés: png, jpeg, jpg, gif"
     * )
     * @var null|File
     */
    private $imageFile;

    /**
     * @ORM\ManyToOne(targetEntity=Veterinary::class, inversedBy="articles")
     */
    private $createdBy;

    /**
     * @ORM\ManyToOne(targetEntity=ArticleCategory::class, inversedBy="article", cascade={"persist"})
     */
    private $articleCategory;

    /**
     * @ORM\OneToMany(targetEntity=Commentary::class, mappedBy="article")
     */
    private $comments;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getTitleUrl(): ?string
    {
        return $this->titleUrl;
    }

    public function setTitleUrl(string $titleUrl): self
    {
        $this->titleUrl = $titleUrl;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getIsActivated(): ?bool
    {
        return $this->isActivated;
    }

    public function setIsActivated(?bool $isActivated): self
    {
        $this->isActivated = $isActivated;

        return $this;
    }

    public function getPriority(): ?int
    {
        return $this->priority;
    }

    public function setPriority(?int $priority): self
    {
        $this->priority = $priority;

        return $this;
    }

    /**
     * @return string|string[]
     *
     * use to decode in front without twig extension and raw ect
     */
    public function getDescriptionDecode()
    {
        $withoutHtml = strip_tags($this->description);

        $baseHtml = ['&agrave;', '&nbsp;', '&#39;', '&eacute;', '&quot;', '&rsquo;', '&egrave;', '&ecirc;', '&acirc;', '&ccedil;', '&hellip;', '&ndash;', '&oelig;', '&Eacute;'];

        $result = ['à', ' ', '\'', 'é', '"', '\'', 'è', 'ê', 'â', 'ç', '...', '-', 'oe', 'E'];

        return str_replace($baseHtml, $result, $withoutHtml);
    }

    public function setImageFile(File $image = null): void
    {
        $this->imageFile = $image;

        if ($image) {
            $this->dateUpdate = new DateTime('now');
        }
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function setImage($image): void
    {
        $this->image = $image;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?Veterinary $createdBy): self
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    public function getArticleCategory(): ?ArticleCategory
    {
        return $this->articleCategory;
    }

    public function setArticleCategory(?ArticleCategory $articleCategory): self
    {
        $this->articleCategory = $articleCategory;

        return $this;
    }

    /**
     * @return Collection|Commentary[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Commentary $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setArticle($this);
        }

        return $this;
    }

    public function removeComment(Commentary $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);

            if ($comment->getArticle() === $this) {
                $comment->setArticle(null);
            }
        }

        return $this;
    }
}

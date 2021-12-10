<?php

namespace App\Entity\Contents;

use App\Interfaces\Structure\ClinicInterface;
use App\Interfaces\User\CreatedByInterface;
use App\Repository\Contents\ArticleCategoryRepository;
use App\Traits\Structure\ClinicTrait;
use App\Traits\User\CreatedByTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=ArticleCategoryRepository::class)
 * @UniqueEntity(fields="title", message="Une catégorie existe déjà avec ce titre.")
 *
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 */
class ArticleCategory implements CreatedByInterface, ClinicInterface
{
    use CreatedByTrait;
    use ClinicTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id;

    /**
     * @ORM\OneToMany(targetEntity=Article::class, mappedBy="articleCategory")
     */
    private $article;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private ?string $title;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private ?string $titleUrl;

    public function __construct()
    {
        $this->article = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection|Article[]
     */
    public function getArticle(): Collection
    {
        return $this->article;
    }

    public function addArticle(Article $article): self
    {
        if (!$this->article->contains($article)) {
            $this->article[] = $article;
            $article->setArticleCategory($this);
        }

        return $this;
    }

    public function removeArticle(Article $article): self
    {
        if ($this->article->contains($article)) {
            $this->article->removeElement($article);
            // set the owning side to null (unless already changed)
            if ($article->getArticleCategory() === $this) {
                $article->setArticleCategory(null);
            }
        }

        return $this;
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
}

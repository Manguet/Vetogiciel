<?php

namespace App\Entity\Contents;

use App\Interfaces\DateTime\EntityDateInterface;
use App\Repository\Contents\CommentRepository;
use App\Traits\DateTime\EntityDateTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 *
 * @ORM\HasLifecycleCallbacks
 * @ORM\Entity(repositoryClass=CommentRepository::class)
 */
class Commentary implements EntityDateInterface
{
    use EntityDateTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity=Article::class, inversedBy="comments")
     */
    private $article;

    /**
     * @ORM\Column(type="string")
     */
    private $createdBy;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getArticle(): ?Article
    {
        return $this->article;
    }

    public function setArticle(?Article $article): self
    {
        $this->article = $article;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * @param mixed $createdBy
     */
    public function setCreatedBy($createdBy): void
    {
        $this->createdBy = $createdBy;
    }
}

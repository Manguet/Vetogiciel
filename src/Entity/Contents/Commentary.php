<?php

namespace App\Entity\Contents;

use App\Interfaces\DateTime\EntityDateInterface;
use App\Interfaces\User\CreatedByWithUserInterface;
use App\Repository\Contents\CommentaryRepository;
use App\Traits\DateTime\EntityDateTrait;
use App\Traits\User\CreatedByWithUserTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 *
 * @ORM\HasLifecycleCallbacks
 * @ORM\Entity(repositoryClass=CommentaryRepository::class)
 */
class Commentary implements EntityDateInterface, CreatedByWithUserInterface
{
    use EntityDateTrait;
    use CreatedByWithUserTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id;

    /**
     * @ORM\Column(type="text")
     */
    private ?string $description;

    /**
     * @ORM\ManyToOne(targetEntity=Article::class, inversedBy="comments")
     */
    private ?Article $article;

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
}

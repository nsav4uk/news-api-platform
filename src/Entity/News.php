<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ApiResource(
 *     normalizationContext={"groups"={"news:read"}},
 *     denormalizationContext={"groups"={"news:write"}},
 *     collectionOperations={
 *          "get",
 *          "post"={"security"="is_granted('ROLE_USER')"}
 *      },
 *     itemOperations={
 *          "get"={"normalization_context"={"groups"={"news:read", "news:comments"}}},
 *          "patch"={
 *              "security"="is_granted('ROLE_MANAGER')",
 *              "denormalization_context"={"groups"={"news:patch"}}
 *          },
 *     }
 * )
 * @ORM\Entity()
 */
class News
{
    public const PENDING_APPROVAL = 'pending approval';
    public const PUBLISHED = 'published';

    const STATUSES = [
        self::PENDING_APPROVAL,
        self::PUBLISHED,
    ];

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private ?int $id;

    /**
     * @ORM\Column(type="string", length=50)
     * @Assert\NotBlank()
     * @Assert\Length(max=50)
     * @Groups({"news:read", "news:write"})
     */
    private ?string $title;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank()
     * @Groups({"news:read", "news:write"})
     */
    private ?string $body;

    /**
     * @ORM\Column(type="string", length=50)
     * @Groups("news:patch")
     * @Assert\Choice(choices="News::STATUSES")
     */
    private string $status = self::PENDING_APPROVAL;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="news")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"news:read"})
     */
    private ?User $author;

    /**
     * @ORM\OneToMany(targetEntity=Comment::class, mappedBy="news")
     * @Groups({"news:comments"})
     */
    private Collection $comments;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return $this
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getBody(): ?string
    {
        return $this->body;
    }

    /**
     * @param string $body
     * @return $this
     */
    public function setBody(string $body): self
    {
        $this->body = $body;

        return $this;
    }

    /**
     * @return User|null
     */
    public function getAuthor(): ?User
    {
        return $this->author;
    }

    /**
     * @param User|null $author
     * @return $this
     */
    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @param string $status
     * @return $this
     */
    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return Comment[]
     */
    public function getComments(): array
    {
        return $this->comments->getValues();
    }

    /**
     * @param Comment $comment
     * @return $this
     */
    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setNews($this);
        }

        return $this;
    }

    /**
     * @param Comment $comment
     * @return $this
     */
    public function removeComment(Comment $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getNews() === $this) {
                $comment->setNews(null);
            }
        }

        return $this;
    }
}

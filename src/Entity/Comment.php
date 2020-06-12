<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ApiResource(
 *     normalizationContext={"groups"={"comment:read"}},
 *     denormalizationContext={"groups"={"comment:write"}},
 *     collectionOperations={"get", "post"},
 *     itemOperations={"get"}
 * )
 * @ORM\Entity()
 */
class Comment
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private ?int $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"news:comments", "comment:read", "comment:write"})
     * @Assert\NotBlank()
     * @Assert\Length(max=255)
     */
    private ?string $comment;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="comments")
     * @Groups({"news:comments"})
     */
    private ?User $sender;

    /**
     * @ORM\ManyToOne(targetEntity=News::class, inversedBy="comments")
     * @ORM\JoinColumn(nullable=false)
     * @Groups("comment:write")
     */
    private ?News $news;

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
    public function getComment(): ?string
    {
        return $this->comment;
    }

    /**
     * @param string $comment
     * @return $this
     */
    public function setComment(string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * @return User|null
     */
    public function getSender(): ?User
    {
        return $this->sender;
    }

    /**
     * @param User|null $sender
     * @return $this
     */
    public function setSender(?User $sender): self
    {
        $this->sender = $sender;

        return $this;
    }

    /**
     * @return News|null
     */
    public function getNews(): ?News
    {
        return $this->news;
    }

    /**
     * @param News|null $news
     * @return $this
     */
    public function setNews(?News $news): self
    {
        $this->news = $news;

        return $this;
    }
}

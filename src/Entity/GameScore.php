<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiProperty;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Doctrine\Orm\Filter\RangeFilter;
use Symfony\Component\Uid\Ulid;
use App\Entity\User;

#[ORM\Entity]
#[ApiResource(
    operations: [
        new Post(
            uriTemplate: '/scores/add',
            description: 'Création d\'une partie',
            security: "is_granted('ROLE_USER')"
        ),
        new GetCollection(
            uriTemplate: '/scores',
            description: 'Récupération des parties'
        )
    ],
    normalizationContext: ['groups' => ['game:read']],
    denormalizationContext: ['groups' => ['game:write']]
)]
#[ApiFilter(OrderFilter::class, properties: ['score' => 'DESC', 'date' => 'ASC'])]
#[ApiFilter(SearchFilter::class, properties: ['user.uid' => 'exact'])]
#[ApiFilter(RangeFilter::class, properties: ['score'])]
class GameScore
{
    #[ORM\Id]
    #[ORM\Column(type: 'ulid', unique: true)]
    #[ApiProperty(identifier: true)]
    #[Groups(['game:read'])]
    private ?Ulid $uid = null;

    #[ORM\Column(type: 'integer')]
    #[Groups(['game:read', 'game:write'])]
    private int $score;

    #[ORM\Column(type: 'datetime')]
    #[Groups(['game:read', 'game:write'])]
    private \DateTimeInterface $date;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: "user_uid", referencedColumnName: "uid", nullable: true)]
    #[Groups(['game:read'])]
    private ?User $user = null;

    public function __construct()
    {
        $this->uid = new Ulid();
        $this->date = new \DateTimeImmutable();
    }

    public function getUid(): ?Ulid
    {
        return $this->uid;
    }

    public function setUid(Ulid $uid): void
    {
        $this->uid = $uid;
    }

    public function getScore(): int
    {
        return $this->score;
    }

    public function setScore(int $score): void
    {
        $this->score = $score;
    }

    public function getDate(): \DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): void
    {
        $this->date = $date;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): void
    {
        $this->user = $user;
    }
}

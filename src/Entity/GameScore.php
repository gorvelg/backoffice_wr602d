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
            description: 'Récupération des parties',
            // security: "is_granted('ROLE_USER')"
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
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[ApiProperty(identifier: false)]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255, unique: true)]
    #[ApiProperty(identifier: true)]
    #[Groups(['game:read'])]
    private string $uid;

    #[ORM\Column(type: 'integer')]
    #[Groups(['game:read', 'game:write'])]
    private int $score;

    #[ORM\Column(type: 'datetime')]
    #[Groups(['game:read', 'game:write'])]
    private \DateTimeInterface $date;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[Groups(['game:read', 'game:write'])]
    private ?User $user = null;

    public function __construct()
    {
        $this->uid = (new Ulid())->toBase32();
        $this->date = new \DateTimeImmutable();
    }

    /**
     * @return \DateTimeInterface
     */
    public function getDate(): \DateTimeInterface
    {
        return $this->date;
    }

    /**
     * @param \DateTimeInterface $date
     */
    public function setDate(\DateTimeInterface $date): void
    {
        $this->date = $date;
    }

    /**
     * @return string
     */
    public function getUid(): string
    {
        return $this->uid;
    }

    /**
     * @param string $uid
     */
    public function setUid(string $uid): void
    {
        $this->uid = $uid;
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int|null $id
     */
    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getScore(): int
    {
        return $this->score;
    }

    /**
     * @param int $score
     */
    public function setScore(int $score): void
    {
        $this->score = $score;
    }

    /**
     * @return User|null
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * @param User|null $user
     */
    public function setUser(?User $user): void
    {
        $this->user = $user;
    }

}

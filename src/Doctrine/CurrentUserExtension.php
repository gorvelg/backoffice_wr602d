<?php

namespace App\Doctrine;

use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use App\Entity\GameScore;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\SecurityBundle\Security;

final class CurrentUserExtension implements QueryCollectionExtensionInterface, QueryItemExtensionInterface
{
    public function __construct(
        private Security $security,
    ) {}

    public function applyToCollection(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        Operation $operation = null,
        array $context = []
    ): void {
        $this->addWhere($queryBuilder, $resourceClass, $operation);
    }

    public function applyToItem(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        array $identifiers,
        Operation $operation = null,
        array $context = []
    ): void {
        $this->addWhere($queryBuilder, $resourceClass, $operation);
    }

    private function addWhere(
        QueryBuilder $queryBuilder,
        string $resourceClass,
        ?Operation $operation = null
    ): void {
        if (GameScore::class !== $resourceClass) {
            return;
        }

        $user = $this->security->getUser();

        // Ne filtre PAS si admin, si utilisateur non connecté ou si on est sur l'opération publique
        if (
            $this->security->isGranted('ROLE_ADMIN') ||
            !$user ||
            ($operation && $operation->getName() === 'public_scores')
        ) {
            return;
        }

        // Join explicite pour accéder à user.uid
        $rootAlias = $queryBuilder->getRootAliases()[0];

        $queryBuilder
            ->join(sprintf('%s.user', $rootAlias), 'u')
            ->andWhere('u.uid = :current_user_uid')
            ->setParameter('current_user_uid', $user->getUid(), 'ulid');
    }
}

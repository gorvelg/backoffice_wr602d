<?php

namespace App\EventListener;

use App\Entity\GameScore;
use Symfony\Component\Security\Core\Security;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\EventDispatcher\Attribute\AsEntityListener;

#[AsEntityListener(event: Events::PRE_PERSIST, method: 'prePersist', entity: GameScore::class)]
class GameScoreListener
{
    public function __construct(private Security $security)
    {
    }

    public function prePersist(GameScore $gameScore, PrePersistEventArgs $event): void
    {
        if (!$this->security->getUser()) {
            return;  // Si aucun utilisateur connecté, on n'associe pas d'utilisateur
        }

        $gameScore->setUser($this->security->getUser());
    }
}

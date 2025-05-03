<?php

namespace App\EventListener;

use App\Entity\GameScore;
use Symfony\Bundle\SecurityBundle\Security;
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
        dump('PRE_PERSIST déclenché');
        dump($this->security->getUser());
        die();

        if (!$this->security->getUser()) {
            return;
        }

        $gameScore->setUser($this->security->getUser());
    }
}

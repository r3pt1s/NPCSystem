<?php

namespace NPCSystem\session;

use NPCSystem\NPCSystem;
use NPCSystem\task\RemoveSessionTask;
use pocketmine\player\Player;

class Session {

    private static self $instance;
    /** @var array<Player> */
    private array $entityWaitSessions = [];

    public function __construct() {
        self::$instance = $this;
    }

    public function addToEntityWaitSession(Player $player) {
        $this->entityWaitSessions[$player->getName()] = $player;
        NPCSystem::getInstance()->getScheduler()->scheduleDelayedTask(new RemoveSessionTask($player), 15);
    }

    public function removeFromEntityWaitSession(Player $player) {
        unset($this->entityWaitSessions[$player->getName()]);
    }

    public function hasEntityWaitSession(Player $player): bool {
        return isset($this->entityWaitSessions[$player->getName()]);
    }

    public function getEntityWaitSessions(): array {
        return $this->entityWaitSessions;
    }

    public static function getInstance(): Session {
        return self::$instance;
    }
}
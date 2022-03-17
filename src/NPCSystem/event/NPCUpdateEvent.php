<?php

namespace NPCSystem\event;

use NPCSystem\npc\NPC;
use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;
use pocketmine\event\Event;

class NPCUpdateEvent extends Event implements Cancellable {
    use CancellableTrait;

    private NPC $npc;
    private string $updateKey;
    private mixed $updateValue;

    public function __construct(NPC $npc, string $updateKey, mixed $oldValue, mixed $updateValue) {
        $this->npc = $npc;
        $this->updateKey = $updateKey;
        $this->updateValue = $updateValue;
    }

    public function getNPC(): NPC {
        return $this->npc;
    }

    public function getUpdateKey(): string {
        return $this->updateKey;
    }

    public function getUpdateValue(): mixed {
        return $this->updateValue;
    }
}
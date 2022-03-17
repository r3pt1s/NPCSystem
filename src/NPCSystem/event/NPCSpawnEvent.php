<?php

namespace NPCSystem\event;

use NPCSystem\npc\NPC;
use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;
use pocketmine\event\Event;

class NPCSpawnEvent extends Event implements Cancellable {
    use CancellableTrait;

    private NPC $npc;

    public function __construct(NPC $npc) {
        $this->npc = $npc;
    }

    public function getNPC(): NPC {
        return $this->npc;
    }
}
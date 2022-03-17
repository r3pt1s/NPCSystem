<?php

namespace NPCSystem\npc;

use NPCSystem\npc\data\NPCData;
use pocketmine\entity\Human;

class NPC {

    private string $identifier;
    private NPCData $data;
    private ?Human $entity = null;

    public function __construct(string $identifier, NPCData $data) {
        $this->identifier = $identifier;
        $this->data = $data;
    }

    public function getIdentifier(): string {
        return $this->identifier;
    }

    public function getData(): NPCData {
        return $this->data;
    }

    public function getEntity(): ?Human {
        return $this->entity;
    }

    public function setEntity(?Human $entity): void {
        $this->entity = $entity;
    }
}
<?php

namespace NPCSystem\task;

use NPCSystem\session\Session;
use pocketmine\player\Player;
use pocketmine\scheduler\Task;

class RemoveSessionTask extends Task {

    private Player $player;

    public function __construct(Player $player) {
        $this->player = $player;
    }

    public function onRun(): void {
        if (Session::getInstance()->hasEntityWaitSession($this->player)) Session::getInstance()->removeFromEntityWaitSession($this->player);
    }
}
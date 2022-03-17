<?php

namespace NPCSystem\form\subForm;

use dktapps\pmforms\MenuForm;
use dktapps\pmforms\MenuOption;
use NPCSystem\form\MainForm;
use NPCSystem\npc\NPCManager;
use pocketmine\player\Player;

class ListSubForm extends MenuForm {

    private array $options = [];
    private array $identifiers = [];

    public function __construct(string $message = "") {
        foreach (NPCManager::getInstance()->getNPCs() as $npc) {
            $this->identifiers[] = $npc->getIdentifier();
            $this->options[] = new MenuOption($npc->getIdentifier());
        }
        
        if (count($this->identifiers) == 0) $this->options[] = new MenuOption("§cNo NPCs were found!");

        parent::__construct("§8× §l§6NPCSystem §r§8| §l§eList all NPCs §r§8×", $message, $this->options, function (Player $player, int $data): void {
            if (isset($this->identifiers[$data])) {
                if (($npc = NPCManager::getInstance()->getNPC($this->identifiers[$data])) !== null) {
                    $player->sendForm(new MainForm());
                } else {
                    $player->sendForm(new self("§cThe choosen NPC doesn't exists!"));
                }
            } else {
                $player->sendForm(new self("§cThe choosen NPC doesn't exists!"));
            }
        }, function (Player $player): void {
            $player->sendForm(new MainForm());
        });
    }
}
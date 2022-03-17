<?php

namespace NPCSystem\form\subForm;

use dktapps\pmforms\CustomForm;
use dktapps\pmforms\CustomFormResponse;
use dktapps\pmforms\element\Dropdown;
use dktapps\pmforms\element\Label;
use NPCSystem\form\MainForm;
use NPCSystem\npc\NPCManager;
use NPCSystem\NPCSystem;
use pocketmine\player\Player;

class RemoveSubForm extends CustomForm {

    private array $elements = [];
    private array $identifiers = [];

    public function __construct(string $message = "") {
        if ($message !== "") $this->elements[] = new Label("message", $message);

        foreach (NPCManager::getInstance()->getNPCs() as $npc) {
            $this->identifiers[] = $npc->getIdentifier();
        }

        $this->elements[] = new Dropdown("npcIdentifier", "§7Choose a npc:", (count($this->identifiers) > 0 ? $this->identifiers : ["§cNo NPCs were found!"]));

        parent::__construct("§8× §l§6NPCSystem §r§8| §l§cRemove a NPC §r§8×", $this->elements, function (Player $player, CustomFormResponse $response): void {
            if (isset($this->identifiers[$response->getInt("npcIdentifier")])) {
                if (($npc = NPCManager::getInstance()->getNPC($this->identifiers[$response->getInt("npcIdentifier")])) !== null) {
                    if (NPCManager::getInstance()->removeNPC($npc)) {
                        $player->sendMessage(NPCSystem::getPrefix() . "The NPC was successfully removed!");
                        NPCManager::getInstance()->removeNPC($npc);
                        NPCManager::getInstance()->despawnNPC($npc);
                    } else {
                        $player->sendMessage(NPCSystem::getPrefix() . "§cThe NPC can't be removed!");
                    }
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
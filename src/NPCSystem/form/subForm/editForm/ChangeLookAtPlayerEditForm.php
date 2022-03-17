<?php

namespace NPCSystem\form\subForm\editForm;

use dktapps\pmforms\CustomFormResponse;
use dktapps\pmforms\element\Label;
use dktapps\pmforms\element\Toggle;
use NPCSystem\npc\NPC;
use NPCSystem\npc\NPCManager;
use NPCSystem\NPCSystem;
use pocketmine\player\Player;

class ChangeLookAtPlayerEditForm extends EditForm {

    public function __construct(NPC $npc, bool $openWithRightClick = false, string $message = "") {
        if ($message !== "") $this->elements[] = new Label("message", $message);

        $this->elements[] = new Toggle("lookAtPlayer", "§7Look at the player?", $npc->getData()->isLookAtPlayer());

        parent::__construct($npc, $openWithRightClick, "§8× §l§6NPCSystem §r§8| §l§6Edit a NPC §r§8×", $this->elements, function (Player $player, CustomFormResponse $response): void {
            $lookAtPlayer = $response->getBool("lookAtPlayer");
            if (NPCManager::getInstance()->updateNPC($this->npc, NPCManager::KEY_LOOK_AT_PLAYER, $lookAtPlayer)) {
                $player->sendMessage(NPCSystem::getPrefix() . "Look at player was successfully changed!");
                $player->sendForm(new \NPCSystem\form\subForm\EditForm($this->npc, $this->openWithRightClick));
            } else $player->sendForm(new self($this->npc, $this->openWithRightClick, "§cLook at player can't be changed!"));
        });
    }
}
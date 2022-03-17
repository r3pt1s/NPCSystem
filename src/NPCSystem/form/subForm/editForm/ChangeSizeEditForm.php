<?php

namespace NPCSystem\form\subForm\editForm;

use dktapps\pmforms\CustomFormResponse;
use dktapps\pmforms\element\Label;
use dktapps\pmforms\element\Slider;
use NPCSystem\npc\NPC;
use NPCSystem\npc\NPCManager;
use NPCSystem\NPCSystem;
use pocketmine\player\Player;

class ChangeSizeEditForm extends EditForm {

    public function __construct(NPC $npc, bool $openWithRightClick, string $message = "") {
        if ($message !== "") $this->elements[] = new Label("message", $message);

        $this->elements[] = new Slider("size", "§7Size", 1, 50, 1.0, $npc->getData()->getSize());

        parent::__construct($npc, $openWithRightClick, "§8× §l§6NPCSystem §r§8| §l§6Edit a NPC §r§8×", $this->elements, function (Player $player, CustomFormResponse $response): void {
            $size = $response->getFloat("size");
            if (NPCManager::getInstance()->updateNPC($this->npc, NPCManager::KEY_SIZE, $size)) {
                $player->sendMessage(NPCSystem::getPrefix() . "The size was successfully changed!");
                $player->sendForm(new \NPCSystem\form\subForm\EditForm($this->npc, $this->openWithRightClick));
            } else $player->sendForm(new self($this->npc, $this->openWithRightClick, "§cThe size can't be changed!"));
        });
    }
}
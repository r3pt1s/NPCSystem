<?php

namespace NPCSystem\form\subForm\editForm;

use dktapps\pmforms\CustomFormResponse;
use dktapps\pmforms\element\Input;
use dktapps\pmforms\element\Label;
use NPCSystem\cache\Cache;
use NPCSystem\npc\NPC;
use NPCSystem\npc\NPCManager;
use NPCSystem\NPCSystem;
use pocketmine\player\Player;

class ChangeSkinEditForm extends EditForm {

    public function __construct(NPC $npc, bool $openWithRightClick = false, string $message = "") {
        if ($message !== "") $this->elements[] = new Label("message", $message);

        $this->elements[] = new Input("skin", "§7Skin:", "r3pt1s");

        parent::__construct($npc, $openWithRightClick, "§8× §l§6NPCSystem §r§8| §l§6Edit a NPC §r§8×", $this->elements, function (Player $player, CustomFormResponse $response): void {
            $skin = $response->getString("skin");
            if ($skin !== "") {
                if (Cache::getInstance()->isInSkinCache($skin)) {
                    if (NPCManager::getInstance()->updateNPC($this->npc, NPCManager::KEY_SKIN, $skin)) {
                        $player->sendMessage(NPCSystem::getPrefix() . "The skin was successfully changed!");
                        $player->sendForm(new \NPCSystem\form\subForm\EditForm($this->npc, $this->openWithRightClick));
                    } else $player->sendForm(new self($this->npc, $this->openWithRightClick, "§cThe skin can't be changed!"));
                } else $player->sendForm(new self($this->npc, $this->openWithRightClick, "§cThe skin of the provided player doesn't exists!"));
            } else $player->sendForm(new self($this->npc, $this->openWithRightClick, "§cPlease provide a skin (player)!"));
        });
    }
}
<?php

namespace NPCSystem\form\subForm\editForm;

use dktapps\pmforms\CustomFormResponse;
use dktapps\pmforms\element\Input;
use dktapps\pmforms\element\Label;
use NPCSystem\npc\NPC;
use NPCSystem\npc\NPCManager;
use NPCSystem\NPCSystem;
use pocketmine\player\Player;

class ChangeNameTagEditForm extends EditForm {
    
    public function __construct(NPC $npc, bool $openWithRightClick = false, string $message = "") {
        if ($message !== "") $this->elements[] = new Label("message", $message);

        $this->elements[] = new Input("nametag", "§7NameTag: §8(§cMax. 48 characters§8)", "123");

        parent::__construct($npc, $openWithRightClick, "§8× §l§6NPCSystem §r§8| §l§6Edit a NPC §r§8×", $this->elements, function (Player $player, CustomFormResponse $response): void {
            $nameTag = $response->getString("nametag");
            if ($nameTag !== "") {
                if (strlen($nameTag) <= 48) {
                    if (NPCManager::getInstance()->updateNPC($this->npc, NPCManager::KEY_NAME_TAG, $nameTag)) {
                        $player->sendMessage(NPCSystem::getPrefix() . "The nametag was successfully changed!");
                        $player->sendForm(new \NPCSystem\form\subForm\EditForm($this->npc, $this->openWithRightClick));
                    } else $player->sendForm(new self($this->npc, $this->openWithRightClick, "§cThe nametag can't be changed!"));
                } else $player->sendForm(new self($this->npc, $this->openWithRightClick, "§cThe nametag is too long!"));
            } else $player->sendForm(new self($this->npc, $this->openWithRightClick, "§cPlease provide a nametag!"));
        });
    }
}
<?php

namespace NPCSystem\form\subForm\editForm;

use dktapps\pmforms\CustomFormResponse;
use dktapps\pmforms\element\Input;
use dktapps\pmforms\element\Label;
use NPCSystem\npc\NPC;
use NPCSystem\npc\NPCManager;
use NPCSystem\NPCSystem;
use pocketmine\player\Player;

class ChangeChestplateEditForm extends EditForm {

    public function __construct(NPC $npc, bool $openWithRightClick = false, string $message = "") {
        if ($message !== "") $this->elements[] = new Label("message", $message);

        $this->elements[] = new Input("itemId", "§7ItemID:", "0");
        $this->elements[] = new Input("itemMeta", "§7ItemMeta:", "0", "0");

        parent::__construct($npc, $openWithRightClick, "§8× §l§6NPCSystem §r§8| §l§6Edit a NPC §r§8×", $this->elements, function (Player $player, CustomFormResponse $response): void {
            $itemId = $response->getString("itemId");
            $itemMeta = $response->getString("itemMeta");
            if ($itemId !== "") {
                if ($itemMeta !== "") {
                    if (NPCManager::getInstance()->updateNPC($this->npc, NPCManager::KEY_CHESTPLATE, $itemId . ":" . $itemMeta)) {
                        $player->sendMessage(NPCSystem::getPrefix() . "The chestplate was successfully changed!");
                        $player->sendForm(new \NPCSystem\form\subForm\EditForm($this->npc, $this->openWithRightClick));
                    } else $player->sendForm(new self($this->npc, $this->openWithRightClick, "§cThe chestplate can't be changed!"));
                } else $player->sendForm(new self($this->npc, $this->openWithRightClick, "§cPlease provide a item meta!"));
            } else $player->sendForm(new self($this->npc, $this->openWithRightClick, "§cPlease provide a item id!"));
        });
    }
}
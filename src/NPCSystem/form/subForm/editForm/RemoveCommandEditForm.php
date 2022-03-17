<?php

namespace NPCSystem\form\subForm\editForm;

use dktapps\pmforms\CustomFormResponse;
use dktapps\pmforms\element\Input;
use dktapps\pmforms\element\Label;
use NPCSystem\npc\NPC;
use NPCSystem\npc\NPCManager;
use NPCSystem\NPCSystem;
use pocketmine\player\Player;

class RemoveCommandEditForm extends EditForm {

    public function __construct(NPC $npc, bool $openWithRightClick, string $message = "") {
        if ($message !== "") $this->elements[] = new Label("message", $message);

        $this->elements[] = new Input("command", "§7Command: §8(§cWITHOUT §e/§c!§8)", "");

        parent::__construct($npc, $openWithRightClick, "§8× §l§6NPCSystem §r§8| §l§6Edit a NPC §r§8×", $this->elements, function (Player $player, CustomFormResponse $response): void {
            $command = $response->getString("command");
            if ($command !== "") {
                $commands = $this->npc->getData()->getCommands();
                if (in_array($command, $commands)) unset($commands[array_search($command, $commands)]);
                if (NPCManager::getInstance()->updateNPC($this->npc, NPCManager::KEY_COMMANDS, $commands)) {
                    $player->sendMessage(NPCSystem::getPrefix() . "The command was successfully removed!");
                    $player->sendForm(new \NPCSystem\form\subForm\EditForm($this->npc, $this->openWithRightClick));
                } else $player->sendForm(new self($this->npc, $this->openWithRightClick, "§cThe command can't be removed!"));
            } else $player->sendForm(new self($this->npc, $this->openWithRightClick, "§cPlease provide a command!"));
        });
    }
}
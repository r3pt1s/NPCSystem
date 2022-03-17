<?php

namespace NPCSystem\form\subForm\editForm;

use dktapps\pmforms\MenuForm;
use dktapps\pmforms\MenuOption;
use NPCSystem\npc\NPC;
use pocketmine\player\Player;

class ListCommandsEditForm extends MenuForm {

    private array $options = [];
    private array $commands = [];

    public function __construct(NPC $npc, bool $openWithRightClick) {
        foreach ($npc->getData()->getCommands() as $command) {
            $this->options[] = new MenuOption("§e" . $command);
            $this->commands[] = $command;
        }

        if (count($this->commands) == 0) $this->options[] = new MenuOption("§cNo Commands were found!");

        parent::__construct("§8× §l§6NPCSystem §r§8| §l§6Edit a NPC §r§8×", "", $this->options, function (Player $player, int $data) use($npc, $openWithRightClick): void {
            $player->sendForm(new \NPCSystem\form\subForm\EditForm($npc, $openWithRightClick));
        }, function (Player $player) use($npc, $openWithRightClick): void {
            $player->sendForm(new \NPCSystem\form\subForm\EditForm($npc, $openWithRightClick));
        });
    }
}
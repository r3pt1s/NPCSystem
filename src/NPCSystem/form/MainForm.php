<?php

namespace NPCSystem\form;

use dktapps\pmforms\MenuForm;
use dktapps\pmforms\MenuOption;
use NPCSystem\form\subForm\CreateSubForm;
use NPCSystem\form\subForm\EditSelectSubForm;
use NPCSystem\form\subForm\ListSubForm;
use NPCSystem\form\subForm\RemoveSubForm;
use pocketmine\player\Player;

class MainForm extends MenuForm {

    private array $options = [];

    public function __construct() {
        $this->options[] = new MenuOption("§8× §aCreate a NPC");
        $this->options[] = new MenuOption("§8× §cRemove a NPC");
        $this->options[] = new MenuOption("§8× §6Edit a NPC");
        $this->options[] = new MenuOption("§8× §eList all NPCs");

        parent::__construct("§8× §l§6NPCSystem §r§8×", "", $this->options, function (Player $player, int $data): void {
            if ($data == 0) $player->sendForm(new CreateSubForm());
            else if ($data == 1) $player->sendForm(new RemoveSubForm());
            else if ($data == 2) $player->sendForm(new EditSelectSubForm());
            else if ($data == 3) $player->sendForm(new ListSubForm());
        });
    }
}
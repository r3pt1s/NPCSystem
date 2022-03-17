<?php

namespace NPCSystem\form\subForm\editForm;

use dktapps\pmforms\CustomForm;
use NPCSystem\npc\NPC;
use pocketmine\player\Player;

abstract class EditForm extends CustomForm {

    public NPC $npc;
    public bool $openWithRightClick;
    public array $elements = [];

    public function __construct(NPC $npc, bool $openWithRightClick, string $title, array $elements, \Closure $onSubmit) {
        $this->npc = $npc;
        $this->openWithRightClick = $openWithRightClick;
        parent::__construct($title, $elements, $onSubmit, function (Player $player): void {
            $player->sendForm(new \NPCSystem\form\subForm\EditForm($this->npc, $this->openWithRightClick));
        });
    }
}
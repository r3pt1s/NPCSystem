<?php

namespace NPCSystem\form\subForm;

use dktapps\pmforms\MenuForm;
use dktapps\pmforms\MenuOption;
use NPCSystem\form\MainForm;
use NPCSystem\form\subForm\editForm\AddCommandEditForm;
use NPCSystem\form\subForm\editForm\ChangeBootsEditForm;
use NPCSystem\form\subForm\editForm\ChangeChestplateEditForm;
use NPCSystem\form\subForm\editForm\ChangeHelmetEditForm;
use NPCSystem\form\subForm\editForm\ChangeItemEditForm;
use NPCSystem\form\subForm\editForm\ChangeLeggingsEditForm;
use NPCSystem\form\subForm\editForm\ChangeLookAtPlayerEditForm;
use NPCSystem\form\subForm\editForm\ChangeNameTagEditForm;
use NPCSystem\form\subForm\editForm\ChangeSizeEditForm;
use NPCSystem\form\subForm\editForm\ChangeSkinEditForm;
use NPCSystem\form\subForm\editForm\ListCommandsEditForm;
use NPCSystem\form\subForm\editForm\RemoveCommandEditForm;
use NPCSystem\npc\NPC;
use NPCSystem\npc\NPCManager;
use NPCSystem\NPCSystem;
use NPCSystem\util\Util;
use pocketmine\item\ItemIds;
use pocketmine\player\Player;

class EditForm extends MenuForm {

    private NPC $npc;
    private array $options = [];

    public function __construct(NPC $npc, bool $openWithRightClick = false, string $message = "") {
        $this->npc = $npc;

        $text = "";

        if ($message !== "") $text .= $message . "\n\n";
        $text .= "§8× §7NameTag: §r" . $npc->getData()->getNameTag() . "\n";
        $text .= "§8× §7Commands: §e" . count($npc->getData()->getCommands()) . "\n";
        $text .= "§8× §7Size: §e" . $npc->getData()->getSize() . "\n";
        $text .= "§8× §7Skin: §e" . $npc->getData()->getSkin() . "\n";
        $text .= "§8× §7LookAtPlayer: §e" . $this->boolToString($npc->getData()->isLookAtPlayer()) . "\n";
        $text .= "§8× §7Location: §e" . str_replace(":", "§8, §e", Util::vectorToString($npc->getData()->getLocation()->asVector3())) . " §8(§e" . $npc->getData()->getLocation()->getWorld()->getFolderName() . "§8)\n";
        $text .= "§8× §7ItemInHand: §e" . $npc->getData()->getItemInHand()->getName() . " §8(§e" . Util::itemToString($npc->getData()->getItemInHand()) . "§8)\n";
        $text .= "§8× §7Helmet: §e" . $npc->getData()->getHelmet()->getName() . " §8(§e" . Util::itemToString($npc->getData()->getHelmet()) . "§8)\n";
        $text .= "§8× §7Chestplate: §e" . $npc->getData()->getChestplate()->getName() . " §8(§e" . Util::itemToString($npc->getData()->getChestplate()) . "§8)\n";
        $text .= "§8× §7Leggings: §e" . $npc->getData()->getLeggings()->getName() . " §8(§e" . Util::itemToString($npc->getData()->getLeggings()) . "§8)\n";
        $text .= "§8× §7Boots: §e" . $npc->getData()->getBoots()->getName() . " §8(§e" . Util::itemToString($npc->getData()->getBoots()) . "§8)\n";
        $text .= "§8× §7Creator: §e" . $npc->getData()->getCreator();

        $this->options[] = new MenuOption("§8× §aSpawn");
        $this->options[] = new MenuOption("§8× §cDespawn");
        $this->options[] = new MenuOption("§8× §eRespawn");
        $this->options[] = new MenuOption("§8× §7Change NameTag");
        $this->options[] = new MenuOption("§8× §7Add Command");
        $this->options[] = new MenuOption("§8× §7Remove Command");
        $this->options[] = new MenuOption("§8× §7List all Commands");
        $this->options[] = new MenuOption("§8× §7Change Size");
        $this->options[] = new MenuOption("§8× §7Change Skin");
        $this->options[] = new MenuOption("§8× §7Change LookAtPlayer");
        $this->options[] = new MenuOption("§8× §7Change ItemInHand");
        $this->options[] = new MenuOption("§8× §7Change Helmet");
        $this->options[] = new MenuOption("§8× §7Change Chestplate");
        $this->options[] = new MenuOption("§8× §7Change Leggings");
        $this->options[] = new MenuOption("§8× §7Change Boots");
        $this->options[] = new MenuOption("§8× §4Remove");

        parent::__construct("§8× §l§6NPCSystem §r§8| §l§6Edit a NPC §r§8×", $text, $this->options, function (Player $player, int $data) use($openWithRightClick): void {
            if ($data == 0) {
                if (NPCManager::getInstance()->spawnNPC($this->npc, $error)) {
                    $player->sendMessage(NPCSystem::getPrefix() . "The NPC was successfully spawned!");
                    $player->sendForm(new self($this->npc, $openWithRightClick));
                } else $player->sendForm(new self($this->npc, $openWithRightClick, "§c" . $error));
            } else if ($data == 1) {
                if (NPCManager::getInstance()->despawnNPC($this->npc, $error)) {
                    $player->sendMessage(NPCSystem::getPrefix() . "The NPC was successfully despawned!");
                    $player->sendForm(new self($this->npc, $openWithRightClick));
                } else $player->sendForm(new self($this->npc, $openWithRightClick, "§c" . $error));
            } else if ($data == 2) {
                if (NPCManager::getInstance()->respawnNPC($this->npc, $error)) {
                    $player->sendMessage(NPCSystem::getPrefix() . "The NPC was successfully respawned!");
                    $player->sendForm(new self($this->npc, $openWithRightClick));
                } else $player->sendForm(new self($this->npc, $openWithRightClick, "§c" . $error));
            } else if ($data == 3) {
                $player->sendForm(new ChangeNameTagEditForm($this->npc, $openWithRightClick));
            } else if ($data == 4) {
                $player->sendForm(new AddCommandEditForm($this->npc, $openWithRightClick));
            } else if ($data == 5) {
                $player->sendForm(new RemoveCommandEditForm($this->npc, $openWithRightClick));
            } else if ($data == 6) {
                $player->sendForm(new ListCommandsEditForm($this->npc, $openWithRightClick));
            } else if ($data == 7) {
                $player->sendForm(new ChangeSizeEditForm($this->npc, $openWithRightClick));
            } else if ($data == 8) {
                $player->sendForm(new ChangeSkinEditForm($this->npc, $openWithRightClick));
            } else if ($data == 9) {
                $player->sendForm(new ChangeLookAtPlayerEditForm($this->npc, $openWithRightClick));
            } else if ($data == 10) {
                $player->sendForm(new ChangeItemEditForm($this->npc, $openWithRightClick));
            } else if ($data == 11) {
                $player->sendForm(new ChangeHelmetEditForm($this->npc, $openWithRightClick));
            } else if ($data == 12) {
                $player->sendForm(new ChangeChestplateEditForm($this->npc, $openWithRightClick));
            } else if ($data == 13) {
                $player->sendForm(new ChangeLeggingsEditForm($this->npc, $openWithRightClick));
            } else if ($data == 14) {
                $player->sendForm(new ChangeBootsEditForm($this->npc, $openWithRightClick));
            } else if ($data == 15) {
                if (NPCManager::getInstance()->removeNPC($this->npc)) {
                    $player->sendMessage(NPCSystem::getPrefix() . "The NPC was successfully removed!");
                    NPCManager::getInstance()->removeNPC($this->npc);
                    NPCManager::getInstance()->despawnNPC($this->npc);
                    $player->sendForm(new MainForm());
                } else {
                    $player->sendForm(new self($this->npc, $openWithRightClick, "§cThe NPC can't be removed!"));
                }
            }
        }, function (Player $player) use($openWithRightClick): void {
            if (!$openWithRightClick) $player->sendForm(new MainForm());
        });
    }

    private function boolToString(bool $v): string {
        if ($v) return "§aON";
        else return "§cOFF";
    }
}
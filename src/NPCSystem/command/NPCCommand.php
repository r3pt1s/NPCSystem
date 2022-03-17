<?php

namespace NPCSystem\command;

use NPCSystem\form\MainForm;
use NPCSystem\NPCSystem;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\lang\Translatable;
use pocketmine\player\Player;

class NPCCommand extends Command {

    public function __construct(string $name, Translatable|string $description = "", Translatable|string|null $usageMessage = null, array $aliases = [])
    {
        parent::__construct($name, $description, $usageMessage, $aliases);
        $this->setPermission("npc.command");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): bool {
        if ($sender instanceof Player) {
            if ($sender->hasPermission($this->getPermission())) {
                $sender->sendForm(new MainForm());
            } else {
                $sender->sendMessage(NPCSystem::getPrefix() . "§cYou don't have the permission to use this command!");
            }
        }
        return true;
    }
}
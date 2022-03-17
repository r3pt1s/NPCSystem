<?php

namespace NPCSystem\form\subForm;

use dktapps\pmforms\CustomForm;
use dktapps\pmforms\CustomFormResponse;
use dktapps\pmforms\element\Input;
use dktapps\pmforms\element\Label;
use dktapps\pmforms\element\Toggle;
use NPCSystem\form\MainForm;
use NPCSystem\npc\data\NPCData;
use NPCSystem\npc\NPC;
use NPCSystem\npc\NPCManager;
use NPCSystem\NPCSystem;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;

class CreateSubForm extends CustomForm {

    private array $elements = [];

    public function __construct(string $message = "") {
        if ($message !== "") $this->elements[] = new Label("message", $message);
        $this->elements[] = new Input("id", "§7Identifier: §8(§cMax. 16 characters§8)", "123");
        $this->elements[] = new Input("nameTag", "§7NameTag: §8(§cMax. 48 characters§8)", "123");
        $this->elements[] = new Toggle("spawnInstant", "§7Spawn Instant after creation?", true);
        $this->elements[] = new Toggle("lookAtPlayer", "§7Look at the player?", true);

        parent::__construct("§8× §l§6NPCSystem §r§8| §l§aCreate a NPC §r§8×", $this->elements, function (Player $player, CustomFormResponse $response): void {
            $identifier = $response->getString("id");
            $nameTag = $response->getString("nameTag");
            $spawnInstant = $response->getBool("spawnInstant");
            $lookAtPlayer = $response->getBool("lookAtPlayer");

            if ($identifier !== "") {
                if ($nameTag !== "") {
                    if (strlen($identifier) <= 16) {
                        if (strlen($nameTag) <= 48) {
                            if (!NPCManager::getInstance()->npcExists($identifier)) {
                                if (NPCManager::getInstance()->createNPC($npc = new NPC($identifier, new NPCData($nameTag, [], 1, null, $lookAtPlayer,$player->getLocation(), VanillaItems::AIR(), VanillaItems::AIR(), VanillaItems::AIR(), VanillaItems::AIR(), VanillaItems::AIR(), $player->getName())))) {
                                    $player->sendMessage(NPCSystem::getPrefix() . "The NPC was successfully created!");
                                    if ($spawnInstant) NPCManager::getInstance()->spawnNPC($npc);
                                } else {
                                    $player->sendMessage(NPCSystem::getPrefix() . "§cThe NPC can't be created!");
                                }
                            } else {
                                $player->sendForm(new self("§cAn npc with this identifier already exists!"));
                            }
                        } else {
                            $player->sendForm(new self("§cThe nametag is too long!"));
                        }
                    } else {
                        $player->sendForm(new self("§cThe identifier is too long!"));
                    }
                } else {
                    $player->sendForm(new self("§cPlease provide a nametag!"));
                }
            } else {
                $player->sendForm(new self("§cPlease provide a identifier!"));
            }
        }, function (Player $player): void {
            $player->sendForm(new MainForm());
        });
    }
}
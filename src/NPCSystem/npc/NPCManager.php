<?php

namespace NPCSystem\npc;

use NPCSystem\cache\Cache;
use NPCSystem\event\NPCCreateEvent;
use NPCSystem\event\NPCDespawnEvent;
use NPCSystem\event\NPCRemoveEvent;
use NPCSystem\event\NPCRespawnEvent;
use NPCSystem\event\NPCSpawnEvent;
use NPCSystem\event\NPCUpdateEvent;
use NPCSystem\npc\data\NPCData;
use NPCSystem\NPCSystem;
use NPCSystem\util\Util;
use pocketmine\entity\Human;
use pocketmine\entity\Location;
use pocketmine\item\ItemFactory;
use pocketmine\item\VanillaItems;
use pocketmine\utils\Config;
use pocketmine\world\Position;

class NPCManager {

    public const KEY_NAME_TAG = 0;
    public const KEY_COMMANDS = 1;
    public const KEY_SIZE = 2;
    public const KEY_SKIN = 3;
    public const KEY_LOOK_AT_PLAYER = 4;
    public const KEY_ITEM_IN_HAND = 5;
    public const KEY_HELMET = 6;
    public const KEY_CHESTPLATE = 7;
    public const KEY_LEGGINGS = 8;
    public const KEY_BOOTS = 9;

    public const UPDATE_KEYS = [
        0 => "NameTag",
        1 => "Commands",
        2 => "Size",
        3 => "Skin",
        4 => "LookAtPlayer",
        5 => "ItemInHand",
        6 => "Helmet",
        7 => "Chestplate",
        8 => "Leggings",
        9 => "Boots",
    ];

    public const REQUIRED_VALUES = [
        "NameTag" => "string",
        "Commands" => "array",
        "Size" => "float",
        "Skin" => "string",
        "LookAtPlayer" => "boolean",
        "ItemInHand" => "string",
        "Helmet" => "string",
        "Chestplate" => "string",
        "Leggings" => "string",
        "Boots" => "string"
    ];

    private static self $instance;
    /** @var array<NPC> */
    private array $npc = [];

    public function __construct() {
        self::$instance = $this;

        $this->loadAll();
    }

    public function loadAll() {
        foreach ($this->getConfig()->getAll() as $identifier => $data) {
            if (
                isset($data["NameTag"]) &&
                isset($data["Commands"]) &&
                isset($data["Size"]) &&
                isset($data["Skin"]) &&
                isset($data["LookAtPlayer"]) &&
                isset($data["Location"]) &&
                isset($data["ItemInHand"]) &&
                isset($data["Helmet"]) &&
                isset($data["Chestplate"]) &&
                isset($data["Leggings"]) &&
                isset($data["Boots"]) &&
                isset($data["Creator"])
            ) {
                if (is_array($data["Commands"]) && is_bool($data["LookAtPlayer"])) {
                    $location = Util::stringToVector($data["Location"]);
                    $itemInHand = Util::stringToItem($data["ItemInHand"]);
                    $helmet = Util::stringToItem($data["Helmet"]);
                    $chestplate = Util::stringToItem($data["Chestplate"]);
                    $leggings = Util::stringToItem($data["Leggings"]);
                    $boots = Util::stringToItem($data["Boots"]);

                    if (Cache::getInstance()->isInSkinCache($data["Skin"]) && $location instanceof Location) {
                        $this->npc[$identifier] = new NPC($identifier, new NPCData($data["NameTag"], $data["Commands"], $data["Size"], $data["Skin"], $data["LookAtPlayer"], $location, $itemInHand, $helmet, $chestplate, $leggings, $boots, $data["Creator"]));
                    }
                }
            }
        }

        $this->spawnAllNPC();
    }

    public function reload() {
        $this->npc = [];
        $this->loadAll();
    }

    public function createNPC(NPC $npc): bool {
        ($ev = new NPCCreateEvent($npc))->call();
        if (!$ev->isCancelled()) {
            $cfg = $this->getConfig();
            $cfg->set($npc->getIdentifier(), [
                "NameTag" => $npc->getData()->getNameTag(),
                "Commands" => $npc->getData()->getCommands(),
                "Size" => $npc->getData()->getSize(),
                "Skin" => $npc->getData()->getSkin(),
                "LookAtPlayer" => $npc->getData()->isLookAtPlayer(),
                "Location" => Util::vectorToString($npc->getData()->getLocation()),
                "ItemInHand" => Util::itemToString($npc->getData()->getItemInHand()),
                "Helmet" => Util::itemToString($npc->getData()->getHelmet()),
                "Chestplate" => Util::itemToString($npc->getData()->getChestplate()),
                "Leggings" => Util::itemToString($npc->getData()->getLeggings()),
                "Boots" => Util::itemToString($npc->getData()->getBoots()),
                "Creator" => $npc->getData()->getCreator()
            ]);
            $cfg->save();

            if (!isset($this->npc[$npc->getIdentifier()])) $this->npc[$npc->getIdentifier()] = $npc;
            return true;
        }
        return false;
    }

    public function removeNPC(NPC $npc): bool {
        ($ev = new NPCRemoveEvent($npc))->call();
        if (!$ev->isCancelled()) {
            $cfg = $this->getConfig();
            $cfg->remove($npc->getIdentifier());
            $cfg->save();

            if (isset($this->npc[$npc->getIdentifier()])) unset($this->npc[$npc->getIdentifier()]);
            return true;
        }
        return false;
    }

    public function spawnNPC(NPC $npc, ?string &$error = null): bool {
        ($ev = new NPCSpawnEvent($npc))->call();
        if (!$ev->isCancelled()) {
            if ($npc->getEntity() === null) {
                if (($skin = Cache::getInstance()->getSkinCacheOf($npc->getData()->getSkin())) !== null) {
                    $human = new Human($npc->getData()->getLocation(), $skin);
                    $human->setCanSaveWithChunk(false);
                    $human->setNameTag($npc->getData()->getNameTag());
                    $human->setNameTagAlwaysVisible();
                    $human->getInventory()->setItemInHand($npc->getData()->getItemInHand());
                    $human->getArmorInventory()->setHelmet($npc->getData()->getHelmet());
                    $human->getArmorInventory()->setChestplate($npc->getData()->getChestplate());
                    $human->getArmorInventory()->setLeggings($npc->getData()->getLeggings());
                    $human->getArmorInventory()->setBoots($npc->getData()->getBoots());
                    $human->setScale($npc->getData()->getSize());
                    $human->spawnToAll();
                    $npc->setEntity($human);
                    return true;
                } else $error = "Skin of player doesn't exists in cache!";
            } else $error = "NPC is already spawned!";
        }
        return false;
    }

    public function despawnNPC(NPC $npc, ?string &$error = null): bool {
        ($ev = new NPCDespawnEvent($npc))->call();
        if (!$ev->isCancelled()) {
            if (($entity = $npc->getEntity()) !== null) {
                $entity->getInventory()->clearAll();
                $entity->getArmorInventory()->clearAll();
                $entity->kill();
                $entity->close();
                $npc->setEntity(null);
                return true;
            } else $error = "NPC wasn't spawned yet!";
        }
        return false;
    }

    public function respawnNPC(NPC $npc, ?string &$error = null): bool {
        ($ev = new NPCRespawnEvent($npc))->call();
        if (!$ev->isCancelled()) {
            if (($entity = $npc->getEntity()) !== null) {
                if ($this->despawnNPC($npc, $despawnError)) {
                    if ($despawnError === null) {
                        if ($this->spawnNPC($npc, $spawnError)) {
                            if ($spawnError === null) {
                                return true;
                            } else $error = $spawnError;
                        } else $error = $spawnError;
                    } else $error = $despawnError;
                } else $error = $despawnError;
            } else $error = "NPC wasn't spawned yet!";
        }
        return false;
    }

    public function spawnAllNPC() {
        foreach ($this->npc as $npc) {
            $this->spawnNPC($npc);
        }
    }

    public function despawnAllNPC() {
        foreach ($this->npc as $npc) {
            $this->despawnNPC($npc);
        }
    }

    public function respawnAllNPC() {
        foreach ($this->npc as $npc) {
            $this->respawnNPC($npc);
        }
    }

    public function updateNPC(NPC $npc, int $updateKey, mixed $updateValue, ?string &$error = null): bool {
        $cfg = $this->getConfig();
        if (isset(self::UPDATE_KEYS[$updateKey])) {
            if (Util::is($updateValue, self::REQUIRED_VALUES[self::UPDATE_KEYS[$updateKey]])) {
                ($ev = new NPCUpdateEvent($npc, $updateKey, $updateValue))->call();
                if (!$ev->isCancelled()) {
                    if ($updateKey == self::KEY_NAME_TAG) {
                        $npc->getData()->setNameTag($updateValue);
                        if (($entity = $npc->getEntity()) !== null) $entity->setNameTag($updateValue);
                        $cfg->setNested($npc->getIdentifier() . "." . self::UPDATE_KEYS[$updateKey], $updateValue);
                        $cfg->save();
                        return true;
                    } else if ($updateKey == self::KEY_COMMANDS) {
                        $npc->getData()->setCommands($updateValue);
                        $cfg->setNested($npc->getIdentifier() . "." . self::UPDATE_KEYS[$updateKey], $updateValue);
                        $cfg->save();
                        return true;
                    } else if ($updateKey == self::KEY_SIZE) {
                        $npc->getData()->setSize($updateValue);
                        if (($entity = $npc->getEntity()) !== null) $entity->setScale($updateValue);
                        $cfg->setNested($npc->getIdentifier() . "." . self::UPDATE_KEYS[$updateKey], $updateValue);
                        $cfg->save();
                        return true;
                    } else if ($updateKey == self::KEY_SKIN) {
                        if (($entity = $npc->getEntity()) !== null) {
                            if (($skin = Cache::getInstance()->getSkinCacheOf($updateValue)) !== null) {
                                $npc->getData()->setSkin($updateValue);
                                $entity->setSkin($skin);
                                $cfg->setNested($npc->getIdentifier() . "." . self::UPDATE_KEYS[$updateKey], $updateValue);
                                $cfg->save();
                                return true;
                            }
                        }
                    } else if ($updateKey == self::KEY_LOOK_AT_PLAYER) {
                        $npc->getData()->setLookAtPlayer($updateValue);
                        if (($entity = $npc->getEntity()) !== null) $entity->setRotation($npc->getData()->getLocation()->getYaw(), $npc->getData()->getLocation()->getPitch());
                        $cfg->setNested($npc->getIdentifier() . "." . self::UPDATE_KEYS[$updateKey], $updateValue);
                        $cfg->save();
                        return true;
                    } else if ($updateKey == self::KEY_ITEM_IN_HAND) {
                        if (($item = Util::stringToItem($updateValue)) !== null) {
                            $npc->getData()->setItemInHand($item);
                            if (($entity = $npc->getEntity()) !== null) $entity->getInventory()->setItemInHand($item);
                            $cfg->setNested($npc->getIdentifier() . "." . self::UPDATE_KEYS[$updateKey], $updateValue);
                            $cfg->save();
                            return true;
                        }
                    } else if ($updateKey == self::KEY_HELMET) {
                        if (($item = Util::stringToItem($updateValue)) !== null) {
                            $npc->getData()->setHelmet($item);
                            if (($entity = $npc->getEntity()) !== null) $entity->getArmorInventory()->setHelmet($item);
                            $cfg->setNested($npc->getIdentifier() . "." . self::UPDATE_KEYS[$updateKey], $updateValue);
                            $cfg->save();
                            return true;
                        }
                    } else if ($updateKey == self::KEY_CHESTPLATE) {
                        if (($item = Util::stringToItem($updateValue)) !== null) {
                            $npc->getData()->setChestplate($item);
                            if (($entity = $npc->getEntity()) !== null) $entity->getArmorInventory()->setChestplate($item);
                            $cfg->setNested($npc->getIdentifier() . "." . self::UPDATE_KEYS[$updateKey], $updateValue);
                            $cfg->save();
                            return true;
                        }
                    } else if ($updateKey == self::KEY_LEGGINGS) {
                        if (($item = Util::stringToItem($updateValue)) !== null) {
                            $npc->getData()->setLeggings($item);
                            if (($entity = $npc->getEntity()) !== null) $entity->getArmorInventory()->setLeggings($item);
                            $cfg->setNested($npc->getIdentifier() . "." . self::UPDATE_KEYS[$updateKey], $updateValue);
                            $cfg->save();
                            return true;
                        }
                    } else if ($updateKey == self::KEY_BOOTS) {
                        if (($item = Util::stringToItem($updateValue)) !== null) {
                            $npc->getData()->setBoots($item);
                            if (($entity = $npc->getEntity()) !== null) $entity->getArmorInventory()->setBoots($item);
                            $cfg->setNested($npc->getIdentifier() . "." . self::UPDATE_KEYS[$updateKey], $updateValue);
                            $cfg->save();
                            return true;
                        }
                    }
                }
            }
        }

        return false;
    }

    public function npcExists(string $identifier): bool {
        return $this->getConfig()->exists($identifier);
    }

    public function getNPC(Position|int|string $by): ?NPC {
        foreach ($this->npc as $npc) {
            if (($entity = $npc->getEntity()) instanceof Human) {
                if ($by instanceof Position) {
                    if ($entity->getPosition()->equals($by)) return $npc;
                } else if (is_int($by)) {
                    if ($entity->getId() == $by) return $npc;
                } else if (is_string($by)) {
                    if ($npc->getIdentifier() == $by) return $npc;
                }
            } else {
                if (is_string($by)) {
                    if ($npc->getIdentifier() == $by) return $npc;
                }
            }
        }
        return null;
    }

    public function getNPCs(): array {
        return $this->npc;
    }

    private function getConfig(): Config {
        return new Config(NPCSystem::getInstance()->getDataFolder() . "npc/npc.json", 1);
    }

    public static function getInstance(): NPCManager {
        return self::$instance;
    }
}
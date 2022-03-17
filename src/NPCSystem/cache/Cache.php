<?php

namespace NPCSystem\cache;

use NPCSystem\NPCSystem;
use NPCSystem\util\Util;
use pocketmine\entity\Skin;
use pocketmine\player\Player;

class Cache {

    private static self $instance;
    /** @var array<Skin> */
    private array $skinCache = [];

    public function __construct() {
        self::$instance = $this;
    }

    public function addToSkinCache(Player $player) {
        $this->skinCache[$player->getName()] = $player->getSkin();
    }

    public function removeFromSkinCache(Player $player) {
        unset($this->skinCache[$player->getName()]);
    }

    public function getSkinCacheOf(Player|string $player): ?Skin {
        return $this->skinCache[($player instanceof Player ? $player->getName() : $player)] ?? null;
    }

    public function isInSkinCache(Player|string $player): bool {
        return isset($this->skinCache[($player instanceof Player ? $player->getName() : $player)]);
    }

    public function saveInFile() {
        foreach ($this->skinCache as $player_name => $skin) {
            if (!file_exists(NPCSystem::getInstance()->getDataFolder() . "skin/" . $player_name . "/")) @mkdir(NPCSystem::getInstance()->getDataFolder() . "skin/" . $player_name . "/");

            @file_put_contents(NPCSystem::getInstance()->getDataFolder() . "skin/" . $player_name . "/id.txt", $skin->getSkinId());
            @file_put_contents(NPCSystem::getInstance()->getDataFolder() . "skin/" . $player_name . "/data.txt", $skin->getSkinData());
            @file_put_contents(NPCSystem::getInstance()->getDataFolder() . "skin/" . $player_name . "/geo_name.txt", $skin->getGeometryName());
            @file_put_contents(NPCSystem::getInstance()->getDataFolder() . "skin/" . $player_name . "/geo_data.txt", $skin->getGeometryData());
            @file_put_contents(NPCSystem::getInstance()->getDataFolder() . "skin/" . $player_name . "/cape_data.txt", $skin->getCapeData());
        }
    }

    public function getFromSaveInFile() {
        $skinCache = [];
        foreach (scandir(NPCSystem::getInstance()->getDataFolder() . "skin/") as $file) {
            if ($file == "." || $file == "..") continue;
            if (is_dir(NPCSystem::getInstance()->getDataFolder() . "skin/" . $file)) {
                $skinCache[$file] = [
                    "SkinId" => (!($content = @file_get_contents(NPCSystem::getInstance()->getDataFolder() . "skin/" . $file . "/id.txt")) ? "" : $content),
                    "SkinData" => (!($content = @file_get_contents(NPCSystem::getInstance()->getDataFolder() . "skin/" . $file . "/data.txt")) ? "" : $content),
                    "GeoName" => (!($content = @file_get_contents(NPCSystem::getInstance()->getDataFolder() . "skin/" . $file . "/geo_name.txt")) ? "" : $content),
                    "GeoData" => (!($content = @file_get_contents(NPCSystem::getInstance()->getDataFolder() . "skin/" . $file . "/geo_data.txt")) ? "" : $content),
                    "CapeData" => (!($content = @file_get_contents(NPCSystem::getInstance()->getDataFolder() . "skin/" . $file . "/cape_data.txt")) ? "" : $content),
                ];
            }
        }


        if (count($skinCache) > 0) {
            foreach ($skinCache as $player_name => $skinData) {
                if (isset($skinData["SkinId"]) && isset($skinData["SkinData"]) && isset($skinData["GeoName"]) && isset($skinData["GeoData"]) && isset($skinData["CapeData"])) {
                    $this->skinCache[$player_name] = new Skin($skinData["SkinId"], $skinData["SkinData"], $skinData["CapeData"], $skinData["GeoName"], $skinData["GeoData"]);
                }
            }
        }
    }

    public function removeSaveInFile() {
        foreach (scandir(NPCSystem::getInstance()->getDataFolder() . "skin/") as $file) {
            if ($file == "." || $file == "..") continue;
            if (is_dir(NPCSystem::getInstance()->getDataFolder() . "skin/" . $file)) Util::deleteDir(NPCSystem::getInstance()->getDataFolder() . "skin/" . $file);
        }
    }

    public function getSkinCache(): array {
        return $this->skinCache;
    }

    public static function getInstance(): Cache {
        return self::$instance;
    }
}
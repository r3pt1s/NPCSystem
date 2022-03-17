<?php

namespace NPCSystem;

use NPCSystem\cache\Cache;
use NPCSystem\command\NPCCommand;
use NPCSystem\listener\EventListener;
use NPCSystem\npc\NPCManager;
use NPCSystem\session\Session;
use pocketmine\permission\DefaultPermissions;
use pocketmine\permission\Permission;
use pocketmine\permission\PermissionManager;
use pocketmine\plugin\PluginBase;

class NPCSystem extends PluginBase {

    public static function getPrefix(): string {
        return "§8× §6§lNPCSystem §r§8| §r§7";
    }

    private static self $instance;
    private NPCManager $npcManager;
    private Cache $cache;
    private Session $session;

    protected function onEnable(): void {
        self::$instance = $this;

        if (!file_exists($this->getDataFolder() . "skin/")) @mkdir($this->getDataFolder() . "skin/");
        if (!file_exists($this->getDataFolder() . "npc/")) @mkdir($this->getDataFolder() . "npc/");

        $this->cache = new Cache();
        $this->npcManager = new NPCManager();
        $this->session = new Session();

        $this->cache->getFromSaveInFile();
        $this->cache->removeSaveInFile();

        $this->npcManager->loadAll();

        $this->registerPermission("npc.command");

        $this->getServer()->getPluginManager()->registerEvents(new EventListener(), $this);
        $this->getServer()->getCommandMap()->registerAll("npcSystem", [
            new NPCCommand("npc", "NPC Command", "/npc", [])
        ]);
    }

    protected function onDisable(): void {
        $this->cache->saveInFile();
    }

    private function registerPermission(string... $permissions) {
        if (($operator = PermissionManager::getInstance()->getPermission(DefaultPermissions::ROOT_OPERATOR)) !== null) {
            foreach ($permissions as $permission) DefaultPermissions::registerPermission(new Permission($permission), [$operator]);
        }
    }

    public function getNpcManager(): NPCManager {
        return $this->npcManager;
    }

    public function getCache(): Cache {
        return $this->cache;
    }

    public function getSession(): Session {
        return $this->session;
    }

    public static function getInstance(): NPCSystem {
        return self::$instance;
    }
}
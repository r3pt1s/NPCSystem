<?php

namespace NPCSystem\listener;

use NPCSystem\cache\Cache;
use NPCSystem\form\subForm\EditForm;
use NPCSystem\npc\NPCManager;
use NPCSystem\session\Session;
use pocketmine\entity\Human;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerJumpEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\network\mcpe\protocol\InventoryTransactionPacket;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\network\mcpe\protocol\MoveActorAbsolutePacket;
use pocketmine\network\mcpe\protocol\SetActorMotionPacket;
use pocketmine\network\mcpe\protocol\types\inventory\UseItemOnEntityTransactionData;
use pocketmine\network\mcpe\protocol\types\LevelSoundEvent;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\world\Position;

class EventListener implements Listener {

    public function onJoin(PlayerJoinEvent $event) {
        Cache::getInstance()->addToSkinCache($event->getPlayer());
        NPCManager::getInstance()->respawnAllNPC();
    }

    public function onDamage(EntityDamageEvent $event) {
        $entity = $event->getEntity();

        if ($entity instanceof Human) {
            if (($npc = NPCManager::getInstance()->getNPC($entity->getPosition())) !== null) {
                $event->cancel();
            }
        }
    }

    public function onDataPacketReceive(DataPacketReceiveEvent $event) {
        $packet = $event->getPacket();
        $networkSession = $event->getOrigin();
        $player = $networkSession->getPlayer();

        if ($packet instanceof InventoryTransactionPacket) {
            $trData = $packet->trData;
            if ($trData instanceof UseItemOnEntityTransactionData) {
                if ($trData->getActionType() == $trData::ACTION_INTERACT || $trData->getActionType() == $trData::ACTION_ITEM_INTERACT) {
                    if (($npc = NPCManager::getInstance()->getNPC($trData->getActorRuntimeId())) !== null) {
                        if (!Session::getInstance()->hasEntityWaitSession($player)) {
                            $player->sendForm(new EditForm($npc, true));
                            Session::getInstance()->addToEntityWaitSession($player);
                        }
                    }
                }
            }
        }
    }

    public function onMove(PlayerMoveEvent $event) {
        $player = $event->getPlayer();

        foreach (NPCManager::getInstance()->getNPCs() as $npc) {
            if (($entity = $npc->getEntity()) !== null) {
                if ($entity->getPosition()->distance($player->getPosition()) <= 8) {
                    if ($npc->getData()->isLookAtPlayer()) {
                        $horizontal = sqrt(($player->getPosition()->x - $entity->getPosition()->x) ** 2 + ($player->getPosition()->z - $entity->getLocation()->z) ** 2);
                        $vertical = $player->getPosition()->y - $entity->getLocation()->getY(); /** ($entity->getLocation()->y + $entity->getEyeHeight()); */
                        $pitch = -atan2($vertical, $horizontal) / M_PI * 180;

                        $xDist = $player->getPosition()->x - $entity->getLocation()->x;
                        $zDist = $player->getPosition()->z - $entity->getLocation()->z;

                        $yaw = atan2($zDist, $xDist) / M_PI * 180 - 90;
                        if ($yaw < 0) $yaw += 360.0;

                        $player->getNetworkSession()->sendDataPacket(MoveActorAbsolutePacket::create($entity->getId(), Position::fromObject($entity->getOffsetPosition($entity->getPosition()), $entity->getWorld()), $pitch, $yaw, $yaw, 0));
                    }
                }
            }
        }
    }

    public function onDamageByEntity(EntityDamageByEntityEvent $event) {
        $entity = $event->getEntity();
        $damager = $event->getDamager();

        if ($entity instanceof Human && $damager instanceof Player) {
            if (($npc = NPCManager::getInstance()->getNPC($entity->getPosition())) !== null) {
                $event->cancel();

                if (!Session::getInstance()->hasEntityWaitSession($damager)) {
                    Session::getInstance()->addToEntityWaitSession($damager);
                    foreach ($npc->getData()->getCommands() as $command) {
                        Server::getInstance()->dispatchCommand($damager, $command);
                    }
                }
            }
        }
    }
}
<?php

namespace NPCSystem\util;

use pocketmine\entity\Location;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\math\Vector3;
use pocketmine\Server;
use pocketmine\world\Position;

class Util {

    public static function is($v, string $type): bool {
        if ($type == "string") return is_string($v);
        else if ($type == "int" || $type == "integer") return is_int($v);
        else if ($type == "float") return is_float($v);
        else if ($type == "bool" || $type == "boolean") return is_bool($v);
        else if ($type == "array") return is_array($v);
        return false;
    }

    public static function vectorToString(Vector3 $vector): string {
        if ($vector instanceof Position) return ($vector instanceof Location ? $vector->getX() . ":" . $vector->getY() . ":" . $vector->getZ() . ":" . $vector->getYaw() . ":" . $vector->getPitch() . ":" . $vector->getWorld()->getFolderName() : $vector->getX() . ":" . $vector->getY() . ":" . $vector->getZ() . ":" . $vector->getWorld()->getFolderName());
        else return $vector->getX() . ":" . $vector->getY() . ":" . $vector->getZ();
    }

    public static function stringToVector(string $vectorString): ?Vector3 {
        $explode = explode(":", $vectorString);
        if (isset($explode[0]) && isset($explode[1]) && isset($explode[2])) {
            if (isset($explode[3])) {
                if (isset($explode[4]) && isset($explode[5])) {
                    if (($world = Server::getInstance()->getWorldManager()->getWorldByName($explode[5])) !== null) return new Location($explode[0], $explode[1], $explode[2], $world, $explode[3], $explode[4]);
                } else {
                    if (($world = Server::getInstance()->getWorldManager()->getWorldByName($explode[3])) !== null) return new Position($explode[0], $explode[1], $explode[2], $world);
                }
            } else {
                return new Vector3($explode[0], $explode[1], $explode[2]);
            }
        }
        return null;
    }

    public static function itemToString(Item $item): string {
        return $item->getId() . ":" . $item->getMeta();
    }

    public static function stringToItem(string $itemString, ?Item $default = null): ?Item {
        $explode = explode(":", $itemString);
        if (isset($explode[0]) && isset($explode[1])) {
            return ItemFactory::getInstance()->get($explode[0], $explode[1], 1);
        }
        return $default;
    }

    public static function deleteDir(string $source) {
        if (file_exists($source)) {
            $dir = opendir($source);
            while($file = readdir($dir)) {
                if (($file != ".") && ($file != "..")) {
                    if (is_dir($source . "/" . $file))  {
                        self::deleteDir($source . "/" . $file . "/");
                    } else {
                        unlink($source . "/" . $file);
                    }
                }
            }
            closedir($dir);
            rmdir($source);
        }
    }
}
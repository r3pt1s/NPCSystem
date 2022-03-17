<?php

namespace NPCSystem\npc\data;

use pocketmine\entity\Location;
use pocketmine\item\Item;
use pocketmine\world\Position;

class NPCData {

    private string $nameTag;
    /** @var array<string> */
    private array $commands = [];
    private int $size;
    private string $skin;
    private bool $lookAtPlayer;
    private Location $location;
    private Item $itemInHand;
    private Item $helmet;
    private Item $chestplate;
    private Item $leggings;
    private Item $boots;
    private string $creator;

    public function __construct(string $nameTag, array $commands, int $size, ?string $skin, bool $lookAtPlayer, Location $location, Item $itemInHand, Item $helmet, Item $chestplate, Item $leggings, Item $boots, string $creator) {
        $this->nameTag = $nameTag;
        $this->commands = $commands;
        $this->size = $size;
        $this->skin = ($skin === null ? $creator : $skin);
        $this->lookAtPlayer = $lookAtPlayer;
        $this->location = $location;
        $this->itemInHand = $itemInHand;
        $this->helmet = $helmet;
        $this->chestplate = $chestplate;
        $this->leggings = $leggings;
        $this->boots = $boots;
        $this->creator = $creator;
    }

    public function getNameTag(): string {
        return $this->nameTag;
    }

    public function setNameTag(string $nameTag): void {
        $this->nameTag = $nameTag;
    }

    public function getCommands(): array {
        return $this->commands;
    }

    public function setCommands(array $commands): void {
        $this->commands = $commands;
    }

    public function getSize(): int {
        return $this->size;
    }

    public function setSize(int $size): void {
        $this->size = $size;
    }

    public function getSkin(): string {
        return $this->skin;
    }

    public function setSkin(string $skin): void {
        $this->skin = $skin;
    }

    public function isLookAtPlayer(): bool {
        return $this->lookAtPlayer;
    }

    public function setLookAtPlayer(bool $lookAtPlayer): void {
        $this->lookAtPlayer = $lookAtPlayer;
    }

    public function getLocation(): Location {
        return $this->location;
    }

    public function setLocation(Location $location): void {
        $this->location = $location;
    }

    public function getItemInHand(): Item {
        return $this->itemInHand;
    }

    public function setItemInHand(Item $itemInHand): void {
        $this->itemInHand = $itemInHand;
    }

    public function getHelmet(): Item {
        return $this->helmet;
    }

    public function setHelmet(Item $helmet): void {
        $this->helmet = $helmet;
    }

    public function getChestplate(): Item {
        return $this->chestplate;
    }

    public function setChestplate(Item $chestplate): void {
        $this->chestplate = $chestplate;
    }

    public function getLeggings(): Item {
        return $this->leggings;
    }

    public function setLeggings(Item $leggings): void {
        $this->leggings = $leggings;
    }

    public function getBoots(): Item {
        return $this->boots;
    }

    public function setBoots(Item $boots): void {
        $this->boots = $boots;
    }

    public function getCreator(): string {
        return $this->creator;
    }

    public function setCreator(string $creator): void {
        $this->creator = $creator;
    }
}
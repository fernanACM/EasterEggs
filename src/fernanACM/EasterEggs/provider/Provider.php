<?php

#      _       ____   __  __ 
#     / \     / ___| |  \/  |
#    / _ \   | |     | |\/| |
#   / ___ \  | |___  | |  | |
#  /_/   \_\  \____| |_|  |_|
# The creator of this plugin was fernanACM.
# https://github.com/fernanACM

declare(strict_types=1);

namespace fernanACM\EasterEggs\provider;

use pocketmine\player\Player;

abstract class Provider{

    /**
     * @param Player $player
     * @return array
     */
    abstract protected function getEgg(Player $player): array;

    /**
     * @param Player $player
     * @return integer
     */
    abstract public function getEggs(Player $player): int;

    /**
     * @param Player $player
     * @return integer
     */
    abstract public function getLastEggId(Player $player): int;

    /**
     * @param Player $player
     * @param string $eggId
     * @return void
     */
    abstract public function addEgg(Player $player, string $eggId): void;

    /**
     * @param Player $player
     * @param string $eggId
     * @return void
     */
    abstract public function removeEgg(Player $player, string $eggId): void;

    /**
     * @param Player $player
     * @param string $eggId
     * @return boolean
     */
    abstract public function claimedEgg(Player $player, string $eggId): bool;

    /**
     * @param Player $player
     * @return boolean
     */
    abstract public function exists(Player $player): bool;

    /**
     * @param Player $player
     * @return void
     */
    abstract public function createAccount(Player $player): void;

    /**
     * @param Player $player
     * @return boolean
     */
    abstract public function isCompleted(Player $player): bool;

    /**
     * @param Player $player
     * @param boolean $result
     * @return boolean
     */
    abstract public function setCompleted(Player $player, bool $result): bool;

    /**
     * @param Player $player
     * @return string
     */
    abstract public function getEventName(Player $player): string;

    /**
     * @param Player $player
     * @param string $eventId
     * @return void
     */
    abstract public function applyEventById(Player $player, string $eventId): void;

    /**
     * @return void
     */
    abstract public function load(): void;

    /**
     * @return void
     */
    abstract public function unload(): void;

    /**
     * @return void
     */
    abstract public function reset(): void;
}
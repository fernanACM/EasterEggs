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
     * @param string $userName
     * @return integer
     */
    abstract public function getEggsByUserName(string $userName): int;

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
     * @return void
     */
    abstract public function load(): void;

    /**
     * @return void
     */
    abstract public function unload(): void;
}
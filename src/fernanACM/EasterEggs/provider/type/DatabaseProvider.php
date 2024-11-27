<?php

declare(strict_types=1);

namespace fernanACM\EasterEggs\provider\type;

use pocketmine\player\Player;

use fernanACM\EasterEggs\provider\Provider;

class DatabaseProvider extends Provider{

    /**
     * @param Player $player
     * @return boolean
     */
    public function exists(Player $player): bool{
        return true;
    }

    /**
     * @param Player $player
     * @return void
     */
    public function createAccount(Player $player): void{
        
    }

    /**
     * @return void
     */
    public function load(): void{
        
    }

    /**
     * @return void
     */
    public function unload(): void{
    }
}
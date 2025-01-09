<?php

#      _       ____   __  __ 
#     / \     / ___| |  \/  |
#    / _ \   | |     | |\/| |
#   / ___ \  | |___  | |  | |
#  /_/   \_\  \____| |_|  |_|
# The creator of this plugin was fernanACM.
# https://github.com/fernanACM

declare(strict_types=1);

namespace fernanACM\EasterEggs\events;

use pocketmine\event\player\PlayerEvent;

use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;

use fernanACM\EasterEggs\manager\EasterEggManager;

class PlayerClaimEvent extends PlayerEvent implements Cancellable{
    use CancellableTrait;

    /** @var string $eggId */
    protected string $eggId;

    /**
     * @param string $eggId
     */
    public function __construct(string $eggId){
        $this->eggId = $eggId;
    }

    /**
     * @return string
     */
    public function getEggId(): string{
        return $this->eggId;
    }

    /**
     * @return boolean
     */
    public function isClaimed(): bool{
        return $this->getEasterEggManager()->claimedEgg($this->getPlayer(), $this->getEggId());
    }

    /**
     * @return boolean
     */
    public function isCompleted(): bool{
        return $this->getEasterEggManager()->isCompleted($this->getPlayer());
    }

    /**
     * @return EasterEggManager
     */
    public function getEasterEggManager(): EasterEggManager{
        return EasterEggManager::getInstance();
    }
}
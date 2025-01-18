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

use pocketmine\player\Player;

use pocketmine\event\player\PlayerEvent;

use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;

use pocketmine\item\Item;

use pocketmine\console\ConsoleCommandSender;

use fernanACM\EasterEggs\manager\EasterEggManager;
use fernanACM\EasterEggs\manager\LootManager;

class PlayerRewardEvent extends PlayerEvent implements Cancellable{
    use CancellableTrait;

    /** @var Item[] */
    protected array $rewardedItems = [];
    /** @var array $rewardedCommands */
    protected array $rewardedCommands = [];

    /**
     * @param Player $player
     */
    public function __construct(Player $player){
        $this->player = $player;
    }

    /**
     * @return Item[]
     */
    public function getItems(): array{
        return $this->rewardedItems;
    }

    /**
     * @param Item[] $items
     * @return void
     */
    public function setItems(array $items): void{
        $this->rewardedItems = $items;
    }

    /**
     * @return array
     */
    public function getCommands(): array{
        return $this->rewardedCommands;
    }

    /**
     * @param array $commands
     * @return void
     */
    public function setCommands(array $commands): void{
        $this->rewardedCommands = $commands;
    }

    /**
     * Generates and sets random items as rewards for the player.
     * 
     * @param int $amount
     * @return void
     */
    public function generateRandomItems(int $amount): void{
        $lootManager = $this->getLootManager();
        $this->setItems($lootManager->getRandomItems($amount));
    }

    /**
     * Generates and sets reward commands for the player.
     * 
     * @return void
     */
    public function generateRewardCommands(): void{
        $lootManager = $this->getLootManager();
        $this->setCommands($lootManager->getCommands());
    }

    /**
     * Executes the reward commands and adds items to the player's inventory.
     * If inventory is full, items are dropped at the player's position.
     * 
     * @return void
     */
    public function executeRewards(): void{
        $player = $this->getPlayer();
        foreach($this->getItems() as $item){
            if(!$player->getInventory()->canAddItem($item)){
                $player->getWorld()->dropItem($player->getPosition(), $item);
            }else{
                $player->getInventory()->addItem($item);
            }
        }
        $server = $player->getServer();
        foreach($this->getCommands() as $command){
            $command = str_replace("{PLAYER}", $player->getName(), $command);
            $server->dispatchCommand(new ConsoleCommandSender($server, $server->getLanguage()), $command);
        }
    }

    /**
     * @return EasterEggManager
     */
    public function getEasterEggManager(): EasterEggManager{
        return EasterEggManager::getInstance();
    }

    /**
     * @return LootManager
     */
    public function getLootManager(): LootManager{
        return LootManager::getInstance();
    }
}
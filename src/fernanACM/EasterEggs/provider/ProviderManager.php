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

use pocketmine\utils\Config;
use pocketmine\utils\SingletonTrait;

use fernanACM\EasterEggs\EasterEggs as EE;
use fernanACM\EasterEggs\const\DataConst;

use fernanACM\EasterEggs\helper\EventHelper;

use fernanACM\EasterEggs\addons\ScoreHudAddon;

final class ProviderManager{
    use SingletonTrait{
        setInstance as protected;
        reset as protected;
    }

    /** @var Config $data */
    protected Config $data;

    protected const FILE_NAME = "playerData.yml";

    public function __construct(){
        self::setInstance($this);
    }

    /**
     * @return void
     */
    public function init(): void{
        $this->data = new Config(EE::getInstance()->getDataFolder(). self::FILE_NAME);
    }

    /**
     * Resets the supplier information you have collected.
     * 
     * @return void
     */
    public function reset(): void{
        $allData = $this->data->getAll();
        foreach($allData as $playerName => $playerData){
            $allData[$playerName] = [
                DataConst::EGGS => [],
                DataConst::EVENT_NAME => EventHelper::currentEvent(),
                DataConst::COMPLETED => false
            ];
        }
        $this->data->setAll($allData);
        $this->data->save();
        ScoreHudAddon::onUpdate();
    }

    /**
     * @param Player $player
     * @return array
     */
    protected function getPlayerData(Player $player): array{
        return $this->data->get($player->getDisplayName(), [
            DataConst::EGGS => [],
            DataConst::EVENT_NAME => EventHelper::currentEvent(),
            DataConst::COMPLETED => false
        ]);
    }

    /**
     * Check if player has an account.
     * 
     * @param Player $player
     * @return boolean
     */
    public function exists(Player $player): bool{
        return $this->data->exists($player->getDisplayName());
    }

    /**
     * Creates an account for the player.
     * 
     * @param Player $player
     * @return void
     */
    public function createAccount(Player $player): void{
        if(!$this->exists($player)){
            $this->data->set($player->getDisplayName(), [
                DataConst::EGGS => [],
                DataConst::EVENT_NAME => EventHelper::currentEvent(),
                DataConst::COMPLETED => false
            ]);
            $this->data->save();
            ScoreHudAddon::onUpdate();
        }
    }

    /**
     * Set event type via ID.
     * 
     * @param Player $player
     * @param string $eventId
     * @return void
     */
    public function applyEventById(Player $player, string $eventId): void{
        if(!$this->exists($player)) return;

        $playerData = $this->getPlayerData($player);
        $playerData[DataConst::EVENT_NAME] = $eventId;
        $this->data->set($player->getDisplayName(), [$playerData]);
        $this->data->save();
    }

    /**
     * Our the event that the player is attending.
     * 
     * @param Player $player
     * @return string
     */
    public function getEventName(Player $player): string{
        $playerData = $this->getPlayerData($player);
        return strval($playerData[DataConst::EVENT_NAME] ?? "N/A");
    }

    /**
     * Check if the goal has been achieved.
     * 
     * @param Player $player
     * @return boolean
     */
    public function isCompleted(Player $player): bool{
        $playerData = $this->getPlayerData($player);
        return boolval($playerData[DataConst::COMPLETED] ?? false);
    }

    /**
     * Sets whether the Eggs goal has been completed.
     * 
     * @param Player $player
     * @param boolean $result
     * @return void
     */
    public function setCompleted(Player $player, bool $result): void{
        if(!$this->exists($player)) return;

        $playerData = $this->getPlayerData($player);
        $playerData[DataConst::COMPLETED] = $result;

        $this->data->set($player->getDisplayName(), $playerData);
        $this->data->save();
    }

    /**
     * Get the number of eggs from the player.
     * 
     * @param Player $player
     * @return integer
     */
    public function getEggs(Player $player): int{
        $playerData = $this->getPlayerData($player);
        return count($playerData[DataConst::EGGS] ?? []);
    }

    /**
     * Get the player's last egg ID.
     * 
     * @param Player $player
     * @return integer
     */
    public function getLastEggId(Player $player): int{
        $playerData = $this->getPlayerData($player);
        $claimedEggs = $playerData[DataConst::EGGS] ?? [];
        $ids = array_map(function(string $eggId): int{
            preg_match('/\d+/', $eggId, $matches); // SEARCH NUMBERS IN THE FORMAT "egg(n)"
            return isset($matches[0]) ? (int)$matches[0] : 0;
        }, $claimedEggs);
        return empty($ids) ? 0 : max($ids);
    }

    /**
     * @param Player $player
     * @return array
     */
    protected function getEgg(Player $player): array{
        $playerData = $this->getPlayerData($player);
        return $playerData[DataConst::EGGS] ?? [];
    }

    /**
     * Add an egg (point) to the player.
     * 
     * @param Player $player
     * @param string $eggId
     * @return void
     */
    public function addEgg(Player $player, string $eggId): void{
        if(!$this->exists($player)) return;
        if(isset($this->getEgg($player)[$eggId])) return;

        $playerData = $this->getPlayerData($player);
        $eggs = $playerData[DataConst::EGGS] ?? [];
        if(in_array($eggId, $eggs)) return;

        $eggs[] = $eggId;
        $playerData[DataConst::EGGS] = $eggs;
        $this->data->set($player->getDisplayName(), $playerData);
        $this->data->save();
    }

    /**
     * Remove an egg (point) from the player.
     * 
     * @param Player $player
     * @param string $eggId
     * @return void
     */
    public function removeEgg(Player $player, string $eggId): void{
        if(!$this->exists($player)) return;

        $playerData = $this->getPlayerData($player);
        $eggs = $playerData[DataConst::EGGS] ?? [];
        $playerData[DataConst::EGGS] = array_values(array_diff($eggs, [$eggId]));

        $this->data->set($player->getDisplayName(), $playerData);
        $this->data->save();
    }

    /**
     * Check if you have claimed the egg.
     * 
     * @param Player $player
     * @param string $eggId
     * @return boolean
     */
    public function claimedEgg(Player $player, string $eggId): bool{
        if(!$this->exists($player)) return false;
        if(isset($this->getEgg($player)[$eggId])) return false;

        $playerData = $this->getPlayerData($player);
        $eggs = $playerData[DataConst::EGGS] ?? [];
        return in_array($eggId, $eggs, true);
    }

    /**
     * Provider instance
     * 
     * @return Config
     */
    public function getConfig(): Config{
        return $this->data;
    }
}
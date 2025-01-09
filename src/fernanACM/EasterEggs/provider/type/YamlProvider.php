<?php

#      _       ____   __  __ 
#     / \     / ___| |  \/  |
#    / _ \   | |     | |\/| |
#   / ___ \  | |___  | |  | |
#  /_/   \_\  \____| |_|  |_|
# The creator of this plugin was fernanACM.
# https://github.com/fernanACM

declare(strict_types=1);

namespace fernanACM\EasterEggs\provider\type;

use pocketmine\player\Player;

use pocketmine\utils\Config;

use fernanACM\EasterEggs\provider\Provider;

use fernanACM\EasterEggs\EasterEggs as EE;
use fernanACM\EasterEggs\const\DataConst;

class YamlProvider extends Provider{

    /** @var EE $plugin */
    protected EE $plugin;
    /** @var Config $data */
    protected Config $data;

    protected const FILE_NAME = "playerData.yml";

    /**
     * @param EE $plugin
     */
    public function __construct(EE $plugin){
        $this->plugin = $plugin;
    }

    /**
     * @return void
     */
    public function load(): void{
        $this->data = new Config($this->plugin->getDataFolder(). self::FILE_NAME);
    }

    /**
     * @return void
     */
    public function unload(): void{
        // NOTHING...
    }

    /**
     * @return void
     */
    public function reset(): void{
        $allData = $this->data->getAll();
        foreach($allData as $playerName => $playerData){
            $allData[$playerName] = [
                DataConst::EGGS => [],
                DataConst::EVENT_NAME => "",
                DataConst::COMPLETED => false
            ];
        }
        $this->data->setAll($allData);
        $this->data->save();
    }

    /**
     * @param Player $player
     * @return array
     */
    protected function getPlayerData(Player $player): array{
        return $this->data->get($player->getDisplayName(), [
            DataConst::EGGS => [],
            DataConst::EVENT_NAME => "",
            DataConst::COMPLETED => false
        ]);
    }

    /**
     * @param Player $player
     * @return boolean
     */
    public function exists(Player $player): bool{
        return $this->data->exists($player->getDisplayName());
    }

    /**
     * @param Player $player
     * @return void
     */
    public function createAccount(Player $player): void{
        if(!$this->exists($player)){
            $this->data->set($player->getDisplayName(), [
                DataConst::EGGS => [],
                DataConst::EVENT_NAME => "",
                DataConst::COMPLETED => false
            ]);
            $this->data->save();
        }
    }

    /**
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
     * @param Player $player
     * @return string
     */
    public function getEventName(Player $player): string{
        $playerData = $this->getPlayerData($player);
        return strval($playerData[DataConst::EVENT_NAME] ?? "N/A");
    }

    /**
     * @param Player $player
     * @return boolean
     */
    public function isCompleted(Player $player): bool{
        $playerData = $this->getPlayerData($player);
        return boolval($playerData[DataConst::COMPLETED] ?? false);
    }

    /**
     * @param Player $player
     * @param boolean $result
     * @return boolean
     */
    public function setCompleted(Player $player, bool $result): bool{
        if(!$this->exists($player)) return false;

        $playerData = $this->getPlayerData($player);
        $playerData[DataConst::COMPLETED] = $result;

        $this->data->set($player->getDisplayName(), $playerData);
        $this->data->save();
        return true;
    }

    /**
     * @param Player $player
     * @return integer
     */
    public function getEggs(Player $player): int{
        $playerData = $this->getPlayerData($player);
        return count($playerData[DataConst::EGGS] ?? []);
    }

    /**
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
        $this->data->setNested($player->getDisplayName(), [$playerData]);
        $this->data->save();
    }

    /**
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
     * @return Config
     */
    public function getConfig(): Config{
        return $this->data;
    }
}
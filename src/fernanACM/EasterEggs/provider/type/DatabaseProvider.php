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

use poggit\libasynql\libasynql;
use poggit\libasynql\DataConnector;

use fernanACM\EasterEggs\EasterEggs as EE;
use fernanACM\EasterEggs\provider\Provider;

use fernanACM\EasterEggs\const\DataConst;
use fernanACM\EasterEggs\const\DatabaseConst;

class DatabaseProvider extends Provider{

    /** @var EE $plugin */
    protected EE $plugin;
    /** @var DataConnector $database */
    protected DataConnector $database;

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
        $data = strtolower(strval($this->plugin->config->getNested("Storage.Database.type")));
        $this->database = libasynql::create($this->plugin, $data, [
            "sqlite" => "sqlite.sql",
            "mysql"  => "mysql.sql",
        ]);
        $this->database->executeGeneric(DatabaseConst::INIT);
    }

    /**
     * @return void
     */
    public function unload(): void{
        if(!is_null($this->database)) $this->database->close();
    }

    /**
     * @return void
     */
    public function reset(): void{
        $this->database->executeGeneric(DatabaseConst::RESET);
    }

    /**
     * @param Player $player
     * @return array
     */
    protected function getPlayerData(Player $player): array{
        $playerData = [];
        $this->database->executeSelect(
            DatabaseConst::GET_PLAYER_DATA,
            [DataConst::PLAYER => $player->getName()],
            function(array $rows) use (&$playerData){
                if(!empty($rows)){
                    $playerData = $rows[0];
                }
            }
        );
        return $playerData;
    }

    /**
     * @param Player $player
     * @return boolean
     */
    public function exists(Player $player): bool{
        $exists = false;
        $this->database->executeSelect(
            DatabaseConst::PLAYER_EXISTS,
            [DataConst::PLAYER => $player->getName()],
            function(array $rows) use (&$exists){
                $exists = !empty($rows);
            }
        );
        return $exists;
    }

    /**
     * @param Player $player
     * @return void
     */
    public function createAccount(Player $player): void{
        if(!$this->exists($player)){
            $this->database->executeInsert(
                DatabaseConst::CREATE_PLAYER,
                [
                    DataConst::PLAYER => $player->getName(),
                    DataConst::EVENT_NAME => "",
                    DataConst::COMPLETED => false,
                    DataConst::EGGS => json_encode([])
                ]
            );
        }
    }

    /**
     * @param Player $player
     * @param string $eventId
     * @return void
     */
    public function applyEventById(Player $player, string $eventId): void{
        $this->database->executeChange(
            DatabaseConst::UPDATE_EVENT,
            [
                DataConst::PLAYER => $player->getName(),
                DataConst::EVENT_NAME => $eventId
            ]
        );
    }

    /**
     * @param Player $player
     * @return string
     */
    public function getEventName(Player $player): string{
        $data = $this->getPlayerData($player);
        return $data[DataConst::EVENT_NAME] ?? "N/A";
    }

    /**
     * @param Player $player
     * @return boolean
     */
    public function isCompleted(Player $player): bool{
        $data = $this->getPlayerData($player);
        return boolval($data[DataConst::COMPLETED] ?? false);
    }

    /**
     * @param Player $player
     * @param boolean $result
     * @return boolean
     */
    public function setCompleted(Player $player, bool $result): bool{
        if(!$this->exists($player)) return false;

        $this->database->executeChange(
            DatabaseConst::SET_COMPLETED,
            [
                DataConst::PLAYER => $player->getName(),
                DataConst::COMPLETED => $result
            ]
        );
        return true;
    }

    /**
     * @param Player $player
     * @return integer
     */
    public function getEggs(Player $player): int{
        $data = $this->getPlayerData($player);
        $eggs = json_decode($data[DataConst::EGGS] ?? "[]", true);
        return count($eggs);
    }

    /**
     * @param Player $player
     * @return array
     */
    protected function getEgg(Player $player): array{
        $data = $this->getPlayerData($player);
        return json_decode($data[DataConst::EGGS] ?? "[]", true);
    }

    /**
     * @param Player $player
     * @param string $eggId
     * @return void
     */
    public function addEgg(Player $player, string $eggId): void{
        $eggs = $this->getEgg($player);
        if(in_array($eggId, $eggs, true)) return;

        $eggs[] = $eggId;
        $this->database->executeChange(
            DatabaseConst::UPDATE_EGGS,
            [
                DataConst::PLAYER => $player->getName(),
                DataConst::EGGS => json_encode($eggs)
            ]
        );
    }

    /**
     * @param Player $player
     * @param string $eggId
     * @return void
     */
    public function removeEgg(Player $player, string $eggId): void{
        $eggs = $this->getEgg($player);
        if(!in_array($eggId, $eggs, true)) return;

        $eggs = array_filter($eggs, fn($egg) => $egg !== $eggId);
        $this->database->executeChange(
            DatabaseConst::UPDATE_EGGS,
            [
                DataConst::PLAYER => $player->getName(),
                DataConst::EGGS => json_encode($eggs)
            ]
        );
    }

    /**
     * @return Config
     */
    public function getDatabase(): DataConnector{
        return $this->database;
    }
}
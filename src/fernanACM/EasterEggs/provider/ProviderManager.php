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

use pocketmine\utils\SingletonTrait;

use fernanACM\EasterEggs\EasterEggs as EE;
use fernanACM\EasterEggs\const\StorageConst;
use fernanACM\EasterEggs\provider\type\DatabaseProvider;
use fernanACM\EasterEggs\provider\type\YamlProvider;

final class ProviderManager{
    use SingletonTrait{
        setInstance as protected;
        reset as protected;
    }

    /** @var Provider|null $provider */
    protected ?Provider $provider = null;

    public function __construct(){
        self::setInstance($this);
    }

    /**
     * @return void
     */
    public function init(): void{
        $this->loadProvider();
        $this->provider->load();
    }

    /**
     * @return void
     */
    public function ending(): void{
        $this->provider->unload();
    } 

    /**
     * @return void
     */
    protected function loadProvider(): void{
        $config = EE::getInstance()->config;
        switch(strtolower(strval($config->getNested("Storage.provider")))){
            case StorageConst::YML:
            case StorageConst::YAML:
                $this->provider = new YamlProvider(EE::getInstance());
            break;

            case StorageConst::SQLITE:
            case StorageConst::MYSQL:
                $this->provider = new DatabaseProvider(EE::getInstance());
            break;

            default:
                $this->provider = new YamlProvider(EE::getInstance());
            break;
        }
    }

    /**
     * @return void
     */
    public function reset(): void{
        $this->provider->reset();
    }

    /**
     * @param Player $player
     * @return boolean
     */
    public function exists(Player $player): bool{
        return $this->provider->exists($player);
    }

    /**
     * @param Player $player
     * @return void
     */
    public function create(Player $player): void{
        $this->provider->createAccount($player);
    }

    /**
     * @param Player $player
     * @param string $eventId
     * @return void
     */
    public function applyEventById(Player $player, string $eventId): void{
        $this->provider->applyEventById($player, $eventId);
    }

    /**
     * @param Player $player
     * @return string
     */
    public function getEventName(Player $player): string{
        return $this->provider->getEventName($player);
    }

    /**
     * @param Player $player
     * @param boolean $result
     * @return void
     */
    public function setCompleted(Player $player, bool $result): void{
        $this->provider->setCompleted($player, $result);
    }

    /**
     * @param Player $player
     * @return boolean
     */
    public function isCompleted(Player $player): bool{
        return $this->provider->isCompleted($player);
    }

    /**
     * @param Player $player
     * @return integer
     */
    public function getEggs(Player $player): int{
        return $this->provider->getEggs($player);
    }

    /**
     * @param Player $player
     * @return integer
     */
    public function getLastEggId(Player $player): int{
        return $this->provider->getLastEggId($player);
    }

    /**
     * @param Player $player
     * @param string $eggId
     * @return void
     */
    public function addEgg(Player $player, string $eggId): void{
        $this->provider->addEgg($player, $eggId);
    }

    /**
     * @param Player $player
     * @param string $eggId
     * @return void
     */
    public function removeEgg(Player $player, string $eggId): void{
        $this->provider->removeEgg($player, $eggId);
    }

    /**
     * @param Player $player
     * @param string $eggId
     * @return boolean
     */
    public function claimedEgg(Player $player, string $eggId): bool{
        return $this->provider->claimedEgg($player, $eggId);
    }

    /**
     * @return Provider
     */
    public function getProvider(): Provider{
        return $this->provider;
    }
}
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
     * @return Provider
     */
    public function getProvider(): Provider{
        return $this->provider;
    }
}
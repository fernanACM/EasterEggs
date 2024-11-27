<?php

#      _       ____   __  __ 
#     / \     / ___| |  \/  |
#    / _ \   | |     | |\/| |
#   / ___ \  | |___  | |  | |
#  /_/   \_\  \____| |_|  |_|
# The creator of this plugin was fernanACM.
# https://github.com/fernanACM

declare(strict_types=1);

namespace fernanACM\EasterEggs;

use pocketmine\plugin\PluginBase;

use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;
use pocketmine\utils\SingletonTrait;

class EasterEggs extends PluginBase{
    use SingletonTrait{
        setInstance as protected;
        reset as protected;
    }

    /** @var Config $config */
    public Config $config;

    /**
     * @return void
     */
    public function onLoad(): void{
        self::setInstance($this);
    }

    /**
     * @return void
     */
    public function onEnable(): void{

    }

    /**
     * @return void
     */
    public function onDisable(): void{
    }

    /**
     * @return void
     */
    protected function loadFiles(): void{

    }

    /**
     * @return void
     */
    protected function loadVirions(): void{

    }

    /**
     * @return void
     */
    protected function loadEntities(): void{

    }

    /**
     * @return string
     */
    public static function getPrefix(): string{
        return TextFormat::colorize(self::$instance->config->get("Prefix"));
    }
}

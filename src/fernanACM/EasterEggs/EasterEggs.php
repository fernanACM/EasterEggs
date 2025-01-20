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
# LIBS
use Vecnavium\FormsUI\FormsUI;

use muqsit\invmenu\InvMenu;
use muqsit\invmenu\InvMenuHandler;

use muqsit\simplepackethandler\SimplePacketHandler;

use CortexPE\Commando\PacketHooker;
use CortexPE\Commando\BaseCommand;

use DaPigGuy\libPiggyUpdateChecker\libPiggyUpdateChecker;
# MY FILES
use fernanACM\EasterEggs\forms\FormManager;
use fernanACM\EasterEggs\language\LanguageManager;
use fernanACM\EasterEggs\manager\LootManager;
use fernanACM\EasterEggs\manager\EasterEggManager;
use fernanACM\EasterEggs\provider\ProviderManager;
use fernanACM\EasterEggs\entities\EntityManager;

use fernanACM\EasterEggs\commands\EasterEggCommand;

use fernanACM\EasterEggs\addons\ScoreHudAddon;

use fernanACM\EasterEggs\utils\helper\EventHelper;
use fernanACM\EasterEggs\utils\helper\SetupHelper;

use fernanACM\EasterEggs\utils\SkinUtils;

class EasterEggs extends PluginBase{
    use SingletonTrait{
        setInstance as protected;
        reset as protected;
    }

    /** @var Config $config */
    public Config $config;

    # CheckConfig
    protected const CONFIG_VERSION = "1.0.0";
    protected const LANGUAGE_VERSION = "1.0.0";

    /**
     * @return void
     */
    public function onLoad(): void{
        self::setInstance($this);
        $this->loadFiles();
        $this->loadCheck();
    }

    /**
     * @return void
     */
    public function onEnable(): void{
        $this->loadVirions();
        $this->loadCommands();
        $this->loadEntities();
        $this->loadEvents();
        $this->getLootManager()->loadInventory();
    }

    /**
     * @return void
     */
    public function onDisable(): void{
        $this->getLootManager()->saveInventory();
    }

    /**
     * @return void
     */
    protected function loadFiles(): void{
        $this->saveResource("config.yml");
        $this->config = new Config($this->getDataFolder(). "config.yml");
        $this->getLanguageManager()->init();
        $this->getProviderManager()->init();
        $this->getLootManager()->init();
        EventHelper::init();
        SetupHelper::init();
        SkinUtils::init();
    }

    /**
     * @return void
     */
    protected function loadCheck(): void{
        # CONFIG
        $configUpdated = false;
        if((!$this->config->exists("config-version")) || ($this->config->get("config-version") != self::CONFIG_VERSION)){
            rename($this->getDataFolder() . "config.yml", $this->getDataFolder() . "config_old.yml");
            $this->saveResource("config.yml");
            $this->getLogger()->critical("Your configuration file is outdated.");
            $this->getLogger()->notice("Your old configuration has been saved as config_old.yml and a new configuration file has been generated. Please update accordingly.");
            $configUpdated = true;
        }
        if($configUpdated) $this->config = new Config($this->getDataFolder() . "config.yml");

        # LANGUAGES
        $languageUpdated = false;
        $data = new Config($this->getDataFolder() . "languages/" . $this->config->get("language") . ".yml");
        if((!$data->exists("language-version")) || ($data->get("language-version") != self::LANGUAGE_VERSION)){
            rename($this->getDataFolder() . "languages/" . $this->config->get("language") . ".yml", $this->getDataFolder() . "languages/" . $this->config->get("language") . "_old.yml");
            $this->getLogger()->critical("Your ".$this->config->get("language").".yml file is outdated.");
            $this->getLogger()->notice("Your old ".$this->config->get("language").".yml has been saved as ".$this->config->get("language")."_old.yml and a new ".$this->config->get("language").".yml file has been generated. Please update accordingly.");
            $languageUpdated = true;
        }
        if($languageUpdated) $this->getLanguageManager()->init();
    }

    /**
     * @return void
     */
    protected function loadVirions(): void{
        foreach([
            "FormsUI" => FormsUI::class,
            "SimplePacketHandler" => SimplePacketHandler::class,
            "InvMenu" => InvMenu::class,
            "Commando" => BaseCommand::class,
            "libPiggyUpdateChecker" => libPiggyUpdateChecker::class
            ] as $virion => $class
        ){
            if(!class_exists($class)){
                $this->getLogger()->error($virion . " virion not found. Please download {$this->getName()} from Poggit-CI or use DEVirion (not recommended).");
                $this->getServer()->getPluginManager()->disablePlugin($this);
                return;
            }
        }
        if(!InvMenuHandler::isRegistered()) InvMenuHandler::register($this);
        if(!PacketHooker::isRegistered()) PacketHooker::register($this);
        # Update
        libPiggyUpdateChecker::init($this);
    }

    /**
     * @return void
     */
    protected function loadCommands(): void{
        $this->getServer()->getCommandMap()->register("eastereggs", new EasterEggCommand);
    }

    /**
     * @return void
     */
    protected function loadEntities(): void{
        $this->getEntityManager()->init();
    }

    /**
     * @return void
     */
    protected function loadEvents(): void{
        $this->getServer()->getPluginManager()->registerEvents(new Event, $this);
        ScoreHudAddon::init();
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

    /**
     * @return EntityManager
     */
    public function getEntityManager(): EntityManager{
        return EntityManager::getInstance();
    }

    /**
     * @return FormManager
     */
    public function getFormManager(): FormManager{
        return FormManager::getInstance();
    }

    /**
     * @return LanguageManager
     */
    public function getLanguageManager(): LanguageManager{
        return LanguageManager::getInstance();
    }

    /**
     * @return ProviderManager
     */
    public function getProviderManager(): ProviderManager{
        return ProviderManager::getInstance();
    }

    /**
     * @return string
     */
    public static function getPrefix(): string{
        return TextFormat::colorize(self::$instance->config->get("Prefix"));
    }
}
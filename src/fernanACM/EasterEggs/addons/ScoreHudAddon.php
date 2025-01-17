<?php

#      _       ____   __  __ 
#     / \     / ___| |  \/  |
#    / _ \   | |     | |\/| |
#   / ___ \  | |___  | |  | |
#  /_/   \_\  \____| |_|  |_|
# The creator of this plugin was fernanACM.
# https://github.com/fernanACM

declare(strict_types=1);

namespace fernanACM\EasterEggs\addons;

use pocketmine\Server;
use pocketmine\player\Player;

use pocketmine\event\Listener;

use Ifera\ScoreHud\event\TagsResolveEvent;
use Ifera\ScoreHud\event\PlayerTagUpdateEvent;
use Ifera\ScoreHud\event\ServerTagsUpdateEvent;

use Ifera\ScoreHud\scoreboard\ScoreTag;

use fernanACM\EasterEggs\EasterEggs as EE;

use fernanACM\EasterEggs\manager\EasterEggManager;
use fernanACM\EasterEggs\provider\ProviderManager;

use fernanACM\EasterEggs\events\PlayerAddPointEvent;
use fernanACM\EasterEggs\events\PlayerRemovePointEvent;

class ScoreHudAddon implements Listener{

    /** @var bool $success */
    protected static $success = true;

    public const EGGS = "eastereggs.eggs";
    public const GOAL = "eastereggs.goal";
    public const EVENT_NAME = "eastereggs.event-name";

    /**
     * @return void
     */
    public static function init(): void{
        $server = Server::getInstance();
        $pluginManager = $server->getPluginManager();
        if(!is_null(($scorehud = $pluginManager->getPlugin("ScoreHud")))){
            $version = $scorehud->getDescription()->getVersion();
            if(version_compare($version, "6.0.0", "<")){
                Server::getInstance()->getLogger()->warning("Outdated version of ScoreHud (v" . $version . ") detected, requires >= v6.0.0. Integration disabled.");
                self::$success = false;
                return;
            }
            $pluginManager->registerEvents(new self, EE::getInstance());
            self::$success = true;
        }else{
            $server->getLogger()->warning("ScoreHud plugin not found. Integration disabled.");
            self::$success = false;
        }
    }
    
    /**
     * @param Player $player
     * @return void
     */
    public static function onUpdate(?Player $player = null): void{
        $manager = EasterEggManager::getInstance();
        if(!self::$success) return;

        if(!is_null($player)){
            $eggs = ProviderManager::getInstance()->exists($player) ? strval($manager->eggs($player)) : "0";
            (new PlayerTagUpdateEvent($player, new ScoreTag(self::EGGS, strval($eggs))))->call();
        }else{
            (new ServerTagsUpdateEvent([
                new ScoreTag(self::GOAL, strval($manager->eggLimit())),
                new ScoreTag(self::EVENT_NAME, strval($manager->eventName())),
            ]))->call();
        }
    }

    /**
     * @param TagsResolveEvent $event
     * @return void
     */
    public function onTagResolve(TagsResolveEvent $event): void{
        $tag = $event->getTag();
        $manager = EasterEggManager::getInstance();
        $provider = ProviderManager::getInstance();
        switch($tag->getName()){
            case self::EGGS:
                $tag->setValue($provider->exists($event->getPlayer()) ? strval($manager->eggs($event->getPlayer())) : "0");
            break;

            case self::GOAL:
                $tag->setValue(strval($manager->eggLimit()));
            break;

            case self::EVENT_NAME:
                $tag->setValue(strval($manager->eventName()));
            break;
        }
    }

    /**
     * @param PlayerAddPointEvent $event
     * @return void
     */
    public function onAddPoint(PlayerAddPointEvent $event): void{
        self::onUpdate($event->getPlayer());
    }

    /**
     * @param PlayerRemovePointEvent $event
     * @return void
     */
    public function onRemovePoint(PlayerRemovePointEvent $event): void{
        self::onUpdate($event->getPlayer());
    }
}
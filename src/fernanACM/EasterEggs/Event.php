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

use pocketmine\event\Listener;

use pocketmine\block\Block;

use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerLoginEvent;

use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;

use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;

use fernanACM\EasterEggs\EasterEggs as EE;
use fernanACM\EasterEggs\entities\EasterEggEntity;
use fernanACM\EasterEggs\helper\SetupHelper as SH;

use fernanACM\EasterEggs\language\LangKey;
use fernanACM\EasterEggs\language\Language;
use fernanACM\EasterEggs\permissions\Perms;

class Event implements Listener{

    /** @var array $cooldown */
    protected static array $cooldown = [];

    /**
     * @param PlayerLoginEvent $event
     * @return void
     */
    public function onLogin(PlayerLoginEvent $event): void{
        $player = $event->getPlayer();
        $provider = EE::getInstance()->getProviderManager();
        if(!$provider->exists($player)){
            $provider->createAccount($player);
        }
    }

    /**
     * @param PlayerJoinEvent $event
     * @return void
     */
    public function onJoin(PlayerJoinEvent $event): void{
        $player = $event->getPlayer();
        if(SH::inSetupMode($player)) SH::exitSetupMode($player);
    }

    /**
     * @param PlayerQuitEvent $event
     * @return void
     */
    public function onQuit(PlayerQuitEvent $event): void{
        $player = $event->getPlayer();
        if(SH::inSetupMode($player)) SH::exitSetupMode($player);
    }

    /**
     * @param BlockBreakEvent $event
     * @return void
     */
    public function onBreak(BlockBreakEvent $event): void{
        $block = $event->getBlock();
        $player = $event->getPlayer();
        if(!$player->hasPermission(Perms::ENTER_SETUP_MODE) and !SH::inSetupMode($player)) return;

        if(SH::eggExists($block)){
            Language::isError($player, LangKey::ERROR_EXISTING_EGG);
            return;
        }

        SH::addEgg($player, $block); // ADD LOCATION
        $event->cancel(); // CANCEL BLOCK BREAK
    }

    /**
     * @param PlayerInteractEvent $event
     * @return void
     */
    public function onInteract(PlayerInteractEvent $event): void{
        $player = $event->getPlayer();
        $block = $event->getBlock();
        $item = $event->getItem();
        $action = $event->getAction();
        if($action !== PlayerInteractEvent::RIGHT_CLICK_BLOCK) return;

        if(!$player->hasPermission(Perms::USE_REMOVER_WAND) || !$item->equals(SH::removeWand())) return;

        if((self::$cooldown[$player->getName()] ?? .0) < microtime(true)){
            self::$cooldown[$player->getName()] = microtime(true) + 0.2;
        }else return;
        
        if(!SH::eggExists($block)){
            Language::isError($player, LangKey::ERROR_NO_EXISTING_EGGS);
            return;
        }

        SH::removeEgg($player, $block); // REMOVE LOCATION
        $world = $block->getPosition()->getWorld();
        $blockBounds = $block->getCollisionBoxes();
        if(empty($blockBounds)){
            return;
        }
        foreach($world->getEntities() as $entity){
            if(!$entity instanceof EasterEggEntity){
                continue;
            }
            foreach($blockBounds as $bound){
                if($entity->getBoundingBox()->intersectsWith($bound)){
                    $entity->flagForDespawn(); // MAKE THE ENTITY DISAPPEAR
                    break;
                }
            }
        }
    }

    /**
     * @param BlockPlaceEvent $event
     * @return void
     */
    public function onPlace(BlockPlaceEvent $event): void{
        $transaction = $event->getTransaction();
        foreach($transaction->getBlocks() as [$x, $y, $z, $block]){
            /** @var Block $block */
            if(SH::eggExists($block)) $event->cancel();
        }
    }
}
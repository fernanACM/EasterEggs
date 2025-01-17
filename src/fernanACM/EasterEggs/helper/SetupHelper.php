<?php

#      _       ____   __  __ 
#     / \     / ___| |  \/  |
#    / _ \   | |     | |\/| |
#   / ___ \  | |___  | |  | |
#  /_/   \_\  \____| |_|  |_|
# The creator of this plugin was fernanACM.
# https://github.com/fernanACM

declare(strict_types=1);

namespace fernanACM\EasterEggs\helper;

use pocketmine\player\Player;

use pocketmine\utils\Config;

use pocketmine\block\Block;

use pocketmine\nbt\tag\CompoundTag;

use pocketmine\item\Item;
use pocketmine\item\VanillaItems;

use fernanACM\EasterEggs\const\DataConst;
use fernanACM\EasterEggs\const\NBTConst;
use fernanACM\EasterEggs\EasterEggs as EE;

use fernanACM\EasterEggs\language\LangKey;
use fernanACM\EasterEggs\language\Language;

final class SetupHelper{

    /** @var Config $config */
    protected static Config $config;
    /** @var string[] $setup */
    protected static array $setup = [];

    protected const FILE_NAME = "eggsData.yml";

    /**
     * @return void
     */
    public static function init(): void{
        self::$config = new Config(EE::getInstance()->getDataFolder(). self::FILE_NAME, Config::YAML, [
            DataConst::EGGS => []
        ]);
    }

    /**
     * @param Block $block
     * @return boolean
     */
    public static function eggExists(Block $block): bool{
        $x = (int)$block->getPosition()->x;
        $y = (int)$block->getPosition()->y;
        $z = (int)$block->getPosition()->z;
        $world = (string)$block->getPosition()->getWorld()->getFolderName();
        $positions = self::getEggs();
        $positionString = "$x:$y:$z:$world";
        return in_array($positionString, $positions);
    }

    /**
     * @param Block $block
     * @return void
     */
    public static function addEgg(Player $player, Block $block): void{
        if(!self::inSetupMode($player)) return;
        $x = (int)$block->getPosition()->x;
        $y = (int)$block->getPosition()->y;
        $z = (int)$block->getPosition()->z;
        $world = (string)$block->getPosition()->getWorld()->getFolderName();
        $positions = self::$config->get(DataConst::EGGS, []);
        $positions[] = "$x:$y:$z:$world";
        self::$config->set(DataConst::EGGS, $positions);
        self::$config->save();
        Language::isSuccess($player, LangKey::SUCCESS_SAVED_POSITION, [
            "{X}" => $x, "{Y}" => $y, "{Z}" => $z, "{WORLD}" => $world]);
        // PROGRESS
        $eggs = count(self::getEggs());
        $limit = EE::getInstance()->getEasterEggManager()->eggLimit();
        $player->sendMessage(EE::getPrefix().Language::getPlayerMessage($player, LangKey::SUCCESS_SETUP_PROGRESS, [
            "{EGGS}" => $eggs,
            "{LIMIT}" => $limit
        ]));
        // LIMIT
        $manager = EE::getInstance()->getEasterEggManager();
        if($manager->fullEggs()){
            Language::isSuccess($player, LangKey::SUCCESS_SETUP_COMPLETED);
            if(self::inSetupMode($player)) self::exitSetupMode($player);
        }
    }

    /**
     * @param Player $player
     * @param Block $block
     * @return void
     */
    public static function removeEgg(Player $player, Block $block): void{
        $x = (int)$block->getPosition()->x;
        $y = (int)$block->getPosition()->y;
        $z = (int)$block->getPosition()->z;
        $world = (string)$block->getPosition()->getWorld()->getFolderName();
        $positions = self::getEggs();
        $positionString = "$x:$y:$z:$world";
        $key = array_search($positionString, $positions);
        if($key !== false){
            unset($positions[$key]);
            self::$config->set(DataConst::EGGS, array_values($positions));
            self::$config->save();
            Language::isSuccess($player, LangKey::SUCCESS_REMOVED_POSITION, [
                "{X}" => $x, "{Y}" => $y, "{Z}" => $z, "{WORLD}" => $world]);
        }
    }

    /**
     * @return string[]
     */
    public static function getEggs(): array{
        return (array)self::$config->get(DataConst::EGGS, []);
    }

    /**
     * @return Item
     */
    public static function removeWand(): Item{
        $item = VanillaItems::BLAZE_ROD();
        $item->setNamedTag(CompoundTag::create()->setString(NBTConst::EASTER_EGGS, NBTConst::REMOVE));
        $item->setCustomName(Language::getMessage(LangKey::ITEM_NAME));
        return $item;
    }

    /**
     * @param Player $player
     * @return boolean
     */
    public static function inSetupMode(Player $player): bool{
        return isset(self::$setup[$player->getXuid()]);
    }

    /**
     * @param Player $player
     * @param boolean $msg
     * @return void
     */
    public static function toggleSetupMode(Player $player, bool $msg = true): void{
        if(self::inSetupMode($player)){
            self::exitSetupMode($player, $msg);
        }else self::setSetupMode($player, $msg);
    }

    /**
     * @param Player $player
     * @param boolean $msg
     * @return void
     */
    public static function setSetupMode(Player $player, bool $msg = true): void{
        if(self::inSetupMode($player)) return;
        self::$setup[$player->getXuid()] = true;
        if($msg) Language::isSuccess($player, LangKey::SUCCESS_ENTER_SETUP_MODE, [
            "{EGG_LIMIT}" => EE::getInstance()->getEasterEggManager()->eggLimit()
        ]);
    }

    /**
     * @param Player $player
     * @param boolean $msg
     * @return void
     */
    public static function exitSetupMode(Player $player, bool $msg = true): void{
        if(!self::inSetupMode($player)) return;
        unset(self::$setup[$player->getXuid()]);
        if($msg) Language::isSuccess($player, LangKey::SUCCESS_EXIT_SETUP_MODE);
    }

    /**
     * Get all Player Xuid
     * 
     * @return string[]
     */
    public static function getAllSetupMode(): array{
        return array_keys(self::$setup);
    }
}
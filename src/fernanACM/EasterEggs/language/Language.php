<?php

#      _       ____   __  __ 
#     / \     / ___| |  \/  |
#    / _ \   | |     | |\/| |
#   / ___ \  | |___  | |  | |
#  /_/   \_\  \____| |_|  |_|
# The creator of this plugin was fernanACM.
# https://github.com/fernanACM

declare(strict_types=1);

namespace fernanACM\EasterEggs\language;

use pocketmine\player\Player;

use pocketmine\utils\TextFormat;

use fernanACM\EasterEggs\EasterEggs as EE;
use fernanACM\EasterEggs\utils\PluginUtils;
use fernanACM\EasterEggs\language\LanguageManager;

final class Language{

    /**
     * @param Player $player
     * @param string $key
     * @param array $replaces
     * @return string
     */
    public static function getPlayerMessage(Player $player, string $key, array $replaces = []): string{
        $messageArray = LanguageManager::getInstance()->getConfig()->getNested($key, []);
        if(!is_array($messageArray)){
            $messageArray = [$messageArray];
        }
        $message = implode("\n", $messageArray);
        foreach($replaces as $search => $replace){
            $message = str_replace($search, (string)$replace, $message);
        }
        return PluginUtils::codeUtil($player, $message);
    }

    /**
     * @param string $key
     * @param array $replaces
     * @return string
     */
    public static function getMessage(string $key, array $replaces = []): string{
        $messageArray = LanguageManager::getInstance()->getConfig()->getNested($key, []);
        if(!is_array($messageArray)){
            $messageArray = [$messageArray];
        }
        $message = implode("\n", $messageArray);
        foreach($replaces as $search => $replace){
            $message = str_replace($search, (string)$replace, $message);
        }
        return TextFormat::colorize($message);
    }

    /**
     * @param Player $player
     * @param string $key
     * @param array $replaces
     * @param boolean $sound
     * @return void
     */
    public static function isError(Player $player, string $key, array $replaces = [], bool $sound = true): void{
        $player->sendMessage(EE::getPrefix().self::getPlayerMessage($player, $key, $replaces));
        if($sound) PluginUtils::PlaySound($player, "mob.villager.no");
    }

    /**
     * @param Player $player
     * @param string $key
     * @param array $replaces
     * @param boolean $sound
     * @return void
     */
    public static function isSuccess(Player $player, string $key, array $replaces = [], bool $sound = true): void{
        $player->sendMessage(EE::getPrefix().self::getPlayerMessage($player, $key, $replaces));
        if($sound) PluginUtils::PlaySound($player, "mob.villager.yes");
    }
}
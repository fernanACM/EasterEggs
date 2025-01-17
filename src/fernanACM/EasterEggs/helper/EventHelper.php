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

use pocketmine\utils\Config;

use fernanACM\EasterEggs\EasterEggs as EE;
use fernanACM\EasterEggs\const\EventConst;

use fernanACM\EasterEggs\addons\ScoreHudAddon;

final class EventHelper{

    /** @var Config $config */
    protected static Config $config;

    protected const FILE_NAME = "events.yml";

    /**
     * @return void
     */
    public static function init(): void{
        self::$config = new Config(EE::getInstance()->getDataFolder(). self::FILE_NAME, Config::YAML, [
            EventConst::EVENTS => ["EasterEggs", "ChristmasACM", "Valentine's Day"],
            EventConst::CURRENT_EVENT => "EasterEggs"
        ]);
    }

    /**
     * @param string $eventName
     * @return boolean
     */
    public static function exists(string $eventName): bool{
        return in_array($eventName, (array)self::$config->get(EventConst::EVENTS, []), true);
    }

    /**
     * @param string $eventName
     * @return void
     */
    public static function create(string $eventName): void{
        if(!self::exists($eventName)){
            $events = (array)self::$config->get(EventConst::EVENTS, []);
            $events[] = $eventName;
            self::$config->set(EventConst::EVENTS, $events);
            self::$config->save();
            ScoreHudAddon::onUpdate();
        }
    }

    /**
     * @param string $eventName
     * @return void
     */
    public static function delete(string $eventName): void{
        if(self::exists($eventName)){
            $events = (array)self::$config->get(EventConst::EVENTS, []);
            $events = array_filter($events, fn($event) => $event !== $eventName);
            self::$config->set(EventConst::EVENTS, array_values($events));
            self::$config->save();
            ScoreHudAddon::onUpdate();
        }
    }

    /**
     * @return void
     */
    public static function reset(): void{
        self::$config->set(EventConst::EVENTS, ["EasterEggs", "ChristmasACM", "Valentine's Day"]);
        self::$config->set(EventConst::CURRENT_EVENT, "EasterEggs");
        self::$config->save();
        ScoreHudAddon::onUpdate();
    }

    /**
     * @return string[]
     */
    public static function all(): array{
        return (array)self::$config->get(EventConst::EVENTS, []);
    }

    /**
     * @param string $eventName
     * @return void
     */
    public static function set(string $eventName): void{
        if(self::exists($eventName)){
            self::$config->set(EventConst::CURRENT_EVENT, $eventName);
            self::$config->save();
            ScoreHudAddon::onUpdate();
        }
    }

    /**
     * @return string|null
     */
    public static function currentEvent(): ?string{
        $event = self::$config->get(EventConst::CURRENT_EVENT, "");
        return $event !== "" ? $event : null;
    }
}
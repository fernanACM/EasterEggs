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

final class EventHelper{

    /** @var Config $config */
    protected static Config $config;

    protected const FILE_NAME = "events.yml";

    /**
     * @return void
     */
    public static function init(): void{
        self::$config = new Config(EE::getInstance()->getDataFolder(). self::FILE_NAME, Config::YAML, [
            "events" => ["ChristmasACM", "Valentine's Day"]
        ]);
    }

    /**
     * @param string $eventName
     * @return boolean
     */
    public static function exists(string $eventName): bool{
        return in_array($eventName, (array)self::$config->get("events", []), true);
    }

    /**
     * @param string $eventName
     * @return void
     */
    public static function create(string $eventName): void{
        if(!self::exists($eventName)){
            $events = (array)self::$config->get("events", []);
            $events[] = $eventName;
            self::$config->set("events", $events);
            self::$config->save();
        }
    }

    /**
     * @param string $eventName
     * @return void
     */
    public static function delete(string $eventName): void{
        if(self::exists($eventName)){
            $events = (array)self::$config->get("events", []);
            $events = array_filter($events, fn($event) => $event !== $eventName);
            self::$config->set("events", array_values($events));
            self::$config->save();
        }
    }

    /**
     * @return void
     */
    public static function reset(): void{
        self::$config->set("events", ["ChristmasACM", "Valentine's Day"]);
        self::$config->save();
    }

    /**
     * @return string[]
     */
    public static function all(): array{
        return (array)self::$config->get("events", []);
    }
}
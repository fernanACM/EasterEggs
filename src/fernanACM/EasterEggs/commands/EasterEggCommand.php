<?php

#      _       ____   __  __ 
#     / \     / ___| |  \/  |
#    / _ \   | |     | |\/| |
#   / ___ \  | |___  | |  | |
#  /_/   \_\  \____| |_|  |_|
# The creator of this plugin was fernanACM.
# https://github.com/fernanACM

declare(strict_types=1);

namespace fernanACM\EasterEggs\commands;

use pocketmine\player\Player;

use pocketmine\command\CommandSender;

use CortexPE\Commando\BaseCommand;

use fernanACM\EasterEggs\EasterEggs as EE;
use fernanACM\EasterEggs\permissions\Perms;

use fernanACM\EasterEggs\language\LangKey;
use fernanACM\EasterEggs\language\Language;

use fernanACM\EasterEggs\commands\subcommands\EventsSubCommand;
use fernanACM\EasterEggs\commands\subcommands\LootSubCommand;
use fernanACM\EasterEggs\commands\subcommands\SetupSubCommand;

use fernanACM\EasterEggs\utils\PluginUtils;

class EasterEggCommand extends BaseCommand{

    public function __construct(){
        parent::__construct(EE::getInstance(), "eastereggs", "EasterEggs by fernanACM", ["ee", "easter", "eggs"]);
        $this->setPermission(Perms::MAIN_COMMAND);
    }

    /**
     * @return void
     */
    protected function prepare(): void{
        $commands = [new SetupSubCommand, new EventsSubCommand, new LootSubCommand];
        foreach($commands as $command){
            $this->registerSubCommand($command);
        }
    }

    /**
     * @param CommandSender $sender
     * @param string $aliasUsed
     * @param array $args
     * @return void
     */
    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void{
        if(!$sender instanceof Player){
            $sender->sendMessage("Use this command in-game");
            return;
        }
        if(!$sender->hasPermission(Perms::MAIN_COMMAND)){
            Language::isError($sender, LangKey::ERROR_NO_PERMISSION);
            return;
        }
        $sender->sendMessage("§l§b»EasterEggs«");
        $sender->sendMessage("§7» /eastereggs - Command list");
        $sender->sendMessage("§7» /eastereggs setup - Setup manage");
        $sender->sendMessage("§7» /eastereggs events - Events manage");
        $sender->sendMessage("§7» /eastereggs loot - Edit loot\n");
        $sender->sendMessage("§l§cYOUTUBE:§r§e fernanACM");
        $sender->sendMessage("§l§9DISCORD:§r§e fernanacm");
        $sender->sendMessage("§l§7GITHUB:§r§e fernanACM");
        PluginUtils::PlaySound($sender, "random.pop2", 1, 1);
    }
}
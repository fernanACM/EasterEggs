<?php

#      _       ____   __  __ 
#     / \     / ___| |  \/  |
#    / _ \   | |     | |\/| |
#   / ___ \  | |___  | |  | |
#  /_/   \_\  \____| |_|  |_|
# The creator of this plugin was fernanACM.
# https://github.com/fernanACM

declare(strict_types=1);

namespace fernanACM\EasterEggs\manager;

use pocketmine\Server;
use pocketmine\player\Player;

use pocketmine\utils\TextFormat as TF;
use pocketmine\utils\SingletonTrait;

use pocketmine\console\ConsoleCommandSender;

use fernanACM\EasterEggs\EasterEggs as EE;

use fernanACM\EasterEggs\language\LangKey;
use fernanACM\EasterEggs\language\Language;

use fernanACM\EasterEggs\helper\SetupHelper;

use fernanACM\EasterEggs\utils\PluginUtils;

use fernanACM\EasterEggs\events\PlayerClaimEvent;
use fernanACM\EasterEggs\events\PlayerAddPointEvent;
use fernanACM\EasterEggs\events\PlayerRewardEvent;
use fernanACM\EasterEggs\events\PlayerRemovePointEvent;
use fernanACM\EasterEggs\provider\ProviderManager;

final class EasterEggManager{
    use SingletonTrait{
        setInstance as protected;
        reset as protected;
    }

    public function __construct(){
        self::setInstance($this);
    }

    /**
     * Provides the number of Easter Eggs the player has obtained.
     * 
     * @param Player $player
     * @return integer
     */
    public function eggs(Player $player): int{
        $provider = EE::getInstance()->getProviderManager();
        return $provider->exists($player) ? $provider->getEggs($player) : 0;
    }

    /**
     * Adds an egg to a player's progress, validating various criteria such as the egg limit, whether 
     * it has already been claimed, or whether the progress is complete. If the egg limit 
     * is reached, an optional reward is executed. Additionally, it provides feedback to the player 
     * through messages and sound effects.
     * 
     * @param Player $player
     * @param string|null $eggId
     * @param boolean $reward
     * @param boolean $message
     * @return void
     */
    public function add(Player $player, ?string $eggId = null, bool $reward = true, bool $message = true): void{
        $provider = EE::getInstance()->getProviderManager();
        if(!$provider->exists($player)) return;

        if(!$this->fullEggs()){
            Language::isError($player, LangKey::ERROR_EGGS_ARE_MISSING);
            return;
        }

        if($this->isCompleted($player)){
            Language::isError($player, LangKey::ERROR_IT_IS_COMPLETED);
            return;
        }

        if(is_null($eggId)){
            $lastEggId = $provider->getLastEggId($player);
            $eggId = $this->idNumToEggId($lastEggId + 1);
        }
        
        if($this->claimedEgg($player, $eggId)){
            Language::isError($player, LangKey::ERROR_IT_IS_CLAIMED);
            return;
        }

        // LIMIT
        $eggLimit = $this->eggLimit();
        if($this->eggs($player) >= $eggLimit){
            Language::isError($player, LangKey::ERROR_REACHED_THE_LIMIT);
            return;
        }
        $this->claim($player, function(bool $result) use($provider, $player, $eggId, $reward, $message): void{
            if($result){
                $provider->addEgg($player, $eggId); // SUCCESS - ADDED POINT
                Language::isSuccess($player, LangKey::SUCCESS_ADDED_POINT, [], false);
                PluginUtils::PlaySound($player, "random.pop2", 1, 5.6);
                (new PlayerAddPointEvent($eggId))->call(); // CALL EVENT
            }else{
                if($reward){
                    $this->reward($player, $message); // REWARD
                    
                }
                $provider->setCompleted($player, true); // COMPLETED
            }
        });
    }

    /**
     * Evaluates whether a player can claim a new egg based on current progress 
     * and the maximum limit allowed. Provides the evaluation result through a callback.
     * 
     * @param Player $player
     * @param callable $callable
     * @return void
     */
    public function claim(Player $player, callable $callable): void{
        $eggLimit = $this->eggLimit();
        if($this->eggs($player) >= $eggLimit){
            $callable(false);
            return;
        }
        $callable(true);
        $num = ProviderManager::getInstance()->getLastEggId($player);
        $eggId = $this->idNumToEggId($num);
        (new PlayerClaimEvent($eggId))->call(); // CALL EVENT
    }

    /**
     * Provides rewards to a player as part of the Easter Egg system. Rewards may include 
     * items, command execution, broadcast messages, player titles, and more, depending 
     * on the setting.
     * 
     * @param Player $player
     * @param boolean $message
     * @return void
     */
    public function reward(Player $player, bool $message = true): void{
        $config = EE::getInstance()->config;
        $lootManager = EE::getInstance()->getLootManager();
        $min_items = intval($config->getNested("Settings.EasterEgg.Reward.Items.min"));
        $max_items = intval($config->getNested("Settings.EasterEgg.Reward.Items.max"));
        $items = rand($min_items, $max_items);
        # (ITEMS)
        if(boolval($config->getNested("Settings.EasterEgg.Reward.Items.receive", true))){
            foreach($lootManager->getRandomItems($items) as $reward){
                if(!$player->getInventory()->canAddItem($reward)){
                    $player->getWorld()->dropItem($player->getPosition(), $reward);
                }else $player->getInventory()->addItem($reward);
            }
        }
        # (COMMANDS)
        $server = Server::getInstance();
        if(boolval($config->getNested("Settings.EasterEgg.Reward.Commands.execute", true))){
            foreach($lootManager->getCommands() as $command){
                $command = str_replace("{PLAYER}", $player->getName(), $command);
                $server->dispatchCommand(new ConsoleCommandSender($server, $server->getLanguage()), $command);
            }
        }
        # (BROADCASTER)
        $provider = EE::getInstance()->getProviderManager();
        if(boolval($config->getNested("Settings.EasterEgg.Reward.broadcast", true))){
            $message = TF::colorize($config->getNested("Settings.EasterEgg.Reward.broadcast-message"));
            $message = str_replace(["{PLAYER}", "{EGGS}"], [$player->getName(), $this->eggs($player)], $message);
            $server->broadcastMessage(EE::getPrefix().$message);
        }
        # (PLAYER TITLES)
        if(boolval($config->getNested("Settings.EasterEgg.Reward.player-titles", true))){
            $title = Language::getPlayerMessage($player, LangKey::REWARD_TITLE);
            $subtitle = Language::getPlayerMessage($player, LangKey::REWARD_SUBTITLE);
            $player->sendTitle($title, $subtitle);
        }

        if($message){
            Language::isSuccess($player, LangKey::SUCCESS_REWARD_RECEIVED, [], false);
            PluginUtils::PlaySound($player, "random.levelup", 1, 2.3);
        }
        (new PlayerRewardEvent)->call(); // CALL EVENT
    }

    /**
     * Remove an egg previously claimed by a player from the Easter Egg system. 
     * This feature also updates the player's progress accordingly and notifies the change.
     * 
     * @param Player $player
     * @param integer $numId
     * @return void
     */
    public function remove(Player $player, int $numId): void{
        $eggId = $this->idNumToEggId($numId);
        $provider = EE::getInstance()->getProviderManager();
        if($this->isCompleted($player)){
            Language::isError($player, LangKey::ERROR_IT_IS_COMPLETED);
            return;
        }

        if(!$this->claimedEgg($player, $eggId)){
            Language::isError($player, LangKey::ERROR_UNCLAIMED_EGG);
            return;
        }
        $provider->removeEgg($player, $eggId); // SUCCESS - POINT REMOVED
        Language::isSuccess($player, LangKey::SUCCESS_ADDED_POINT, [], false);
        PluginUtils::PlaySound($player, "mob.irongolem.death", 1, 2.1);
        (new PlayerRemovePointEvent($eggId))->call(); // CALL EVENT
    }

    /**
     * Converts an integer to the identification format used for Easter Eggs in the system.
     * 
     * @param integer $num
     * @return string
     */
    public function idNumToEggId(int $num): string{
        return "egg(".($num).")";
    }

    /**
     * Provides you the limit of Easter Eggs
     * 
     * @return integer
     */
    public function eggLimit(): int{
        return intval(EE::getInstance()->config->getNested("Settings.EasterEgg.egg-limit"));
    }

    /**
     * Analyze if the Easter Eggs Setup has been completed.
     * 
     * @return boolean
     */
    public function fullEggs(): bool{
        $eggs = SetupHelper::getEggs();
        $eggLimit = $this->eggLimit();
        return is_array($eggs) && count($eggs) === $eggLimit;
    }

    /**
     * Checks if the player has completed the Easter Eggs objective.
     * 
     * @param Player $player
     * @return boolean
     */
    public function isCompleted(Player $player): bool{
        $provider = EE::getInstance()->getProviderManager();
        return $provider->exists($player) ? $provider->isCompleted($player) : false;
    }

    /**
     * Checks if a specific player has already claimed an egg with a given ID.
     * 
     * @param Player $player
     * @param string $eggId
     * @return boolean
     */
    public function claimedEgg(Player $player, string $eggId): bool{
        $provider = EE::getInstance()->getProviderManager();
        return $provider->exists($player) ? $provider->claimedEgg($player, $eggId) : false;
    }
}
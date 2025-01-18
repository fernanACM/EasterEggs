<?php

#      _       ____   __  __ 
#     / \     / ___| |  \/  |
#    / _ \   | |     | |\/| |
#   / ___ \  | |___  | |  | |
#  /_/   \_\  \____| |_|  |_|
# The creator of this plugin was fernanACM.
# https://github.com/fernanACM

declare(strict_types=1);

namespace fernanACM\EasterEggs\forms\type;

use pocketmine\player\Player;

use pocketmine\utils\TextFormat as TF;
use pocketmine\utils\SingletonTrait;

use Vecnavium\FormsUI\SimpleForm;
use Vecnavium\FormsUI\ModalForm;

use fernanACM\EasterEggs\EasterEggs as EE;

use fernanACM\EasterEggs\language\LangKey;
use fernanACM\EasterEggs\language\Language;

use fernanACM\EasterEggs\helper\SetupHelper as SH;
use fernanACM\EasterEggs\utils\PluginUtils;

final class SetupForm{
    use SingletonTrait{
        setInstance as protected;
        reset as protected;
    }

    public function __construct(){
        self::setInstance($this);
    }

    /**
     * @param Player $player
     * @return void
     */
    public function open(Player $player): void{
        $inSetupMode = SH::inSetupMode($player);
        $form = new SimpleForm(function(Player $player, $data): void{
            if(is_null($data)){
                PluginUtils::PlaySound($player, "random.pop2", 1, 5.3);
                return;
            }
            switch($data){
                case 0: // ENTER / LEAVE SETUP MODE
                    $manager = EE::getInstance()->getEasterEggManager();
                    if($manager->fullEggs()){
                        Language::isSuccess($player, LangKey::SUCCESS_SETUP_COMPLETED);
                        return;
                    }
                    SH::toggleSetupMode($player);
                    PluginUtils::PlaySound($player, "random.pop", 1, 5.3);
                break;
                
                case 1; // REMOVE / WAND
                    $wand = SH::removeWand();
                    if(!$player->getInventory()->canAddItem($wand)){
                        Language::isError($player, LangKey::ERROR_FULL_INVENTORY);
                        return;
                    }
                    $player->getInventory()->addItem($wand);
                    PluginUtils::PlaySound($player, "random.pop", 1, 5.3);
                break;

                case 2: // LOCATIONS
                    $this->locations($player);
                    PluginUtils::PlaySound($player, "random.pop", 1, 5.3);
                break;

                case 3: // ENTITIES
                    $this->entities($player);
                    PluginUtils::PlaySound($player, "random.pop", 1, 5.3);
                break;

                case 4: // RESET DATA
                    $this->confirm($player, function(bool $result) use($player): void{
                        if(!$result) return;
                        $provider = EE::getInstance()->getProviderManager();
                        $provider->reset(); // RESET DATA
                        // NEW PROFILE
                        foreach($player->getServer()->getOnlinePlayers() as $target){
                            if(!$provider->exists($target)){
                                $provider->createAccount($target);
                            }
                        }
                        Language::isSuccess($player, LangKey::SUCCESS_SETUP_RESET_DATA);
                    });
                    PluginUtils::PlaySound($player, "random.pop", 1, 5.3);
                break;

                case 5: // CLOSE
                break;
            }
        });
        $form->setTitle(TF::colorize("&l&9EASTEREGGS"));
        $form->setContent(Language::getPlayerMessage($player, LangKey::SETUP_FORM_CONTENT));
        if($inSetupMode){
            $form->addButton(Language::getPlayerMessage($player, LangKey::SETUP_FORM_TOGGLE1_BUTTON),0,"textures/ui/icon_agent"); // ENABLE
        }else $form->addButton(Language::getPlayerMessage($player, LangKey::SETUP_FORM_TOGGLE2_BUTTON),0,"textures/ui/icon_agent"); // DISABLE
        $form->addButton(Language::getPlayerMessage($player, LangKey::SETUP_FORM_REMOVE_BUTTON),0,"textures/items/blaze_rod");
        $form->addButton(Language::getPlayerMessage($player, LangKey::SETUP_FORM_LOCATIONS_BUTTON),0,"textures/ui/worldsIcon");
        $form->addButton(Language::getPlayerMessage($player, LangKey::SETUP_FORM_ENTITY_BUTTON),0,"textures/ui/icon_panda");
        $form->addButton(Language::getPlayerMessage($player, LangKey::SETUP_FORM_RESET_DATA_BUTTON),0,"textures/ui/recap_glyph_color_2x");
        $form->addButton(Language::getPlayerMessage($player, LangKey::SETUP_FORM_CLOSE_BUTTON),0,"textures/ui/cancel");
        $player->sendForm($form);
    }

    /**
     * @param Player $player
     * @return void
     */
    protected function locations(Player $player): void{
        $locations = SH::getEggs();
        $form = new SimpleForm(function(Player $player, $data): void{
            if(is_null($data)){
                $this->open($player);
                PluginUtils::PlaySound($player, "random.pop2", 1, 5.3);
                return;
            }
            $this->open($player);
            PluginUtils::PlaySound($player, "random.pop", 1, 5.3);
        });
        $form->setTitle(TF::colorize("&l&9EASTEREGGS"));
        $content = "";
        foreach($locations as $location){
            [$x, $y, $z, $world] = explode(":", $location);
            $content .= Language::getPlayerMessage($player, LangKey::SETUP_FORM_CONTENT_LOCATIONS, [
                "{X}" => $x,
                "{Y}" => $y,
                "{Z}" => $z,
                "{WORLD}" => $world
            ]) . "\n";
        }
        $form->setContent($content);
        $form->addButton(TF::colorize("&l&2OK"),0,"textures/ui/check");
        $player->sendForm($form);
    }

    /**
     * @param Player $player
     * @return void
     */
    public function entities(Player $player): void{
        $form = new SimpleForm(function(Player $player, $data): void{
            if(is_null($data)){
                $this->open($player);
                PluginUtils::PlaySound($player, "random.pop2", 1, 5.3);
                return;
            }
            $manager = EE::getInstance()->getEasterEggManager();
            $entityManager = EE::getInstance()->getEntityManager();
            switch($data){
                case 0: // SPAWN
                    if(!$manager->fullEggs()){
                        Language::isError($player, LangKey::ERROR_EGGS_ARE_MISSING);
                        return;
                    }
                    $entityManager->despawnToAll(); // OLD ENTITIES
                    $entityManager->spawnToAll(); // NEW ENTITIES
                    Language::isSuccess($player, LangKey::SUCCESS_SETUP_SPAWN_ENTITY, [
                        "{EGGS}" => count(SH::getEggs())
                    ], false);
                    PluginUtils::PlaySound($player, "random.levelup", 1, 2.1);
                    PluginUtils::PlaySound($player, "random.pop", 1, 5.3);
                break;

                case 1: // DESPAWN
                    $entityManager->despawnToAll();
                    Language::isSuccess($player, LangKey::SUCCESS_SETUP_DESPAWN_ENTITY, [
                        "{EGGS}" => count(SH::getEggs())
                    ]);
                    PluginUtils::PlaySound($player, "random.pop", 1, 5.3);
                break;

                case 3: // CLOSE
                    $this->open($player);
                    PluginUtils::PlaySound($player, "random.pop2", 1, 5.3);
                break;
            }
        });
        $form->setTitle(TF::colorize("&l&9EASTEREGGS"));
        $form->setContent(Language::getPlayerMessage($player, LangKey::SETUP_FORM_CONTENT_ENTITIES));
        $form->addButton(Language::getPlayerMessage($player, LangKey::SETUP_FORM_ENTITIES_BUTTON1),0,"textures/ui/MashupIcon");
        $form->addButton(Language::getPlayerMessage($player, LangKey::SETUP_FORM_ENTITIES_BUTTON2),0,"textures/ui/mashup_hangar");
        $form->addButton(Language::getPlayerMessage($player, LangKey::SETUP_FORM_CLOSE_BUTTON),0,"textures/ui/cancel");
        $player->sendForm($form);
    }

    /**
     * @param Player $player
     * @param callable $callable
     * @return void
     */
    protected function confirm(Player $player, callable $callable): void{
        $modal = new ModalForm(function(Player $player, $data) use($callable): void{
            $callable((bool)$data);
        });
        $modal->setTitle(TF::colorize("&l&9EASTEREGGS"));
        $modal->setContent(Language::getPlayerMessage($player, LangKey::SETUP_FORM_CONFIRM_CONTENT));
        $modal->setButton1(Language::getPlayerMessage($player, LangKey::SETUP_FORM_CONFIRM_BUTTON1)); // YES
        $modal->setButton2(Language::getPlayerMessage($player, LangKey::SETUP_FORM_CONFIRM_BUTTON2)); // NO
        $player->sendForm($modal);
    }
}
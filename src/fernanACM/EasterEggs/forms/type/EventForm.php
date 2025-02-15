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

use pocketmine\Server;
use pocketmine\player\Player;

use pocketmine\utils\TextFormat as TF;
use pocketmine\utils\SingletonTrait;

use Vecnavium\FormsUI\SimpleForm;
use Vecnavium\FormsUI\CustomForm;
use Vecnavium\FormsUI\ModalForm;

use fernanACM\EasterEggs\EasterEggs as EE;

use fernanACM\EasterEggs\language\LangKey;
use fernanACM\EasterEggs\language\Language;

use fernanACM\EasterEggs\utils\helper\EventHelper;
use fernanACM\EasterEggs\utils\PluginUtils;

final class EventForm{
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
        $form = new SimpleForm(function(Player $player, $data): void{
            if(is_null($data)){
                PluginUtils::PlaySound($player, "random.pop2", 1, 5.3);
                return;
            }

            switch($data){
                case 0: // CREATE
                    $this->create($player);
                    PluginUtils::PlaySound($player, "random.pop", 1, 5.3);
                break;

                case 1: // REMOVE
                    $this->remove($player);
                    PluginUtils::PlaySound($player, "random.pop", 1, 5.3);
                break;

                case 2: // APPLY
                    $this->apply($player);
                    PluginUtils::PlaySound($player, "random.pop", 1, 5.3);
                break;

                case 3: // LIST
                    $this->list($player);
                    PluginUtils::PlaySound($player, "random.pop", 1, 5.3);
                break;

                case 4: // CLOSE
                    PluginUtils::PlaySound($player, "random.pop2", 1, 5.3);
                break;
            }
        });
        $form->setTitle(TF::colorize("&l&9EASTEREGGS"));
        $form->setContent(Language::getPlayerMessage($player, LangKey::EVENT_FORM_MAIN_CONTENT));
        $form->addButton(Language::getPlayerMessage($player, LangKey::EVENT_FORM_MAIN_CREATOR_BUTTON),0,"textures/ui/mashup_PaintBrush");
        $form->addButton(Language::getPlayerMessage($player, LangKey::EVENT_FORM_MAIN_REMOVER_BUTTON),0,"textures/ui/icon_trash");
        $form->addButton(Language::getPlayerMessage($player, LangKey::EVENT_FORM_MAIN_APPLY_BUTTON),0,"textures/ui/icon_sign");
        $form->addButton(Language::getPlayerMessage($player, LangKey::EVENT_FORM_MAIN_LIST_BUTTON),0,"textures/ui/icon_book_writable");
        $form->addButton(Language::getPlayerMessage($player, LangKey::EVENT_FORM_MAIN_CLOSE_BUTTON),0,"textures/ui/cancel");
        $player->sendForm($form);
    }

    /**
     * @param Player $player
     * @return void
     */
    protected function create(Player $player): void{
        $custom = new CustomForm(function(Player $player, $data): void{
            if(is_null($data)){
                return;
            }

            if(empty($data[0])){
                Language::isError($player, LangKey::ERROR_LACK_OF_DATA);
                return;
            }
            $limit = intval(EE::getInstance()->config->getNested("Settings.Form.characteres")); // CONFIGURATION
            if(strlen($data[0]) > $limit){
                Language::isError($player, LangKey::ERROR_LACK_OF_DATA);
                return;
            }
            if(EventHelper::exists(strval($data[0]))){
                Language::isError($player, LangKey::ERROR_EXISTING_EVENT);
                return;
            }
            // SUCCESS
            EventHelper::create(strval($data[0]));
            Language::isSuccess($player, LangKey::SUCCESS_EVENT_CREATED, ["{EVENT_NAME}" => strval($data[0])]);
        });
        $custom->setTitle(TF::colorize("&l&9EASTEREGGS"));
        $custom->addInput(Language::getPlayerMessage($player, LangKey::EVENT_FORM_CREATOR_CONTENT), Language::getPlayerMessage($player, LangKey::EVENT_FORM_CREATOR_INPUT));
        $player->sendForm($custom);
    }

    /**
     * @param Player $player
     * @return void
     */
    protected function remove(Player $player): void{
        $events = EventHelper::all();
        if(empty($events)){
            Language::isError($player, LangKey::ERROR_NO_EXISTING_EVENTS);
            return;
        }

        $list = array_values($events);
        $form = new SimpleForm(function(Player $player, $data) use($list): void{
            if(is_null($data)){
                $this->open($player);
                return;
            }
            $eventName = strval($list[$data]);
            if(!isset($list[$data]) || !EventHelper::exists($eventName)){
                Language::isError($player, LangKey::ERROR_EVENT_NOT_FOUND);
                return;
            }
            EventHelper::delete($eventName);
            Language::isSuccess($player, LangKey::SUCCESS_EVENT_REMOVED, ["{EVENT_NAME}" => $eventName]);
        });
        $form->setTitle(TF::colorize("&l&9EASTEREGGS"));
        $form->setContent(Language::getPlayerMessage($player, LangKey::EVENT_FORM_REMOVER_CONTENT));
        foreach($events as $event){
            $form->addButton(TF::colorize($event."\n&r&l&c[------------]"),0,"textures/ui/icon_map");
        }
        $player->sendForm($form);
    }

    /**
     * @param Player $player
     * @return void
     */
    protected function list(Player $player): void{
        $events = EventHelper::all();
        if(empty($events)){
            Language::isError($player, LangKey::ERROR_NO_EXISTING_EVENTS);
            return;
        }

        $form = new SimpleForm(function(Player $player, $data): void{
            if(is_null($data)){
                $this->open($player);
                return;
            }
            $this->open($player);
        });
        $form->setTitle(TF::colorize("&l&9EASTEREGGS"));
        $content = "";
        foreach($events as $event){
            $content .= TF::colorize("&e- &b$event&r")."\n";
        }
        $form->setContent($content);
        $form->addButton(TF::colorize("&l&2OK"),0,"textures/ui/check");
        $player->sendForm($form);
    }

    /**
     * @param Player $player
     * @return void
     */
    protected function apply(Player $player): void{
        $events = EventHelper::all();
        if(empty($events)){
            Language::isError($player, LangKey::ERROR_NO_EXISTING_EVENTS);
            return;
        }

        $list = array_values($events);
        $form = new SimpleForm(function(Player $player, $data) use($list): void{
            if(is_null($data) || !isset($list[$data])){
                $this->open($player);
                return;
            }
            
            $this->confirm($player, function(bool $result) use($player, $data, $list): void{
                if(!$result) return;

                $eventName = strval($list[$data]);
                if(!isset($list[$data]) || !EventHelper::exists($eventName)){
                    Language::isError($player, LangKey::ERROR_EVENT_NOT_FOUND);
                    return;
                }
                $provider = EE::getInstance()->getProviderManager();
                // NEW EVENT NAME
                EventHelper::set($eventName);
                $provider->reset();
                // NEW PROFILE
                foreach(Server::getInstance()->getOnlinePlayers() as $target){
                    if(!$provider->exists($target)){
                        $provider->createAccount($target);
                    }
                }
                Language::isSuccess($player, LangKey::SUCCESS_EVENT_ESTABLISHED, ["{EVENT_NAME}" => $eventName]);
            });
        });
        $form->setTitle(TF::colorize("&l&9EASTEREGGS"));
        $form->setContent(Language::getPlayerMessage($player, LangKey::EVENT_FORM_APPLY_CONTENT));
        foreach($events as $event){
            $form->addButton(TF::colorize($event."\n&r&l&2[------------]"),0,"textures/ui/icon_map");
        }
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
        $modal->setContent(Language::getPlayerMessage($player, LangKey::EVENT_FORM_CONFIRM_CONTENT));
        $modal->setButton1(Language::getPlayerMessage($player, LangKey::EVENT_FORM_CONFIRM_BUTTON1)); // YES
        $modal->setButton2(Language::getPlayerMessage($player, LangKey::EVENT_FORM_CONFIRM_BUTTON2)); // NO
        $player->sendForm($modal);
    }
}
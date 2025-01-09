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

use fernanACM\EasterEggs\language\LangKey;
use fernanACM\EasterEggs\language\Language;

use fernanACM\EasterEggs\helper\SetupHelper;

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
        $inSetupMode = SetupHelper::inSetupMode($player);
        $form = new SimpleForm(function(Player $player, $data): void{
            if(is_null($data)){
                return;
            }
            switch($data){
                case 0: // ENTER / LEAVE SETUP MODE
                    SetupHelper::toggleSetupMode($player);
                break;
                
                case 1; // REMOVE / WAND
                    $wand = SetupHelper::removeWand();
                    if(!$player->getInventory()->canAddItem($wand)){
                        Language::isError($player, LangKey::ERROR_FULL_INVENTORY);
                        return;
                    }
                    $player->getInventory()->addItem($wand);
                break;

                case 2: // LOCATIONS
                    $this->locations($player);
                break;

                case 4:
                break;
            }
        });
        $form->setTitle(TF::colorize("&l&9EASTEREGGS"));
    }

    /**
     * @param Player $player
     * @return void
     */
    protected function locations(Player $player): void{
        $locations = SetupHelper::getEggs();
        $form = new SimpleForm(function(Player $player, $data): void{
            if(is_null($data)){
                $this->open($player);
                return;
            }
            $this->open($player);
        });
        $form->setTitle(TF::colorize("&l&9EASTEREGGS"));
        $content = "";
        foreach($locations as $location){
            [$x, $y, $z, $world] = explode(":", $location);
            $content .= Language::getMessage(LangKey::SETUP_FORM_CONTENT_LOCATIONS, [
                "{X}" => $x,
                "{Y}" => $y,
                "{Z}" => $z,
                "{WORLD}" => $world
            ]) . "\n";
        }
        $form->setContent($content);
        $form->addButton(Language::getMessage(LangKey::SETUP_FORM_BUTTON_LOCATIONS));
        $player->sendForm($form);
    } 
}
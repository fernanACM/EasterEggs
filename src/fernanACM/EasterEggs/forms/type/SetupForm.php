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

use pocketmine\utils\SingletonTrait;

use Vecnavium\FormsUI\SimpleForm;
use Vecnavium\FormsUI\CustomForm;

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
                break;

                case 4:
                break;
            }
        });
    }

    /**
     * @param Player $player
     * @return void
     */
    protected function locations(Player $player): void{

    } 
}
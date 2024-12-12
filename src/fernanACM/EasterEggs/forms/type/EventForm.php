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
use Vecnavium\FormsUI\ModalForm;

use fernanACM\EasterEggs\helper\EventHelper;

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
                return;
            }

            switch($data){
                case 0: // CREATE
                    $this->create($player);
                break;

                case 1: // REMOVE
                break;

                case 2: // LIST
                break;

                case 3: // CLOSE
                break;
            }
        });
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
                // ERROR
                return;
            }
            $limit = 23; // CONFIGURATION
            if(strlen($data[0]) < $limit){
                // ERROR
                return;
            }
            // SUCCESS
            EventHelper::create(strval($data[0]));
        });
    }

    /**
     * @param Player $player
     * @return void
     */
    protected function remove(Player $player): void{
        // ALL BUTTONS...
    }

    /**
     * @param Player $player
     * @return void
     */
    protected function list(Player $player): void{
        $form = new SimpleForm(function(Player $player, $data): void{
            if(is_null($data)){
                return;
            }
            // NOTHING...
        });
        // CONTENT
    }
}
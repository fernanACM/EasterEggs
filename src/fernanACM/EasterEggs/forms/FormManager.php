<?php

#      _       ____   __  __ 
#     / \     / ___| |  \/  |
#    / _ \   | |     | |\/| |
#   / ___ \  | |___  | |  | |
#  /_/   \_\  \____| |_|  |_|
# The creator of this plugin was fernanACM.
# https://github.com/fernanACM

declare(strict_types=1);

namespace fernanACM\EasterEggs\forms;

use pocketmine\utils\SingletonTrait;

use fernanACM\EasterEggs\forms\type\EventForm;
use fernanACM\EasterEggs\forms\type\SetupForm;

final class FormManager{
    use SingletonTrait{
        setInstance as protected;
        reset as protected;
    }

    public function __construct(){
        self::setInstance($this);
    }

    /**
     * @return EventForm
     */
    public function event(): EventForm{
        return EventForm::getInstance();
    }

    /**
     * @return SetupForm
     */
    public function setup(): SetupForm{
        return SetupForm::getInstance();
    }
}
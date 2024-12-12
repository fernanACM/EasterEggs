<?php

#      _       ____   __  __ 
#     / \     / ___| |  \/  |
#    / _ \   | |     | |\/| |
#   / ___ \  | |___  | |  | |
#  /_/   \_\  \____| |_|  |_|
# The creator of this plugin was fernanACM.
# https://github.com/fernanACM

declare(strict_types=1);

namespace fernanACM\EasterEggs\language;

use pocketmine\utils\Config;
use pocketmine\utils\SingletonTrait;

use fernanACM\EasterEggs\EasterEggs as Loader;

final class LanguageManager{
    use SingletonTrait{
        setInstance as protected;
        reset as protected;
    }
    
    protected const DATAFOLDER_NAME = "languages";
    # MultiLanguages
    protected const LANGUAGES = [
        "eng", // English
        "spa", // Spanish
        "ger", // German
        "indo", // Indonesian
        "vie" // Vietnamese
    ];

    /** @var Config $messages */
    protected Config $messages;

    /**
     * @return void
     */
    public function init(): void{
        @mkdir(Loader::getInstance()->getDataFolder(). self::DATAFOLDER_NAME);
        foreach($this->getLanguages() as $language){
            Loader::getInstance()->saveResource(self::DATAFOLDER_NAME."/$language.yml");
        }
        $this->loadMessages();
    }

    /**
     * @return void
     */
    protected function loadMessages(): void{
        $this->messages = new Config(Loader::getInstance()->getDataFolder().self::DATAFOLDER_NAME."/".self::getLanguage().".yml");
    }

    /**
     * @return string
     */
    public function getLanguage(): string{
        return strval(Loader::getInstance()->config->get("language", "eng"));
    }

    /**
     * @return string[]
     */
    public function getLanguages(): array{
        return self::LANGUAGES;
    }

    /**
     * @return Config
     */
    public function getConfig(): Config{
        return $this->messages;
    }
}
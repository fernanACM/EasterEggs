<?php

declare(strict_types=1);

namespace fernanACM\EasterEggs\manager;

use pocketmine\player\Player;

use pocketmine\utils\Config;
use pocketmine\utils\TextFormat as TF;
use pocketmine\utils\SingletonTrait;

use pocketmine\item\Item;

use pocketmine\inventory\Inventory;

use muqsit\invmenu\InvMenu;

use fernanACM\EasterEggs\EasterEggs as EE;

use fernanACM\EasterEggs\utils\ItemUtils;

use fernanACM\EasterEggs\language\LangKey;
use fernanACM\EasterEggs\language\Language;

final class LootManager{
    use SingletonTrait{
        setInstance as protected;
        reset as protected;
    }

    /** @var array $menu */
    protected static array $menu = [];

    /** @var Config $backup */
    protected Config $backup;
    protected const JSON = "backup.json";

    public function __construct(){
        self::setInstance($this);
    }

    /**
     * @return void
     */
    public function init(): void{
        @mkdir(EE::getInstance()->getDataFolder(). "backup");
        $this->backup = new Config(EE::getInstance()->getDataFolder(). "backup/".self::JSON);
    }

    /**
     * @return array
     */
    public function getCommands(): array{
        return (array)EE::getInstance()->config->getNested("Settings.EasterEgg.Reward.Commands.list", []);
    }

    /**
     * @return InvMenu
     */
    public function getInvMenu(): InvMenu{
        $menu = InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST);
        $menu->setName(TF::colorize("&l&2EASTEREGGS LOOT (EDIT)"));
        return $menu;
    }

    /**
     * @return array
     */
    public function getContent(): array{
        $menu = [];
        foreach(self::$menu as $content => $item){
            $menu[$content] = $item;
        }
        return $menu;
    }

    /**
     * @param array $content
     * @return void
     */
    public function setContent(array $content): void{
        self::$menu = $content;
    }

    /**
     * @return integer
     */
    public function getNumContent(): int{
        return count(self::$menu);
    }

    /**
     * @param integer $amount
     * @return Item[]
     */
    public function getRandomItems(int $amount): array{
        $menu = $this->getContent();
        $items = [];
        if(empty($menu)){
            return $items;
        }
        for($i = 0; $i < $amount; $i++){
            $items[] = $menu[array_rand($menu)];
        }
        return $items;
    }

    /**
     * @param Player $player
     * @return void
     */
    public function edit(Player $player): void{
        $menu = $this->getInvMenu();
        $menu->setInventoryCloseListener(function(Player $player, Inventory $inventory): void{
            $content = [];
            foreach($inventory->getContents() as $index => $item){
                $content[$index] = $item;
            }
            // SAVED INVENTORY - BACKUP
            $this->setContent($content);
            $this->saveInventory();
            Language::isSuccess($player, LangKey::SAVED_INVENTORY, ["{ITEM_COUNT}" => $this->getNumContent()]);
        });
        $menu->send($player);
    }

    /**
     * @return void
     */
    public function saveInventory(): void{
        $backup = $this->backup;
        $menu = $this->getContent();
        $place = [];
        foreach($menu as $content => $item){
            $place[$content]["slot"] = $content;
            $place[$content]["item"] = ItemUtils::encodeItem($item);
        }
        $backup->setAll($place);
        $backup->save();
    }

    /**
     * @return void
     */
    public function loadInventory(): void{
        $inv = $this->backup;
        $contents = [];
        foreach($inv->getAll() as $content){
            $item = ItemUtils::decodeItem($content["item"]);
            $contents[$content["slot"]] = $item;
        }
        $this->setContent($contents);
    }
}
<?php

#      _       ____   __  __ 
#     / \     / ___| |  \/  |
#    / _ \   | |     | |\/| |
#   / ___ \  | |___  | |  | |
#  /_/   \_\  \____| |_|  |_|
# The creator of this plugin was fernanACM.
# https://github.com/fernanACM

declare(strict_types=1);

namespace fernanACM\EasterEggs\entities;

use pocketmine\player\Player;

use pocketmine\entity\Human;

use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;

use pocketmine\nbt\tag\CompoundTag;

use pocketmine\entity\Location;
use pocketmine\entity\Skin;

use pocketmine\color\Color;

use pocketmine\world\particle\DustParticle;

use fernanACM\EasterEggs\const\NBTConst;

use fernanACM\EasterEggs\manager\EasterEggManager;

use fernanACM\EasterEggs\EasterEggs as EE;

use fernanACM\EasterEggs\utils\helper\SetupHelper as SH;

class EasterEggEntity extends Human{

    /** @var int|null $eggId */
    protected ?int $eggId = null;

    /**
     * @param Location $location
     * @param Skin $skin
     * @param CompoundTag|null $nbt
     */
    public function __construct(Location $location, Skin $skin, ?CompoundTag $nbt){
        parent::__construct($location, $skin, $nbt);
        if(!is_null($nbt) && !is_null($nbt->getTag(NBTConst::EGG_ID))){
            $this->setEggId($nbt->getInt(NBTConst::EGG_ID));
        }
    }

    /**
     * @param integer $currentTick
     * @return boolean
     */
    public function onUpdate(int $currentTick): bool{
        $this->setScale(0.9);
        $this->setNameTagVisible(false);
        $this->setNoClientPredictions();
        $this->particles($currentTick);
        return parent::onUpdate($currentTick);
    }

    /**
     * @param CompoundTag $nbt
     * @return void
     */
    public function initEntity(CompoundTag $nbt): void{
        parent::initEntity($nbt);
        $tag = $nbt->getTag(NBTConst::EGG_ID);
        if(!is_null($tag)){
            $this->setEggId($nbt->getInt(NBTConst::EGG_ID));
        }
    }

    /**
     * @return CompoundTag
     */
    public function saveNBT(): CompoundTag{
        $nbt = parent::saveNBT();
        if(!is_null($this->eggId)){
            $nbt->setInt(NBTConst::EGG_ID, $this->eggId);
        }
        return $nbt;
    }

    /**
     * @param EntityDamageEvent $source
     * @return void
     */
    public function attack(EntityDamageEvent $source): void{
        $source->cancel();
        if(!$source instanceof EntityDamageByEntityEvent) return;

        $damager = $source->getDamager();
        if(!$damager instanceof Player) return;

        if(SH::inSetupMode($damager)){
            return;
        }

        $manager = EasterEggManager::getInstance();
        if(!is_null($this->eggId)){
            $manager->add($damager, $manager->idNumToEggId($this->getEggId()));
        }
    }

    /**
     * @param integer $currentTick
     * @return void
     */
    protected function particles(int $currentTick): void{
        $world = $this->getWorld();
        $position = $this->getPosition();
        $particlesPerRevolution = 20;
        $heightPerRevolution = 0.4;
        $radius = 1;
        $revolutions = 3;
        $time = ($currentTick % ($particlesPerRevolution * $revolutions)) / $particlesPerRevolution;
        $angle = $time * 2 * M_PI;
        $x = $radius * cos($angle);
        $z = $radius * sin($angle);
        $y = $heightPerRevolution * $time;
        $particlePos = $position->add($x, $y, $z);
        $config = EE::getInstance()->config;
        if(boolval($config->getNested("Settings.EasterEgg.Entity.particles", true))){
            $rgb = (array)$config->getNested("Settings.EasterEgg.Entity.particle-color"); // RGB = [R, G, B]
            if(count($rgb) === 3){
                $world->addParticle($particlePos, new DustParticle(new Color(intval($rgb[0]), intval($rgb[1]), intval($rgb[2]))));
            }
        }
    }    

    /**
     * @param integer $eggId
     * @return void
     */
    public function setEggId(int $eggId): void{
        $this->eggId = $eggId;
    }

    /**
     * @return integer
     */
    public function getEggId(): int{
        return $this->eggId ?? 0;
    }
}
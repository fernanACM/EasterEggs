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

use pocketmine\Server;

use pocketmine\utils\SingletonTrait;

use pocketmine\math\Vector3;

use pocketmine\entity\Human;
use pocketmine\entity\Location;

use pocketmine\entity\EntityDataHelper as Helper;
use pocketmine\entity\EntityFactory;

use pocketmine\world\World;

use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\nbt\tag\DoubleTag;
use pocketmine\nbt\tag\FloatTag;

use fernanACM\EasterEggs\utils\SkinUtils;
use fernanACM\EasterEggs\utils\helper\SetupHelper as SH;

final class EntityManager{
    use SingletonTrait{
        setInstance as protected;
        reset as protected;
    }

    public function __construct(){
        self::setInstance($this);
    }

    /**
     * @return void
     */
    public function init(): void{
        EntityFactory::getInstance()->register(EasterEggEntity::class, function(World $world, CompoundTag $nbt): EasterEggEntity{
            return new EasterEggEntity(Helper::parseLocation($nbt, $world), Human::parseSkinNBT($nbt), $nbt);
        }, ["EasterEggEntity"]);
    }

    /**
     * @param Vector3 $position
     * @param Vector3|null $motion
     * @param float $yaw
     * @param float $pitch
     * @return CompoundTag
     */
    protected function createBaseNBT(Vector3 $position, ?Vector3 $motion = null, float $yaw = 0.0, float $pitch = 0.0): CompoundTag{
        return CompoundTag::create()->setTag("Pos", new ListTag([
                new DoubleTag($position->x),
                new DoubleTag($position->y),
                new DoubleTag($position->z)
            ]))->setTag("Motion", new ListTag([
                new DoubleTag(!is_null($motion) ? $motion->x : 0),
                new DoubleTag(!is_null($motion)? $motion->y : 0),
                new DoubleTag(!is_null($motion) ? $motion->z : 0)
            ]))->setTag("Rotation", new ListTag([
                new FloatTag($yaw),
                new FloatTag($pitch)
            ]));
    }

    /**
     * @param Location $location
     * @param integer $eggId
     * @return void
     */
    public function create(Location $location, int $eggId): void{
        $entity = new EasterEggEntity($location, SkinUtils::generateSkinFromPath(SkinUtils::FILE_NAME), $this->createBaseNBT($location));
        $entity->setEggId($eggId);
        $entity->setNameTagVisible(false);
        $entity->setScale(0.9);
        $entity->spawnToAll();
    }

    /**
     * @return void
     */
    public function spawnToAll(): void{
        $eggLocations = SH::getEggs();
        if(empty($eggLocations)){
            return;
        }
        $eggId = 1;
        $server = Server::getInstance();
        foreach($eggLocations as $location){
            [$x, $y, $z, $worldName] = explode(":", $location);
            $world = $server->getWorldManager()->getWorldByName($worldName);
            if(is_null($world)){
                $server->getLogger()->warning("The World {$worldName} has not been found");
                continue;
            }
            $pos = new Location(intval($x)+0.5, intval($y)+1, intval($z)+0.5, $world, 0.0, 0.0);
            $this->create($pos, $eggId++);
        }
    }

    /**
     * @return void
     */
    public function despawnToAll(): void{
        $server = Server::getInstance();
        $worlds = $server->getWorldManager()->getWorlds();
        foreach($worlds as $world){
            foreach($world->getEntities() as $entity){
                if(!$entity instanceof EasterEggEntity) continue;
                $entity->flagForDespawn();
            }
        }
    }
}
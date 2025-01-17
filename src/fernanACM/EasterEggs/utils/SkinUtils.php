<?php

#      _       ____   __  __ 
#     / \     / ___| |  \/  |
#    / _ \   | |     | |\/| |
#   / ___ \  | |___  | |  | |
#  /_/   \_\  \____| |_|  |_|
# The creator of this plugin was fernanACM.
# https://github.com/fernanACM

declare(strict_types=1);

namespace fernanACM\EasterEggs\utils;

use GdImage;

use pocketmine\entity\Skin;

use fernanACM\EasterEggs\EasterEggs as EE;

final class SkinUtils{

    public const FILE_NAME = "egg";

    /**
     * @return string
     */
    protected static function getPath(): string{
        return "skins";
    }

    /**
     * @return void
     */
    public static function init(): void{
        @mkdir(EE::getInstance()->getDataFolder().self::getPath());
        foreach([".json", ".png"] as $extends){
            EE::getInstance()->saveResource(self::getPath()."/".self::FILE_NAME.$extends);
        }
    }

    /**
     * @param string $fileName
     * @return Skin
     */
    public static function generateSkinFromPath(string $fileName): Skin{
        $locate = self::getPath();
        $path = EE::getInstance()->getDataFolder().$locate. DIRECTORY_SEPARATOR.$fileName;
        $size = getimagesize($path.".png");
        $path_file = self::imgTricky($path.".png", $fileName, $locate, [$size[0], $size[1], 4]);
        $img = @imagecreatefrompng($path_file);
        $skinbytes = "";
        for($y = 0; $y < $size[1]; $y++){
            for($x = 0; $x < $size[0]; $x++){
                $colorat = @imagecolorat($img, $x, $y);
                $a = ((~((int)($colorat >> 24))) << 1) & 0xff;
                $r = ($colorat >> 16) & 0xff;
                $g = ($colorat >> 8) & 0xff;
                $b = $colorat & 0xff;
                $skinbytes .= chr($r) . chr($g) . chr($b) . chr($a);
            }
        }
        @imagedestroy($img);
        $geometryPath = $path . ".json";
        if(!file_exists($geometryPath)) {
            $geometryPath = EE::getInstance()->getDataFolder() . $locate . DIRECTORY_SEPARATOR ."geometry.json";
        }
        return new Skin("Standard_CustomSlim", $skinbytes, "", "geometry.".$fileName, file_get_contents($geometryPath));
    }

    /**
     * @param string $skinPath
     * @param string $skinName
     * @param string $locate
     * @param array $size
     * @return string
     */
    public static function imgTricky(string $skinPath, string $skinName, string $locate, array $size): string{
        $path = EE::getInstance()->getDataFolder();
        $down = imagecreatefrompng($skinPath);
        $upper = null;
        if($size[0] * $size[1] * $size[2] == 65536){
            $upper = self::resize_image($path . $locate . "/" . $skinName . ".png", 128, 128);
        }else{
            $upper = self::resize_image($path . $locate . "/" . $skinName . ".png", 64, 64);
        }
        //Remove black color out of the png
        imagecolortransparent($upper, imagecolorallocatealpha($upper, 0, 0, 0, 127));

        imagealphablending($down, true);
        imagesavealpha($down, true);
        imagecopymerge($down, $upper, 0, 0, 0, 0, $size[0], $size[1], 100);
        imagepng($down, $path. self::getPath().'/temp.png');
        return $path. self::getPath().'/temp.png';
    }

    /**
     * @param string $file
     * @param integer $w
     * @param integer $h
     * @param boolean $crop
     * @return GdImage
     */
    public static function resize_image(string $file, int $w, int $h, $crop = false): GdImage{
        list($width, $height) = getimagesize($file);
        $r = $width / $height;
        if($crop){
            if($width > $height){
                $width = ceil($width - ($width * abs($r - $w / $h)));
            }else{
                $height = ceil($height - ($height * abs($r - $w / $h)));
            }
            $newwidth = $w;
            $newheight = $h;
        }else{
            if($w / $h > $r){
                $newwidth = $h * $r;
                $newheight = $h;
            }else{
                $newheight = $w / $r;
                $newwidth = $w;
            }
        }
        $src = imagecreatefrompng($file);
        $dst = imagecreatetruecolor($w, $h);
        imagecolortransparent($dst, imagecolorallocatealpha($dst, 0, 0, 0, 127));
        imagealphablending($dst, false);
        imagesavealpha($dst, true);
        imagecopyresampled($dst, $src, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
        return $dst;
    }
}
<?php
/**
 * Organizzy
 * Copyright (C) 2014 Organizzy Team
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

class ImageResizer {

    private $inputFile;
    private $img;
    private $width;
    private $height;

    /**
     * @param string $inputFile input file name
     * @throws Exception
     */
    public function __construct($inputFile) {
        $this->inputFile = $inputFile;
        list($this->width, $this->height, $type, $attr) = getimagesize($inputFile);

        switch($type) {
            case IMG_PNG:
                $this->img = imagecreatefrompng($inputFile);
                break;

            case IMG_JPEG:
            case IMG_JPG:
                $this->img = imagecreatefromjpeg($inputFile);
                break;

            case IMG_GIF:
                $this->img = imagecreatefromgif($inputFile);
                break;

            default:
                throw new Exception('Image not supported');
        }

    }

    /**
     *
     */
    function __destruct() {
        imagedestroy($this->img);
    }

    /**
     * save resized file name to $outFile
     *
     * @param string $outFile output file name
     * @param int $w image width
     * @param int $h image height
     * @param int $q image quality
     * @return bool return true if success
     */
    public function saveAs($outFile, $w, $h, $q = 70) {
        $dstImg = imagecreatetruecolor($w, $h);

        $scale = max($w / $this->width, $h / $this->height);
        $srcW = $w / $scale;
        $srcH = $h / $scale;
        $srcX = ($this->width - $srcW) / 2;
        $srcY = ($this->height - $srcH) / 2;

        imagecopyresampled($dstImg, $this->img, 0, 0, $srcX, $srcY, $w, $h, $srcW, $srcH);
        $ret = imagejpeg($dstImg, $outFile, $q);
        imagedestroy($dstImg);
        return $ret;
    }
} 
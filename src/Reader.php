<?php
/**
 * Created by PhpStorm.
 * User: daan
 * Date: 9/29/18
 * Time: 1:43 PM
 */

namespace StudioSeptember\RDNAPTrans;


use \RuntimeException;

class Reader
{
    public static $gridFiles = [];

    public static function read($grdFile)
    {
        $buffer = @static::$gridFiles[$grdFile];
        if (!$buffer){
            throw new RuntimeException("${grdFile} is not a valid grd file.");
        }
        return $buffer;
    }

    public static function readShort($handle, $offset) {
        fseek($handle, $offset);
        return @unpack('v', fread($handle, 2))[0];
    }

    public static function readDouble($handle, $offset) {
        fseek($handle, $offset);
        return @unpack('d', fread($handle, 8))[0];
    }

}

Reader::$gridFiles = [
    'x2c.grd' => fopen(__DIR__ . '/../var/x2c.grd', 'r'),
    'y2c.grd' => fopen(__DIR__ . '/../var/y2c.grd', 'r'),
    'nlgeo04.grd' => fopen(__DIR__ . '/../var/nlgeo04.grd', 'r'),
];
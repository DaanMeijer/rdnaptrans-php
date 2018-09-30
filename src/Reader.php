<?php
/**
 * Created by PhpStorm.
 * User: daan
 * Date: 9/29/18
 * Time: 1:43 PM
 */

namespace StudioSeptember\RDNAPTrans;


use Google\FlatBuffers\ByteBuffer;
use \RuntimeException;

class Reader
{
    public static $gridFiles = [];

    /**
     * @param string $grdFile
     * @return ByteBuffer
     */
    public static function read($grdFile)
    {
        $buffer = @static::$gridFiles[$grdFile];
        if (!$buffer){
            throw new RuntimeException("${grdFile} is not a valid grd file.");
        }
        return $buffer;
    }

}

Reader::$gridFiles = [
    'x2c.grd' => ByteBuffer::wrap(file_get_contents(__DIR__ . '/../var/x2c.grd', 'r')),
    'y2c.grd' => ByteBuffer::wrap(file_get_contents(__DIR__ . '/../var/y2c.grd', 'r')),
    'nlgeo04.grd' => ByteBuffer::wrap(file_get_contents(__DIR__ . '/../var/nlgeo04.grd', 'r')),
];
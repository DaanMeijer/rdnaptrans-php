<?php
/**
 * Created by PhpStorm.
 * User: daan
 * Date: 9/29/18
 * Time: 2:21 PM
 */

namespace StudioSeptember\RDNAPTrans;


/**
 * @property double X
 * @property double Y
 * @property double Z
 */
class Cartesian
{

    /**
     * <p>Constructor for Cartesian.</p>
     *
     * @param double X
     * @param double Y
     * @param double Z
     */
    public function __construct($X, $Y, $Z = null)
    {
        if ($Z === null) {
            $Z = 0;
        }
        $this->X = $X;
        $this->Y = $Y;
        $this->Z = $Z;
    }

    /**
     * <p>withZ.</p>
     *
     * @param double z
     * @return Cartesian object
     */
    public function withZ($z)
    {
        return new Cartesian($this->X, $this->Y, $z);
    }
}
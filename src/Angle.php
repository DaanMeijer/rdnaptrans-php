<?php
/**
 * Created by PhpStorm.
 * User: daan
 * Date: 9/29/18
 * Time: 2:23 PM
 */

namespace StudioSeptember\RDNAPTrans;


/**
 * @property double Degrees
 * @property double Minutes
 * @property double Seconds
 */
class Angle
{

    /**
     * <p>Constructor for Angle.</p>
     *
     * @param double Degrees
     * @param double Minutes
     * @param double Seconds
     */
    public function __construct($Degrees, $Minutes, $Seconds) {
        $this->Degrees = $Degrees;
        $this->Minutes = $Minutes;
        $this->Seconds = $Seconds;
    }
}
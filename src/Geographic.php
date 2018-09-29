<?php
/**
 * Created by PhpStorm.
 * User: daan
 * Date: 9/29/18
 * Time: 1:39 PM
 */

namespace StudioSeptember\RDNAPTrans;


/**
 * @property  double phi
 * @property  double lambda
 * @property int|null h
 */
class Geographic
{

    /**
     **    phi      latitude in degrees
     **    lambda   longitude in degrees
     **    h        ellipsoidal height
     */

    /**
     * <p>Constructor for Geographic.</p>
     *
     * @param phi a double.
     * @param lambda a double.
     * @param h a double.
     */
    public function __construct($phi, $lambda, $h = null) {
        if ($h === null){
            $h = 0;
        }
        $this->phi = $phi;
        $this->lambda = $lambda;
        $this->h = $h;
    }

    /**
     * @param double h
     * @return Geographic
     */
    public function withH($h) {
        return new Geographic($this->phi, $this->lambda, $h);
    }

}
<?php
/**
 * Created by PhpStorm.
 * User: daan
 * Date: 9/29/18
 * Time: 1:35 PM
 */

namespace StudioSeptember\RDNAPTrans;


class Transform
{

    /** JAVASCRIPT PORT
     **--------------------------------------------------------------
     **    RDNAPTRANS(TM)2008
     **
     **    Authors: Jochem Lesparre, Joop van Buren, Marc Crombaghs, Frank Dentz,
     **    Arnoud Pol, Sander Oude Elberink
     **             http://www.rdnap.nl
     **    Based on RDNAPTRANS2004
     **    Main changes:
     **    - 7 similarity transformation parameters
     **    - 0.0088 offset in the transformation between ellipsoidal height (h)
     **    and orthometric heights (NAP)
     **    - coordinates are computed also outside the validity regions of the
     **    grid files x2c.grd, y2c.grd and nlgeo04.grd
     **--------------------------------------------------------------
     */

    /**
     **--------------------------------------------------------------
     **    Function name: etrs2rd
     **    Description:   convert ETRS89 coordinates to RD coordinates
     **
     **    Parameter      Type        In/Out Req/Opt Default
     **    phi_etrs       double      in     req     none
     **    lambda_etrs    double      in     req     none
     **    h_etrs         double      in     req     none
     **    x_rd           double      out    -       none
     **    y_rd           double      out    -       none
     **
     **    Additional explanation of the meaning of parameters
     **    phi_etrs, lambda_etrs, h_etrs  input ETRS89 coordinates
     **    x_rd, y_rd                     output RD coordinates
     **
     **    Return value: (besides the standard return values)
     **    none
     **--------------------------------------------------------------
     */
    /**
     * <p>etrs2rd.</p>
     *
     * @param Geographic etrs
     * @return Cartesian
     */
    public static function etrs2rd($etrs) {
    /**
     **--------------------------------------------------------------
     **    Calculate the cartesian ETRS89 coordinates of the pivot point Amersfoort
     **--------------------------------------------------------------
     */
        $amersfoortBessel = Helpers::geographic2cartesian(
        new Geographic(
            Constants::PHI_AMERSFOORT_BESSEL,
            Constants::LAMBDA_AMERSFOORT_BESSEL,
            Constants::H_AMERSFOORT_BESSEL
        ),
        Constants::A_BESSEL,
        Constants::INV_F_BESSEL
    );
    $xAmersfoortETRS = $amersfoortBessel->X + Constants::TX_BESSEL_ETRS;
    $yAmersfoortETRS = $amersfoortBessel->Y + Constants::TY_BESSEL_ETRS;
    $zAmersfoortETRS = $amersfoortBessel->Z + Constants::TZ_BESSEL_ETRS;

    /**
     **--------------------------------------------------------------
     **    Convert ETRS89 coordinates to RD coordinates
     **    (To convert from degrees, minutes and seconds use the function
     **    deg_min_sec2decimal() here)
     **--------------------------------------------------------------
     */
    $cartesianETRS = Helpers::geographic2cartesian(
        $etrs,
        Constants::A_ETRS,
        Constants::INV_F_ETRS
    );

    $cartesianBessel = Helpers::simTrans(
        $cartesianETRS,
        new Cartesian(Constants::TX_ETRS_BESSEL, Constants::TY_ETRS_BESSEL, Constants::TZ_ETRS_BESSEL),
        Constants::ALPHA_ETRS_BESSEL,
        Constants::BETA_ETRS_BESSEL,
        Constants::GAMMA_ETRS_BESSEL,
        Constants::DELTA_ETRS_BESSEL,
        new Cartesian($xAmersfoortETRS, $yAmersfoortETRS, $zAmersfoortETRS)
    );

    $geographicBessel = Helpers::cartesian2geographic(
        $cartesianBessel,
        Constants::A_BESSEL,
        Constants::INV_F_BESSEL
    );

    $pseudoRD = Helpers::rdProjection($geographicBessel);
    $corrected = Helpers::rdCorrection($pseudoRD);
    return $corrected->withZ($geographicBessel->h);
}

/**
 **--------------------------------------------------------------
 **    Function name: rd2etrs
 **    Description:   convert RD coordinates to ETRS89 coordinates
 **
 **    Parameter      Type        In/Out Req/Opt Default
 **    x_rd           double      in     req     none
 **    y_rd           double      in     req     none
 **    nap            double      in     req     none
 **    phi_etrs       double      out    -       none
 **    lambda_etrs    double      out    -       none
 **
 **    Additional explanation of the meaning of parameters
 **    x_rd, y_rd, nap        input RD and NAP coordinates
 **    phi_etrs, lambda_etrs  output ETRS89 coordinates
 **
 **    Return value: (besides the standard return values)
 **    none
 **--------------------------------------------------------------
 */
/**
 * <p>rd2etrs.</p>
 *
 * @param Cartesian
 * @return Geographic
 */
public static function rd2etrs($rd) {
    /**
     **--------------------------------------------------------------
     **    Calculate the cartesian Bessel coordinates of the pivot point Amersfoort
     **--------------------------------------------------------------
     */
    $amersfoortBessel = Helpers::geographic2cartesian(
            new Geographic(
                Constants::PHI_AMERSFOORT_BESSEL,
                Constants::LAMBDA_AMERSFOORT_BESSEL,
                Constants::H_AMERSFOORT_BESSEL
            ),
            Constants::A_BESSEL,
            Constants::INV_F_BESSEL
        );

  /**
   **--------------------------------------------------------------
   **    Calculate appoximated value of ellipsoidal Bessel height
   **    The error made by using a constant for de Bessel geoid height is max.
   **    circa 1 meter in the ellipsoidal height (for the NLGEO2004 geoid model).
   **    This intoduces an error in the phi, lambda position too, this error is
   **    nevertheless certainly smaller than 0.0001 m.
   **--------------------------------------------------------------
   */
    $hBessel = $rd->Z + Constants::MEAN_GEOID_HEIGHT_BESSEL;

  /**
   **--------------------------------------------------------------
   **    Convert RD coordinates to ETRS89 coordinates
   **--------------------------------------------------------------
   */
    $pseudoRD = Helpers::invRdCorrection($rd);
    $etrsBessel = Helpers::invRdProjection($pseudoRD);
    $cartesianBessel = Helpers::geographic2cartesian(
        $etrsBessel->withH($hBessel),
            Constants::A_BESSEL,
            Constants::INV_F_BESSEL
        );

    $cartesianETRS = Helpers::simTrans(
        $cartesianBessel,
            new Cartesian(Constants::TX_BESSEL_ETRS, Constants::TY_BESSEL_ETRS, Constants::TZ_BESSEL_ETRS),
            Constants::ALPHA_BESSEL_ETRS, Constants::BETA_BESSEL_ETRS, Constants::GAMMA_BESSEL_ETRS,
            Constants::DELTA_BESSEL_ETRS,
        $amersfoortBessel
        );

    return Helpers::cartesian2geographic($cartesianETRS,
            Constants::A_ETRS, Constants::INV_F_ETRS);

    /**
     **--------------------------------------------------------------
     **    To convert to degrees, minutes and seconds use the function decimal2deg_min_sec() here
     **--------------------------------------------------------------
     */
  }

  /**
   **--------------------------------------------------------------
   **    Function name: etrs2nap
   **    Description:   convert ellipsoidal ETRS89 height to NAP height
   **
   **    Parameter      Type        In/Out Req/Opt Default
   **    phi            double      in     req     none
   **    lambda         double      in     req     none
   **    h              double      in     req     none
   **    nap            double      out    -       none
   **
   **    Additional explanation of the meaning of parameters
   **    phi, lambda, h  input ETRS89 coordinates
   **    nap             output NAP height
   **
   **    Return value: (besides the standard return values) none
   **    on error (outside geoid grid) nap is not compted here
   **    instead in etrs2rdnap nap=h_bessel
   **--------------------------------------------------------------
   */
  /**
   * <p>etrs2nap.</p>
   *
   * @param Geographic etrs
   * @return double
   */
    public static function etrs2nap($etrs) {
    /**
     **--------------------------------------------------------------
     **    Explanation of the meaning of variables:
     **        n  geoid height
     **    on error (outside geoid grid) nap is not compted
     **    instead in etrs2rdnap nap=h_bessel
     **--------------------------------------------------------------
     */
    $n = self::$grdFileZ->gridInterpolation($etrs->lambda, $etrs->phi);
    return $n ? $etrs->h - $n + 0.0088 : null;
  }

/**
 **--------------------------------------------------------------
 **    Function name: nap2etrs
 **    Description:   convert NAP height to ellipsoidal ETRS89 height
 **
 **    Parameter      Type        In/Out Req/Opt Default
 **    phi            double      in     req     none
 **    lambda         double      in     req     none
 **    nap            double      in     req     none
 **    h              double      out    -       none
 **
 **    Additional explanation of the meaning of parameters
 **    phi, lambda  input ETRS89 position
 **    nap          input NAP height at position phi, lambda
 **    h            output ellipsoidal ETRS89 height
 **
 **    Return value: (besides the standard return values)
 **    none
 **    on error (outside geoid grid) h is not compted here
 **    instead in rdnap2etrs h=h_etrs_sim (from similarity transformation)
 **--------------------------------------------------------------
 */
/**
 * <p>nap2etrs.</p>
 *
 * @param double phi
 * @param double lambda
 * @param double nap
 * @return double|null
 */
    public static function nap2etrs($phi, $lambda, $nap) {
    /**
     **--------------------------------------------------------------
     **    Explanation of the meaning of variables:
     **        n  geoid height
     **--------------------------------------------------------------
     */
    $n = self::$grdFileZ->gridInterpolation($lambda, $phi);
    return $n ? $nap + $n - 0.0088 : null;
  }

    /**
     **--------------------------------------------------------------
     **    Function name: etrs2rdnap
     **    Description:   convert ETRS89 coordinates to RD and NAP coordinates
     **
     **    Parameter      Type        In/Out Req/Opt Default
     **    phi            double      in     req     none
     **    lambda         double      in     req     none
     **    h              double      in     req     none
     **    x_rd           double      out    -       none
     **    y_rd           double      out    -       none
     **    nap            double      out    -       none
     **
     **    Additional explanation of the meaning of parameters
     **    phi, lambda, h   input ETRS89 coordinates
     **    x_rd, y_rd, nap  output RD and NAP coordinates
     **
     **    Return value: (besides the standard return values)
     **    none
     **--------------------------------------------------------------
     */
    /**
     * <p>etrs2rdnap.</p>
     *
     * @param Geographic etrs
     * @return Cartesian
     */
    public static function etrs2rdnap($etrs)
    {
        $rd = Transform::etrs2rd($etrs);
        $betterH = Transform::etrs2nap($etrs);
        return $betterH ? $rd->withZ($betterH) : $rd;
    }

    /**
     **--------------------------------------------------------------
     **    Function name: rdnap2etrs
     **    Description:   convert RD and NAP coordinates to ETRS89 coordinates
     **
     **    Parameter      Type        In/Out Req/Opt Default
     **    x_rd           double      in     req     none
     **    y_rd           double      in     req     none
     **    nap            double      in     req     none
     **    phi            double      out    -       none
     **    lambda         double      out    -       none
     **    h              double      out    -       none
     **
     **    Additional explanation of the meaning of parameters
     **    x_rd, y_rd, nap  input RD and NAP coordinates
     **    phi, lambda, h   output ETRS89 coordinates
     **
     **    Return value: (besides the standard return values)
     **    none
     **--------------------------------------------------------------
     */
    /**
     * <p>rdnap2etrs.</p>
     *
     * @param Cartesian rdnap
     * @return Geographic
     */
    public static function rdnap2etrs($rdnap)
    {
        $etrs = Transform::rd2etrs($rdnap);
        $betterH = Transform::nap2etrs($etrs->phi, $etrs->lambda, $rdnap->Z);
        return $betterH ? $etrs->withH($betterH) : $etrs;
    }

    /**
     **--------------------------------------------------------------
     **    End of RDNAPTRANS(TM)2008
     **--------------------------------------------------------------
     */

    /** @var GrdFile */
    public static $grdFileZ;
}

Transform::$grdFileZ = GrdFile::GRID_FILE_GEOID();
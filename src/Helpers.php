<?php
/**
 * Created by PhpStorm.
 * User: daan
 * Date: 9/29/18
 * Time: 2:34 PM
 */

namespace StudioSeptember\RDNAPTrans;


class Helpers {
    /**
     **--------------------------------------------------------------
     **    Functions
     **--------------------------------------------------------------
     */

    /**
     **--------------------------------------------------------------
     **    Function name: deg_sin
     **    Description:   sine for angles in degrees
     **
     **    Parameter      Type        In/Out Req/Opt Default
     **    $alpha          const      in     req     none
     **
     **    Additional explanation of the meaning of parameters
     **    none
     **
     **    Return value: (besides the standard return values)
     **    sin($alpha)
     **--------------------------------------------------------------
     * @param float $alpha
     * @return float
     */
public static function degSin($alpha) { return sin($alpha / 180.0 * pi()); }

    /**
     **--------------------------------------------------------------
     **    Function name: deg_cos
     **    Description:   cosine for angles in degrees
     **
     **    Parameter      Type        In/Out Req/Opt Default
     **    $alpha          const      in     req     none
     **
     **    Additional explanation of the meaning of parameters
     **    none
     **
     **    Return value: (besides the standard return values)
     **    cos($alpha)
     **--------------------------------------------------------------
     * @param float $alpha
     * @return float
     */
public static function degCos($alpha) { return cos($alpha / 180.0 * pi()); }

    /**
     **--------------------------------------------------------------
     **    Function name: deg_tan
     **    Description:   tangent for angles in degrees
     **
     **    Parameter      Type        In/Out Req/Opt Default
     **    $alpha          const      in     req     none
     **
     **    Additional explanation of the meaning of parameters
     **    none
     **
     **    Return value: (besides the standard return values)
     **    tan($alpha)
     **--------------------------------------------------------------
     * @param float $alpha
     * @return float
     */
  public static function degTan($alpha) { return tan($alpha / 180.0 * pi()); }

    /**
     **--------------------------------------------------------------
     **    Function name: deg_asin
     **    Description:   inverse sine for angles in degrees
     **
     **    Parameter      Type        In/Out Req/Opt Default
     **    a              const      in     req     none
     **
     **    Additional explanation of the meaning of parameters
     **    none
     **
     **    Return value: (besides the standard return values)
     **    asin($a)
     **--------------------------------------------------------------
     * @param float $a
     * @return float|int
     */
  public static function degAsin($a) { return (asin($a) * 180.0 / pi()); }

    /**
     **--------------------------------------------------------------
     **    Function name: deg_atan
     **    Description:   inverse tangent for angles in degrees
     **
     **    Parameter      Type        In/Out Req/Opt Default
     **    a              $in     req     none
     **
     **    Additional explanation of the meaning of parameters
     **    none
     **
     **    Return value: (besides the standard return values)
     **    atan($a)
     **--------------------------------------------------------------
     * @param float $a
     * @return float|int
     */
  public static function degAtan($a) { return (atan($a) * 180.0 / pi()); }

    /**
     **--------------------------------------------------------------
     **    Function name: atanh
     **    Description:   inverse hyperbolic tangent
     **
     **    Parameter      Type        In/Out Req/Opt Default
     **    a              const      in     req     none
     **
     **    Additional explanation of the meaning of parameters
     **    none
     **
     **    Return value: (besides the standard return values)
     **    atanh($a)
     **--------------------------------------------------------------
     * @param float $a
     * @return float
     */
  public static function atanh($a) { return (0.5 * log((1.0 + $a) / (1.0 - $a))); }

    /**
     **--------------------------------------------------------------
     **    Function name: geographic2cartesian
     **    Description:   from geographic coordinates to cartesian coordinates
     **
     **    Parameter      Type        In/Out Req/Opt Default
     **    phi            const      in     req     none
     **    lambda         const      in     req     none
     **    h              const      in     req     none
     **    a              const      in     req     none
     **    inv_f          const      in     req     none
     **    x              const      out    -       none
     **    y              const      out    -       none
     **    z              const      out    -       none
     **
     **    Additional explanation of the meaning of parameters
     **    phi      latitude in degrees
     **    lambda   longitude in degrees
     **    h        ellipsoidal height
     **    a        half major axis of the ellisoid
     **    inv_f    inverse flattening of the ellipsoid
     **    x, y, z  output of cartesian coordinates
     **
     **    Return value: (besides the standard return values)
     **    none
     **--------------------------------------------------------------
     * @param Geographic $geographic
     * @param double $a
     * @param double $inverseF
     * @return Cartesian
     */
  public static function geographic2cartesian($geographic, $a, $inverseF) {
    /**
     **--------------------------------------------------------------
     **    Source: G. Bakker, J.$c-> de Munck and G.L. Strang van Hees,
     **        "Radio Positioning at Sea". Delft University of Technology, 1995.
     **--------------------------------------------------------------
     */

    /**
     **--------------------------------------------------------------
     **    Explanation of the meaning of variables:
     **        f    flattening of the ellipsoid
     **        ee   first eccentricity squared (e squared in some notations)
     **        n    second (East West) principal radius of curvature (N in some notations)
     **--------------------------------------------------------------
     */
    $f = 1.0 / $inverseF;
    $ee = $f * (2.0 - $f);
    $n = $a / sqrt(1.0 - $ee * pow(Helpers::degSin($geographic->phi), 2));

    $x = ($n + $geographic->h) * Helpers::degCos($geographic->phi)
        * Helpers::degCos($geographic->lambda);
    $y = ($n + $geographic->h) * Helpers::degCos($geographic->phi)
        * Helpers::degSin($geographic->lambda);
    $z = ($n * (1.0 - $ee) + $geographic->h) * Helpers::degSin($geographic->phi);

    return new Cartesian($x, $y, $z);
  }

    /**
     **--------------------------------------------------------------
     **    Function name: cartesian2geographic
     **    Description:   from cartesian coordinates to geographic coordinates
     **
     **    Parameter      Type        In/Out Req/Opt Default
     **    x              const      in     req     none
     **    y              const      in     req     none
     **    z              const      in     req     none
     **    a              const      in     req     none
     **    inverseF          const      in     req     none
     **    phi            const      out    -       none
     **    lambda         const      out    -       none
     **    h              const      out    -       none
     **
     **    Additional explanation of the meaning of parameters
     **    x, y, z  input of cartesian coordinates
     **    a        half major axis of the ellisoid
     **    inverseF    inverse flattening of the ellipsoid
     **    phi      output latitude in degrees
     **    lambda   output longitude in degrees
     **    h        output ellipsoidal height
     **
     **    Return value: (besides the standard return values)
     **    none
     **--------------------------------------------------------------
     * @param Cartesian $c
     * @param double $a
     * @param double $inverseF
     * @return Geographic
     */
  public static function cartesian2geographic($c, $a, $inverseF) {
    /**
     **--------------------------------------------------------------
     **    Source: G. Bakker, J.$c-> de Munck and G.L. Strang van Hees, "Radio Positioning at Sea".
     **    Delft University of Technology, 1995.
     **--------------------------------------------------------------
     */

    /**
     **--------------------------------------------------------------
     **    Explanation of the meaning of variables:
     **        f    flattening of the ellipsoid
     **        ee   first eccentricity squared (e squared in some notations)
     **        rho  distance to minor axis
     **        n    second (East West) principal radius of curvature (N in some notations)
     **--------------------------------------------------------------
     */
    $f = 1.0 / $inverseF;
    $ee = $f * (2.0 - $f);
    $rho = sqrt($c->X * $c->X + $c->Y * $c->Y);
    $n = 0;

    /**
     **--------------------------------------------------------------
     **    Iterative calculation of phi
     **--------------------------------------------------------------
     */
    $phi = 0;
    $diff = 90;

    while ($diff > Constants::DEG_PRECISION) {
        $previous = $phi;
        $n = $a / sqrt(1.0 - $ee * pow(Helpers::degSin($phi), 2));
        $phi = Helpers::degAtan(($c->Z / $rho) + ($n * $ee * (Helpers::degSin($phi) / $rho)));
        $diff = abs($phi - $previous);
    }

    /**
     **--------------------------------------------------------------
     **     Calculation of lambda and h
     **--------------------------------------------------------------
     */
    $lambda = Helpers::degAtan($c->Y / $c->X);
    $h = $rho * Helpers::degCos($phi) +
        $c->Z * Helpers::degSin($phi) -
        $n * (1.0 - $ee * pow(Helpers::degSin($phi), 2));

    return new Geographic($phi, $lambda, $h);
  }

    /**
     **--------------------------------------------------------------
     **    Function name: sim_trans
     **    Description:   3 dimensional similarity transformation (7 parameters)
     **    around another pivot point "a" than the origin
     **
     **    Parameter      Type        In/Out Req/Opt Default
     **    x_in           const      in     req     none
     **    y_in           const      in     req     none
     **    z_in           const      in     req     none
     **    tx             const      in     req     none
     **    ty             const      in     req     none
     **    tz             const      in     req     none
     **    $alpha          const      in     req     none
     **    $beta           const      in     req     none
     **    $gamma          const      in     req     none
     **    delta          const      in     req     none
     **    xa             const      in     req     none
     **    ya             const      in     req     none
     **    za             const      in     req     none
     **    xOut          const      out    -       none
     **    yOut          const      out    -       none
     **    zOut          const      out    -       none
     **
     **    Additional explanation of the meaning of parameters
     **    x_in, y_in, z_in     input coordinates
     **    tx                   translation in direction of x axis
     **    ty                   translation in direction of y axis
     **    tz                   translation in direction of z axis
     **    $alpha                rotation around x axis in radials
     **    $beta                 rotation around y axis in radials
     **    $gamma                rotation around z axis in radials
     **    delta                scale parameter (scale = 1 + delta)
     **    xa, ya, za           coordinates of pivot point a (in case
     **                         of rotation around the center of the
     **                         ellipsoid these parameters are zero)
     **    xOut, yOut, zOut  output coordinates
     **
     **    Return value: (besides the standard return values)
     **    none
     **--------------------------------------------------------------
     * @param Cartesian $input
     * @param Cartesian $translate
     * @param double $alpha
     * @param double $beta
     * @param double $gamma
     * @param double $delta
     * @param Cartesian $pivot
     * @return Cartesian
     */
  public static function simTrans($input, $translate, $alpha, $beta, $gamma, $delta, $pivot) {
    /**
     **--------------------------------------------------------------
     **    Source: HTW, "Handleiding voor de Technische Werkzaamheden van het Kadaster".
     **    Apeldoorn: Kadaster, 1996.
     **--------------------------------------------------------------
     */

    /**
     **--------------------------------------------------------------
     **    Calculate the elements of the rotation_matrix:
     **
     **    a b c
     **    d e f
     **    g h i
     **
     **--------------------------------------------------------------
     */
    $a = cos($gamma) * cos($beta);
    $b = (cos($gamma) * sin($beta) * sin($alpha)) + (sin($gamma) * cos($alpha));
    $c = (-cos($gamma) * sin($beta) * cos($alpha)) + (sin($gamma) * sin($alpha));
    $d = -sin($gamma) * cos($beta);
    $e = (-sin($gamma) * sin($beta) * sin($alpha)) + (cos($gamma) * cos($alpha));
    $f = (sin($gamma) * sin($beta) * cos($alpha)) + (cos($gamma) * sin($alpha));
    $g = sin($beta);
    $h = -cos($beta) * sin($alpha);
    $i = cos($beta) * cos($alpha);

    /**
     **--------------------------------------------------------------
     **    Calculate the elements of the vector input_point:
     **    point_2 = input_point - pivot_point
     **--------------------------------------------------------------
     */
    $x = $input->X - $pivot->X;
    $y = $input->Y - $pivot->Y;
    $z = $input->Z - $pivot->Z;

    /**
     **--------------------------------------------------------------
     **    Calculate the elements of the output vector:
     **    output_point = scale * rotation_matrix * point_2 + translation_vector + pivot_point
     **--------------------------------------------------------------
     */
    $xOut = (1.0 + $delta) * ($a * $x + $b * $y + $c * $z) + $translate->X + $pivot->X;
    $yOut = (1.0 + $delta) * ($d * $x + $e * $y + $f * $z) + $translate->Y + $pivot->Y;
    $zOut = (1.0 + $delta) * ($g * $x + $h * $y + $i * $z) + $translate->Z + $pivot->Z;

    return new Cartesian($xOut, $yOut, $zOut);
  }

    /**
     **--------------------------------------------------------------
     **    Function name: rdProjection
     **    Description:   stereographic const projection
     **
     **    Parameter      Type        In/Out Req/Opt Default
     **    phi            const      in     req     none
     **    lambda         const      in     req     none
     **    xRD           const      out    -       none
     **    yRD           const      out    -       none
     **
     **    Additional explanation of the meaning of parameters
     **    phi         input Bessel latitude in degrees
     **    lambda      input Bessel longitude in degrees
     **    xRD, rd_y  output RD coordinates
     **
     **    Return value: (besides the standard return values)
     **    none
     **--------------------------------------------------------------
     * @param Geographic $input
     * @return Cartesian
     */
  public static function rdProjection($input) {
    /**
     **--------------------------------------------------------------
     **    Source: G. Bakker, J.$c-> de Munck and G.L. Strang van Hees,
     **    "Radio Positioning at Sea". Delft University of Technology, 1995.
     **            G. Strang van Hees, "Globale en lokale geodetische systemen".
     **    Delft: Nederlandse Commissie voor Geodesie (NCG), 1997.
     **--------------------------------------------------------------
     */

    /**
     **--------------------------------------------------------------
     **    Explanation of the meaning of constants:
     **        f                         flattening of the ellipsoid
     **        ee                        first eccentricity squared (e squared in some notations)
     **        e                         first eccentricity
     **        eea                       second eccentricity squared (e' squared in some notations)
     **
     **        phiAmersfoortSphere     latitude of projection base point Amersfoort
     **                                  on sphere in degrees
     **        lambdaAmersfoortSphere  longitude of projection base point Amersfoort
     **                                  on sphere in degrees
     **
     **        r1                        first (North South) principal radius of curvature
     **                                  in Amersfoort (M in some notations)
     **        r2                        second (East West) principal radius of curvature in
     **                                  Amersfoort (N in some notations)
     **        rSphere                  radius of sphere
     **
     **        n                         constant of Gaussian projection n = 1.000475...
     **        qAmersfoort              isometric latitude of Amersfoort on ellipsiod
     **        wAmersfoort              isometric latitude of Amersfoort on sphere
     **        m                         constant of Gaussian projection m = 0.003773...
     **                                 (also named c in some notations)
     **--------------------------------------------------------------
     */
    $f = 1 / Constants::INV_F_BESSEL;
    $ee = $f * (2 - $f);
    $e = sqrt($ee);
    $eea = $ee / (1.0 - $ee);

    $phiAmersfoortSphere = Helpers::degAtan(Helpers::degTan(Constants::PHI_AMERSFOORT_BESSEL) /
            sqrt(1 + $eea * pow(Helpers::degCos(Constants::PHI_AMERSFOORT_BESSEL), 2)));
    $lambdaAmersfoortSphere = Constants::LAMBDA_AMERSFOORT_BESSEL;

    $r1 = Constants::A_BESSEL * (1 - $ee) /
        pow(sqrt(1 - $ee * pow(Helpers::degSin(Constants::PHI_AMERSFOORT_BESSEL), 2)), 3);
    $r2 = Constants::A_BESSEL /
        sqrt(1.0 - $ee * pow(Helpers::degSin(Constants::PHI_AMERSFOORT_BESSEL), 2));
    $rSphere = sqrt($r1 * $r2);

    $n = sqrt(1 + $eea * pow(Helpers::degCos(Constants::PHI_AMERSFOORT_BESSEL), 4));
    $qAmersfoort = Helpers::atanh(Helpers::degSin(Constants::PHI_AMERSFOORT_BESSEL)) -
        ($e * Helpers::atanh($e * Helpers::degSin(Constants::PHI_AMERSFOORT_BESSEL)));
    $wAmersfoort = log(Helpers::degTan(45 + 0.5 * $phiAmersfoortSphere));
    $m = $wAmersfoort - $n * $qAmersfoort;

    /**
     **--------------------------------------------------------------
     **    Explanation of the meaning of variables:
     **        q                    isometric latitude on ellipsoid
     **        w                    isometric latitude on sphere
     **        phiSphere           latitude on sphere in degrees
     **        deltaLambdaSphere  difference in longitude on sphere with Amersfoort in degrees
     **        psi                  distance angle from Amersfoort on sphere
     **        $alpha                azimuth from Amersfoort
     **        r                    distance from Amersfoort in projection plane
     **--------------------------------------------------------------
     */
    $q = Helpers::atanh(Helpers::degSin($input->phi)) -
        $e * Helpers::atanh($e * Helpers::degSin($input->phi));
    $w = ($n * $q) + $m;
    $phiSphere = 2 * Helpers::degAtan(exp($w)) - 90;
    $deltaLambdaSphere = $n * ($input->lambda - $lambdaAmersfoortSphere);
    $sinHalfPsiSquared = pow(Helpers::degSin(0.5 * ($phiSphere - $phiAmersfoortSphere)), 2) +
        pow(Helpers::degSin(0.5 * $deltaLambdaSphere), 2) *
        Helpers::degCos($phiSphere) * Helpers::degCos($phiAmersfoortSphere);
    $sinHalfPsi = sqrt($sinHalfPsiSquared);
    $cosHalfPsi = sqrt(1 - $sinHalfPsiSquared);
    $tanHalfPsi = $sinHalfPsi / $cosHalfPsi;
    $sinPsi = 2 * $sinHalfPsi * $cosHalfPsi;
    $cosPsi = 1 - 2 * $sinHalfPsiSquared;
    $sinAlpha = Helpers::degSin($deltaLambdaSphere) * (Helpers::degCos($phiSphere) / $sinPsi);
    $cosAlpha = (Helpers::degSin($phiSphere) - Helpers::degSin($phiAmersfoortSphere) * $cosPsi) /
        (Helpers::degCos($phiAmersfoortSphere) * $sinPsi);
    $r = 2 * Constants::SCALE_RD * $rSphere * $tanHalfPsi;

    $xRD = $r * $sinAlpha + Constants::X_AMERSFOORT_RD;
    $yRD = $r * $cosAlpha + Constants::Y_AMERSFOORT_RD;

    return new Cartesian($xRD, $yRD);
  }

    /**
     **--------------------------------------------------------------
     **    Function name: inv_rd_projection
     **    Description:   inverse stereographic const projection
     **
     **    Parameter      Type        In/Out Req/Opt Default
     **    x_rd           const      in     req     none
     **    y_rd           const      in     req     none
     **    phi            const      out    -       none
     **    lambda         const      out    -       none
     **
     **    Additional explanation of the meaning of parameters
     **    x_rd, rd_y  input RD coordinates
     **    phi         output Bessel latitude in degrees
     **    lambda      output Bessel longitude in degrees
     **
     **    Return value: (besides the standard return values)
     **    none
     **--------------------------------------------------------------
     * @param $input
     * @return Geographic
     */
  public static function invRdProjection($input) {
    /**
     **--------------------------------------------------------------
     **    Source: G. Bakker, J.$c-> de Munck and G.L. Strang van Hees,
     **            "Radio Positioning at Sea". Delft University of Technology, 1995.
     **            G. Strang van Hees, "Globale en lokale geodetische systemen".
     **            Delft: Nederlandse Commissie voor Geodesie (NCG), 1997.
     **--------------------------------------------------------------
     */

    /**
     **--------------------------------------------------------------
     **    Explanation of the meaning of constants:
     **        f                         flattening of the ellipsoid
     **        ee                        first eccentricity squared (e squared in some notations)
     **        e                         first eccentricity
     **        eea                       second eccentricity squared (e' squared in some notations)
     **
     **        phiAmersfoortSphere     latitude of projection base point Amersfoort
     **                                  on sphere in degrees
     **
     **        r1                        first (North South) principal radius of curvature
     **                                  in Amersfoort (M in some notations)
     **        r2                        second (East West) principal radius of curvature
     **                                  in Amersfoort (N in some notations)
     **        rSphere                  radius of sphere
     **
     **        n                         constant of Gaussian projection n = 1.000475...
     **        qAmersfoort              isometric latitude of Amersfoort on ellipsiod
     **        wAmersfoort              isometric latitude of Amersfoort on sphere
     **        m                         constant of Gaussian projection m = 0.003773...
     **                                  (also named c in some notations)
     **--------------------------------------------------------------
     */
    $f = 1 / Constants::INV_F_BESSEL;
    $ee = $f * (2 - $f);
    $e = sqrt($ee);
    $eea = $ee / (1.0 - $ee);

    $phiAmersfoortSphere = self::degAtan(self::degTan(Constants::PHI_AMERSFOORT_BESSEL) /
            sqrt(1 + $eea * pow(self::degCos(Constants::PHI_AMERSFOORT_BESSEL), 2)));

    $r1 = Constants::A_BESSEL * (1 - $ee) /
        pow(sqrt(1 - $ee * pow(self::degSin(Constants::PHI_AMERSFOORT_BESSEL), 2)), 3);
    $r2 = Constants::A_BESSEL / sqrt(1.0 - $ee *
            pow(self::degSin(Constants::PHI_AMERSFOORT_BESSEL), 2));
    $rSphere = sqrt($r1 * $r2);

    $n = sqrt(1 + $eea * pow(self::degCos(Constants::PHI_AMERSFOORT_BESSEL), 4));
    $qAmersfoort = self::atanh(self::degSin(Constants::PHI_AMERSFOORT_BESSEL)) -
        $e * self::atanh($e * self::degSin(Constants::PHI_AMERSFOORT_BESSEL));
    $wAmersfoort = log(self::degTan(45 + 0.5 * $phiAmersfoortSphere));
    $m = $wAmersfoort - $n * $qAmersfoort;

    /**
     **--------------------------------------------------------------
     **    Explanation of the meaning of variables:
     **        r                    distance from Amersfoort in projection plane
     **        $alpha                azimuth from Amersfoort
     **        psi                  distance angle from Amersfoort on sphere in degrees
     **        phiSphere           latitide on sphere in degrees
     **        deltaLambdaSphere  difference in longitude on sphere with Amersfoort in degrees
     **        w                    isometric latitude on sphere
     **        q                    isometric latitude on ellipsiod
     **--------------------------------------------------------------
     */
    $r = sqrt(pow($input->X - Constants::X_AMERSFOORT_RD, 2) +
        pow($input->Y - Constants::Y_AMERSFOORT_RD, 2));

    $sinAlpha = ($input->X - Constants::X_AMERSFOORT_RD) / $r;
    if ($r < Constants::PRECISION){
        $sinAlpha = 0;
    }

    $cosAlpha = ($input->Y - Constants::Y_AMERSFOORT_RD) / $r;
    if ($r < Constants::PRECISION){
        $cosAlpha = 1;
    }

    $psi = 2 * self::degAtan($r / (2 * Constants::SCALE_RD * $rSphere));
    $phiSphere = self::degAsin($cosAlpha * self::degCos($phiAmersfoortSphere) *
            self::degSin($psi) + self::degSin($phiAmersfoortSphere) * self::degCos($psi));
    $deltaLambdaSphere = self::degAsin(($sinAlpha * self::degSin($psi)) / self::degCos($phiSphere));

    $lambda = $deltaLambdaSphere / $n + Constants::LAMBDA_AMERSFOORT_BESSEL;

    $w = self::atanh(self::degSin($phiSphere));
    $q = ($w - $m) / $n;

    /**
     **--------------------------------------------------------------
     **    Iterative calculation of phi
     **--------------------------------------------------------------
     */
      $phi = 0;
      $diff = 90;
    while ($diff > Constants::DEG_PRECISION) {
        $previous = $phi;
        $phi = 2 * self::degAtan(
                exp($q + 0.5 * $e * log((1 + $e * self::degSin($phi)) / (1 - $e * self::degSin($phi))))
            ) - 90;
        $diff = abs($phi - $previous);
    }

    return new Geographic($phi, $lambda);
  }

  public static function rdCorrection($pseudo) {
    $dx = self::$gridDX->gridInterpolation($pseudo->X, $pseudo->Y);
    $dy = self::$gridDY->gridInterpolation($pseudo->X, $pseudo->Y);
    return new Cartesian($pseudo->X - $dx, $pseudo->Y - $dy, $pseudo->Z);
  }

  /**
   **--------------------------------------------------------------
   **    Function name: inv_rd_correction
   **    Description:   remove the modelled distortions in the RD coordinate system
   **
   **    Parameter      Type        In/Out Req/Opt Default
   **    x_rd           const      in     req     none
   **    y_rd           const      in     req     none
   **    x_pseudo_rd    const      out    -       none
   **    x_pseudo_rd    const      out    -       none
   **
   **    Additional explanation of the meaning of parameters
   **    x_rd, y_rd                input coordinates in real RD
   **    x_pseudo_rd, y_pseudo_rd  output coordinates in undistorted pseudo RD
   **
   **    Return value: (besides the standard return values)
   **    none
   **--------------------------------------------------------------
   */
  public static function invRdCorrection($rd) {
    /**
     **--------------------------------------------------------------
     **    The grid values are formally in pseudo RD. For the interpolation
     *     below the RD values are used.
     **    The intoduced error is certainly smaller than 0.0001 m for the X2c.grd and Y2c.grd.
     **--------------------------------------------------------------
     */
    $dx = self::$gridDX->gridInterpolation($rd->X, $rd->Y);
    $dy = self::$gridDY->gridInterpolation($rd->X, $rd->Y);
    return new Cartesian($rd->X + $dx, $rd->Y + $dy, $rd->Z);
  }

    /** @var GrdFile */
    public static $gridDX = null;
    /** @var GrdFile */
    public static $gridDY = null;
}

Helpers::$gridDX = GrdFile::GRID_FILE_DX();
Helpers::$gridDY = GrdFile::GRID_FILE_DY();
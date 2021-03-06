<?php
/**
 * Created by PhpStorm.
 * User: daan
 * Date: 9/29/18
 * Time: 2:19 PM
 */

namespace StudioSeptember\RDNAPTrans;


class Constants
{

    /**
     ** --------------------------------------------------------------
     **    Continuation of static data declarations
     **    Geographic NL-Bessel coordinates of Amersfoort (pivot point and projection base point)
     **        phi     latitude in decimal degrees
     **        lambda  longitude in decimal degrees
     **        h       ellipsoidal height in meters
     **    Source of constants:
     **        Hk.J. Heuvelink, "De stereografische kaartprojectie in
     **        hare toepassing bij de Rijksdriehoeksmeting".
     **        Delft: Rijkscommissie voor Graadmeting en Waterpassing, 1918.
     **        HTW, "Handleiding voor de Technische Werkzaamheden van het Kadaster".
     **        Apeldoorn: Kadaster, 1996.
     **--------------------------------------------------------------
     */
    const PHI_AMERSFOORT_BESSEL = 52.0 + (9.0 / 60.0) + (22.178 / 3600.0);
    const LAMBDA_AMERSFOORT_BESSEL = 5.0 + (23.0 / 60.0) + (15.500 / 3600.0);
    const H_AMERSFOORT_BESSEL = 0.0;
    /**
     **--------------------------------------------------------------
     **    Continuation of static data declarations
     **    Parameters of ellipsoids Bessel1841 and GRS80
     **        a      half major axis in meters
     **        inv_f  inverse flattening
     **    Source of constants: HTW, "Handleiding voor de Technische
     *  Werkzaamheden van het Kadaster". Apeldoorn: Kadaster, 1996.
     **--------------------------------------------------------------
     */

    const A_BESSEL = 6377397.155;
    const INV_F_BESSEL = 299.1528128;
    const A_ETRS = 6378137;
    const INV_F_ETRS = 298.257222101;

    /**
     **--------------------------------------------------------------
     **    Continuation of static data declarations
     **    Transformation parameters relative to pivot point Amersfoort.
     *     Note: Do NOT confuse with parameters for the center of the ellipsoid!
     **        tx     translation in direction of x axis in meters
     **        ty     translation in direction of y axis in meters
     **        tz     translation in direction of z axis in meters
     **        alpha  rotation around x axis in radials
     **        beta   rotation around y axis in radials
     **        gamma  rotation around z axis in radials
     **        delta  scale parameter (scale = 1 + delta)
     **    Source of constants: A. de Bruijne, J. van Buren, A. K\u0148sters and
     *     H. van der Marel, "De geodetische referentiestelsels van Nederland;
     *     Definitie en vastlegging van ETRS89, RD en NAP en hun onderlinge relatie".
     *     Delft: Nederlandse Commissie voor Geodesie (NCG), to be published in 2005.
     **--------------------------------------------------------------
     */
    const TX_BESSEL_ETRS = 593.0248;
    const TY_BESSEL_ETRS = 25.9984;
    const TZ_BESSEL_ETRS = 478.7459;
    const ALPHA_BESSEL_ETRS = 1.9342e-6;
    const BETA_BESSEL_ETRS = -1.6677e-6;
    const GAMMA_BESSEL_ETRS = 9.1019e-6;
    const DELTA_BESSEL_ETRS = 4.0725e-6;

    const TX_ETRS_BESSEL = -593.0248;
    const TY_ETRS_BESSEL = -25.9984;
    const TZ_ETRS_BESSEL = -478.7459;
    const ALPHA_ETRS_BESSEL = -1.9342e-6;
    const BETA_ETRS_BESSEL = 1.6677e-6;
    const GAMMA_ETRS_BESSEL = -9.1019e-6;
    const DELTA_ETRS_BESSEL = -4.0725e-6;

    /**
     **--------------------------------------------------------------
     **    Continuation of static data declarations
     **    Parameters of RD projection
     **        scale         scale factor (k in some notations)
     **                      this factor was first defined by Hk.J. Heuvelink as
     *                       pow(10,-400e-7), nowadays we define it as exactly 0.9999079
     **        x_amersfoort  false Easting
     **        y_amersfoort  false Northing
     **    Source of constants:
     **        G. Bakker, J.C. de Munck and G.L. Strang van Hees,
     *         "Radio Positioning at Sea". Delft University of Technology, 1995.
     **        G. Strang van Hees, "Globale en lokale geodetische systemen".
     *         Delft: Nederlandse Commissie voor Geodesie (NCG), 1997.
     **--------------------------------------------------------------
     */
    const SCALE_RD = 0.9999079;
    const X_AMERSFOORT_RD = 155000;
    const Y_AMERSFOORT_RD = 463000;

    /**
     **--------------------------------------------------------------
     **    Continuation of static data declarations
     **    Precision parameters for iterations (respectively in meters and degrees)
     **--------------------------------------------------------------
     */
    const PRECISION = 0.0001;
    const DEG_PRECISION = self::PRECISION / (40e6 * 360);

    /**
     **--------------------------------------------------------------
     **    Continuation of static data declarations
     **    Mean difference between NAP and ellipsoidal Bessel height.
     *     This is only used for getting from x, y in RD to phi, lambda in ETRS89.
     **--------------------------------------------------------------
     */
    const MEAN_GEOID_HEIGHT_BESSEL = 0.0;
}
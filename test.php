<?PHP
require_once('vendor/autoload.php');


/**
 * > > Zuid-Limburg:
> > Ref:   Latitude: 50.7925849160 Longitude: 5.7737955480 Altitude:
> > 245.948
> > Ref:   Easting:     182260.450 Northing:    311480.670 Altitude:
> > 200.000
 */
$zuidLimburg = new \StudioSeptember\RDNAPTrans\Cartesian(182260.450, 311480.670, 200);

$result = \StudioSeptember\RDNAPTrans\Transform::rdnap2etrs($zuidLimburg);

var_dump($result);
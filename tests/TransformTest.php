<?php
/**
 * Created by PhpStorm.
 * User: daan
 * Date: 9/29/18
 * Time: 8:18 PM
 */
namespace StudioSeptember\RDNAPTrans\Tests;

use StudioSeptember\RDNAPTrans\Transform;
use StudioSeptember\RDNAPTrans\Cartesian;
use StudioSeptember\RDNAPTrans\Geographic;

class TransformTest extends \PHPUnit_Framework_TestCase
{

    public function testRd2etrs()
    {

        foreach (self::POINTS as $point) {
            $cartesian = new Cartesian($point['x'], $point['y'], $point['NAP']);
            $result = Transform::rd2etrs($cartesian);

            $this->assertLessThan(0.001, abs($result->phi - $point['lat']));
            $this->assertLessThan(0.001, abs($result->lambda - $point['long']));
            $this->assertEquals($point['NAP'], $result->h);
        }
    }

    public function testNap2etrs()
    {
        foreach (self::POINTS as $point) {
            $result = Transform::nap2etrs($point['lat'], $point['long'], $point['NAP']);
            $this->assertLessThan(0.001, abs($result - $point['h']));
        }
    }

    public function testRdnap2etrs()
    {
        foreach (self::POINTS as $point) {
            $cartesian = new Cartesian($point['x'], $point['y'], $point['NAP']);
            $result = Transform::rdnap2etrs($cartesian);

            $this->assertLessThan(0.001, abs($result->phi - $point['lat']));
            $this->assertLessThan(0.001, abs($result->lambda - $point['long']));
            $this->assertLessThan(0.001, abs($result->h - $point['h']));
        }
    }




    public function testEtrs2rd()
    {
        foreach (self::POINTS as $point) {

            $geographic = new Geographic($point['lat'], $point['long'], $point['h']);
            $result = Transform::etrs2rd($geographic);

            $this->assertLessThan(0.001, abs($result->X - $point['x']));
            $this->assertLessThan(0.001, abs($result->Y - $point['y']));
            $this->assertEquals($point['h'], $result->Z);
        }
    }

    public function testEtrs2nap()
    {
        foreach (self::POINTS as $point) {

            $geographic = new Geographic($point['lat'], $point['long'], $point['h']);
            $result = Transform::etrs2nap($geographic);

            $this->assertLessThan(0.001, abs($result - $point['NAP']));
        }
    }

    public function testEtrs2rdnap()
    {
        foreach (self::POINTS as $point) {

            $geographic = new Geographic($point['lat'], $point['long'], $point['h']);
            $result = Transform::etrs2rdnap($geographic);

            $this->assertLessThan(0.001, abs($result->X - $point['x']));
            $this->assertLessThan(0.001, abs($result->Y - $point['y']));
            $this->assertLessThan(0.001, abs($result->Z - $point['NAP']));
        }
    }

    /**
     * 1    Texel    117380.12    575040.34    1    53.16075304    4.824761912    42.8614
     * 2    Noord-Groningen    247380.56    604580.78    2    53.41948205    6.776726674    42.3586
     * 3    Amersfoort    155000    463000    0    52.1551729    5.387203657    43.2551
     * 4    Amersfoort_100m    155000    463000    100    52.15517291    5.387203658    143.2551
     * 5    Zeeuws-Vlaanderen    16460.91    377380.23    3    51.36860715    3.397588595    47.4024
     * 6    Zuid-Limburg    182260.45    311480.67    200    50.79258492    5.773795548    245.9478
     * 7    Maasvlakte    64640.89    440700.01    4    51.9473939    4.072887101    47.5968
     * 08*    outside    400000.23    100000.45    5    48.84303021    8.723260235    52.0289
     * 09*    no_rd&geoid    100000.67    300000.89    6    50.68742039    4.608971813    51.6108
     * 10*    no_geoid    100000.67    350000.89    6    51.1368252    4.601375361    50.9672
     * 11*    no_rd    79000.01    500000.23    7    52.48244084    4.268403889    49.9436
     * 12*    edge_rd    50000.45    335999.67    8    51.00397653    3.89124783    52.7427
     */

    const POINTS = [
        ['name' => 'Texel', 'x' => 117380.12, 'y' => 575040.34, 'NAP' => 1, 'lat' => 53.16075304, 'long' => 4.824761912, 'h' => 42.8614],
        ['name' => 'Noord-Groningen', 'x' => 247380.56, 'y' => 604580.78, 'NAP' => 2, 'lat' => 53.41948205, 'long' => 6.776726674, 'h' => 42.3586],
        ['name' => 'Amersfoort', 'x' => 155000, 'y' => 463000, 'NAP' => 0, 'lat' => 52.1551729, 'long' => 5.387203657, 'h' => 43.2551],
        ['name' => 'Amersfoort_100m', 'x' => 155000, 'y' => 463000, 'NAP' => 100, 'lat' => 52.15517291, 'long' => 5.387203658, 'h' => 143.2551],
        ['name' => 'Zeeuws-Vlaanderen', 'x' => 16460.91, 'y' => 377380.23, 'NAP' => 3, 'lat' => 51.36860715, 'long' => 3.397588595, 'h' => 47.4024],
        ['name' => 'Zuid-Limburg', 'x' => 182260.45, 'y' => 311480.67, 'NAP' => 200, 'lat' => 50.79258492, 'long' => 5.773795548, 'h' => 245.9478],
        ['name' => 'Maasvlakte', 'x' => 64640.89, 'y' => 440700.01, 'NAP' => 4, 'lat' => 51.9473939, 'long' => 4.072887101, 'h' => 47.5968],
        ['name' => 'outside', 'x' => 400000.23, 'y' => 100000.45, 'NAP' => 5, 'lat' => 48.84303021, 'long' => 8.723260235, 'h' => 52.0289],
        ['name' => 'no_rd&geoid', 'x' => 100000.67, 'y' => 300000.89, 'NAP' => 6, 'lat' => 50.68742039, 'long' => 4.608971813, 'h' => 51.6108],
        ['name' => 'no_geoid', 'x' => 100000.67, 'y' => 350000.89, 'NAP' => 6, 'lat' => 51.1368252, 'long' => 4.601375361, 'h' => 50.9672],
        ['name' => 'no_rd', 'x' => 79000.01, 'y' => 500000.23, 'NAP' => 7, 'lat' => 52.48244084, 'long' => 4.268403889, 'h' => 49.9436],
        ['name' => 'edge_rd', 'x' => 50000.45, 'y' => 335999.67, 'NAP' => 8, 'lat' => 51.00397653, 'long' => 3.89124783, 'h' => 52.7427],
    ];
}

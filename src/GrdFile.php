<?php
/**
 * Created by PhpStorm.
 * User: daan
 * Date: 9/29/18
 * Time: 3:22 PM
 */

namespace StudioSeptember\RDNAPTrans;


class GrdFile
{

    /**
     **--------------------------------------------------------------
     **    Continuation of public static function data declarations
     **    Names of grd files
     **
     **    Grd files are binary grid files in the format of the program Surfer(R)
     **    The header contains information on the number of grid points,
     **    bounding box and extreme values.
     **
     **    RD-corrections in x and y
     **
     **          -8000 meters < RD Easting  (stepsize 1 km) < 301000 meters
     **         288000 meters < RD Northing (stepsize 1 km) < 630000 meters
     **
     **    Geoid model NLGEO2004
     **
     **        50.525   degrees < ETRS89 latitude  (stepsize 0.050000 degrees) < 53.675 degrees
     **         3.20833 degrees < ETRS89 longitude (stepsize 0.083333 degrees) <  7.45833 degrees
     **
     **        Alternative notation:
     **        50\u0248 31' 30" < ETRS89_latitude  (stepsize 0\u0248 3' 0") < 53\u0248 40' 30"
     **         3\u0248 12' 30" < ETRS89_longitude (stepsize 0\u0248 5' 0") <  7\u0248 27' 30"
     **
     **        The stepsizes correspond to about 5,5 km x 5,5 km in the Netherlands.
     **--------------------------------------------------------------
     */
    private $header = [];

    /** @var resource */
    private $grdInner;

    /** Constant <code>GRID_FILE_DX</code> */

    public static function GRID_FILE_DX()
    {
        return new GrdFile('x2c.grd');
    }

    /** Constant <code>GRID_FILE_DY</code> */
    public static function GRID_FILE_DY()
    {
        return new GrdFile('y2c.grd');
    }

    /** Constant <code>GRID_FILE_GEOID</code> */
    public static function GRID_FILE_GEOID()
    {
        return new GrdFile('nlgeo04.grd');
    }

    /**
     * <p>Constructor for GrdFile.</p>
     *
     * @param string grdFileName a {@link java.net.URL} object.
     */
    public function __construct($grdFileName)
    {
        /**
         **--------------------------------------------------------------
         **    Grd files are binary grid files in the format of the program Surfer(R)
         **--------------------------------------------------------------
         */
        $cursor = 0;

        $data = Reader::read($grdFileName);
        if (!$data) throw new \RuntimeException("Unable to read empty source ${grdFileName}");

        fseek($data, $cursor);
        // Read file id
        $idString = fread($data, 4);
        $cursor += 4;

        /**
         **--------------------------------------------------------------
         **    Checks
         **--------------------------------------------------------------
         */

        if ($idString !== 'DSBB') {
            throw new \RuntimeException("${grdFileName} is not a valid grd file.
      \n Expected first four chars of file to be 'DSBB', but found ${idString}");
        }

        $this->grdInner = $data;
        $this->header = GrdFile::readGrdFileHeader($data, $cursor);
        $this->header += [
            'stepSizeX' => ($this->header['maxX'] - $this->header['minX']) / ($this->header['sizeX'] - 1),
            'stepSizeY' => ($this->header['maxY'] - $this->header['minY']) / ($this->header['sizeY'] - 1)
        ];
        $this->header += [
            'safeMinX' => $this->header['minX'] + $this->header['stepSizeX'],
            'safeMaxX' => $this->header['maxX'] - $this->header['stepSizeX'],
            'safeMinY' => $this->header['minY'] + $this->header['stepSizeY'],
            'safeMaxY' => $this->header['maxY'] - $this->header['stepSizeY']
        ];

    }

    /**
     **--------------------------------------------------------------
     **    Function name: grid_interpolation
     **    Description:   grid interpolation using Overhauser splines
     **
     **    Parameter      Type        In/Out Req/Opt Default
     **    x              double      in     req     none
     **    y              double      in     req     none
     **    grd_file       string      in     req     none
     **    value          double      out    -       none
     **
     **    Additional explanation of the meaning of parameters
     **    x, y           coordinates of the point for which a interpolated value is desired
     **    grd_file       name of the grd file to be read
     **    record_value   output of the interpolated value
     **
     **    Return value: (besides the standard return values)
     **    none
     **--------------------------------------------------------------
     */

    /**
     * <p>gridInterpolation.</p>
     *
     * @param double x.
     * @param double y.
     * @return double a {@link rdnaptrans.value.OptionalDouble} object.
     */
    public function gridInterpolation($x, $y)
    {
        /**
         **--------------------------------------------------------------
         **    Explanation of the meaning of variables:
         **    size_x     number of grid values in x direction (row)
         **    size_y     number of grid values in y direction (col)
         **    min_x      minimum of x
         **    max_x      maximum of x
         **    min_y      minimum of y
         **    max_y      maximum of x
         **    min_value  minimum value in grid (besides the error values)
         **    max_value  maximum value in grid (besides the error values)
         **--------------------------------------------------------------
         */

        /**
         **--------------------------------------------------------------
         **    Check for location safely inside the bounding box of grid
         **--------------------------------------------------------------
         */
        $header = $this->header;
        if ($x <= $header['safeMinX'] || $x >= $header['safeMaxX'] ||
            $y <= $header['safeMinY'] || $y >= $header['safeMaxY']) {
            return null;
        }

        /**
         **--------------------------------------------------------------
         **    The selected grid points are situated around point X like this:
         **
         **        12  13  14  15
         **
         **         8   9  10  11
         **               X
         **         4   5   6   7
         **
         **         0   1   2   3
         **
         **    ddx and ddy (in parts of the grid interval) are defined relative
         **    to grid point 9, respectively to the right and down.
         **--------------------------------------------------------------
         */
        $ddx = ($x - $header['minX']) /
            $header['stepSizeX'] - floor(($x - $header['minX']) / $header['stepSizeX']);
        $ddy = 1 - (($y - $header['minY']) /
                $header['stepSizeY'] - floor(($y - $header['minY']) / $header['stepSizeY']));

        /**
         **--------------------------------------------------------------
         **    Calculate the record numbers of the selected grid points
         **    The records are numbered from lower left corner to the uper right corner starting with 0:
         **
         **    size_x*(size_y-1) . . size_x*size_y-1
         **                   .                    .
         **                   .                    .
         **                   0 . . . . . . size_x-1
         **--------------------------------------------------------------
         */
        $recordNumber = [];
        $recordNumber[5] = (int)(($x - $header['minX']) / $header['stepSizeX'] + floor(($y - $header['minY']) / $header['stepSizeY']) * $header['sizeX']);
        $recordNumber[0] = $recordNumber[5] - $header['sizeX'] - 1;
        $recordNumber[1] = $recordNumber[5] - $header['sizeX'];
        $recordNumber[2] = $recordNumber[5] - $header['sizeX'] + 1;
        $recordNumber[3] = $recordNumber[5] - $header['sizeX'] + 2;
        $recordNumber[4] = $recordNumber[5] - 1;
        $recordNumber[6] = $recordNumber[5] + 1;
        $recordNumber[7] = $recordNumber[5] + 2;
        $recordNumber[8] = $recordNumber[5] + $header['sizeX'] - 1;
        $recordNumber[9] = $recordNumber[5] + $header['sizeX'];
        $recordNumber[10] = $recordNumber[5] + $header['sizeX'] + 1;
        $recordNumber[11] = $recordNumber[5] + $header['sizeX'] + 2;
        $recordNumber[12] = $recordNumber[5] + 2 * $header['sizeX'] - 1;
        $recordNumber[13] = $recordNumber[5] + 2 * $header['sizeX'];
        $recordNumber[14] = $recordNumber[5] + 2 * $header['sizeX'] + 1;
        $recordNumber[15] = $recordNumber[5] + 2 * $header['sizeX'] + 2;

        /**
         **--------------------------------------------------------------
         **    Read the record values of the selected grid point
         **    Outside the validity area the records have a very large value (circa 1.7e38).
         **--------------------------------------------------------------
         */
        $recordValue = [];

        for ($i = 0; $i < 16; $i += 1) {
            $recordValue[$i] = $this->readGrdFileBody($recordNumber[$i]);
            if (
                $recordValue[$i] > $header['maxValue'] + Constants::PRECISION ||
                $recordValue[$i] < $header['minValue'] - Constants::PRECISION
            ) {
                return null;
            }
        }

        /**
         **--------------------------------------------------------------
         **    Calculation of the multiplication factors
         **--------------------------------------------------------------
         */
        $f = [];
        $g = [];
        $gfac = [];
        $f[0] = -0.5 * $ddx + $ddx * $ddx - 0.5 * $ddx * $ddx * $ddx;
        $f[1] = 1.0 - 2.5 * $ddx * $ddx + 1.5 * $ddx * $ddx * $ddx;
        $f[2] = 0.5 * $ddx + 2.0 * $ddx * $ddx - 1.5 * $ddx * $ddx * $ddx;
        $f[3] = -0.5 * $ddx * $ddx + 0.5 * $ddx * $ddx * $ddx;
        $g[0] = -0.5 * $ddy + $ddy * $ddy - 0.5 * $ddy * $ddy * $ddy;
        $g[1] = 1.0 - 2.5 * $ddy * $ddy + 1.5 * $ddy * $ddy * $ddy;
        $g[2] = 0.5 * $ddy + 2.0 * $ddy * $ddy - 1.5 * $ddy * $ddy * $ddy;
        $g[3] = -0.5 * $ddy * $ddy + 0.5 * $ddy * $ddy * $ddy;

        $gfac[12] = $f[0] * $g[0];
        $gfac[8] = $f[0] * $g[1];
        $gfac[4] = $f[0] * $g[2];
        $gfac[0] = $f[0] * $g[3];
        $gfac[13] = $f[1] * $g[0];
        $gfac[9] = $f[1] * $g[1];
        $gfac[5] = $f[1] * $g[2];
        $gfac[1] = $f[1] * $g[3];
        $gfac[14] = $f[2] * $g[0];
        $gfac[10] = $f[2] * $g[1];
        $gfac[6] = $f[2] * $g[2];
        $gfac[2] = $f[2] * $g[3];
        $gfac[15] = $f[3] * $g[0];
        $gfac[11] = $f[3] * $g[1];
        $gfac[7] = $f[3] * $g[2];
        $gfac[3] = $f[3] * $g[3];

        /*
         **--------------------------------------------------------------
         **    Calculation of the interpolated value
         **    Applying the multiplication factors on the selected grid values
         **--------------------------------------------------------------
         */
        $value = 0.0;
        for ($i = 0; $i < 16; $i += 1) {
            $value += $gfac[$i] * $recordValue[$i];
        }

        return $value;
    }

    /**
     **--------------------------------------------------------------
     **    Function name: read_grd_file_header
     **    Description:   reads the header of a grd file
     **
     **    Parameter      Type        In/Out Req/Opt Default
     **    filename       string      in     req     none
     **    size_x         short int   out    -       none
     **    size_y         short int   out    -       none
     **    min_x          double      out    -       none
     **    max_x          double      out    -       none
     **    min_y          double      out    -       none
     **    max_y          double      out    -       none
     **    min_value      double      out    -       none
     **    max_value      double      out    -       none
     **
     **    Additional explanation of the meaning of parameters
     **    filename   name of the to be read binary file
     **    size_x     number of grid values in x direction (row)
     **    size_y     number of grid values in y direction (col)
     **    min_x      minimum of x
     **    max_x      maximum of x
     **    min_y      minimum of y
     **    max_y      maximum of x
     **    min_value  minimum value in grid (besides the error values)
     **    max_value  maximum value in grid (besides the error values)
     **
     **    Return value: (besides the standard return values)
     **    none
     **--------------------------------------------------------------
     * @param $input
     * @param $cursor
     * @return array
     */
    public static function readGrdFileHeader($input, $cursor)
    {
        /**
         **--------------------------------------------------------------
         **    Read output parameters
         **--------------------------------------------------------------
         */

        $sizeX = Reader::readShort($input, $cursor);
        $cursor += 2;
        $sizeY = Reader::readShort($input, $cursor);
        $cursor += 2;
        $minX = Reader::readDouble($input, $cursor);
        $cursor += 8;
        $maxX = Reader::readDouble($input, $cursor);
        $cursor += 8;
        $minY = Reader::readDouble($input, $cursor);
        $cursor += 8;
        $maxY = Reader::readDouble($input, $cursor);
        $cursor += 8;
        $minValue = Reader::readDouble($input, $cursor);
        $cursor += 8;
        $maxValue = Reader::readDouble($input, $cursor);

        return [
            'sizeX' => $sizeX,
            'sizeY' => $sizeY,
            'minX' => $minX,
            'maxX' => $maxX,
            'minY' => $minY,
            'maxY' => $maxY,
            'minValue' => $minValue,
            'maxValue' => $maxValue
        ];
    }

    /**
     **--------------------------------------------------------------
     **    Function name: read_grd_file_body
     **    Description:   reads a value from a grd file
     **
     **    Parameter      Type        In/Out Req/Opt Default
     **    filename       string      in     req     none
     **    number         long int    in     req     none
     **    value          float       out    -       none
     **
     **    Additional explanation of the meaning of parameters
     **    filename       name of the grd file to be read
     **    recordNumber  number defining the position in the file
     **    record_value   output of the read value
     **
     **    Return value: (besides the standard return values)
     **    none
     **--------------------------------------------------------------
     * @param $recordNumber
     * @return array
     */
    public function readGrdFileBody($recordNumber)
    {
        $recordLength = 4;
        $headerLength = 56;

        /**
         **--------------------------------------------------------------
         **    Read
         **    Grd files are binary grid files in the format of the program Surfer(R)
         **    The first "headerLength" bytes are the header of the file
         **    The body of the file consists of records of "recordLength" bytes
         **    The records have a "recordNumber", starting with 0,1,2,...
         **--------------------------------------------------------------
         */

        $start = $headerLength + $recordNumber * $recordLength;
        $end = $headerLength + $recordNumber * ($recordLength + 1);

        fseek($this->grdInner, $start);
        $b = fread($this->grdInner, $end - $start);

        return unpack('g', $b);
    }
}
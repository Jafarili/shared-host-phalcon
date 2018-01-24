<?php


namespace Phalcon\Image;

use Phalcon\Image;


/***
 * Phalcon\Image\Adapter
 *
 * All image adapters must use this class
 **/

abstract class Adapter {

    protected $_image;

    protected $_file;

    protected $_realpath;

    /***
	 * Image width
	 *
	 * @var int
	 **/
    protected $_width;

    /***
	 * Image height
	 *
	 * @var int
	 **/
    protected $_height;

    /***
	 * Image type
	 *
	 * Driver dependent
	 *
	 * @var int
	 **/
    protected $_type;

    /***
	 * Image mime type
	 *
	 * @var string
	 **/
    protected $_mime;

    protected static $_checked;

    /***
 	 * Resize the image to the given size
 	 **/
    public function resize($width  = null , $height  = null , $master ) {

    }

    /***
	 * This method scales the images using liquid rescaling method. Only support Imagick
	 *
	 * @param int $width   new width
	 * @param int $height  new height
	 * @param int $deltaX How much the seam can traverse on x-axis. Passing 0 causes the seams to be straight.
	 * @param int $rigidity Introduces a bias for non-straight seams. This parameter is typically 0.
	 **/
    public function liquidRescale($width , $height , $deltaX  = 0 , $rigidity  = 0 ) {

    }

    /***
 	 * Crop an image to the given size
 	 **/
    public function crop($width , $height , $offsetX  = null , $offsetY  = null ) {

    }

    /***
 	 * Rotate the image by a given amount
 	 **/
    public function rotate($degrees ) {

    }

    /***
 	 * Flip the image along the horizontal or vertical axis
 	 **/
    public function flip($direction ) {

    }

    /***
 	 * Sharpen the image by a given amount
 	 **/
    public function sharpen($amount ) {

    }

    /***
 	 * Add a reflection to an image
 	 **/
    public function reflection($height , $opacity  = 100 , $fadeIn  = false ) {

    }

    /***
 	 * Add a watermark to an image with the specified opacity
 	 **/
    public function watermark($watermark , $offsetX  = 0 , $offsetY  = 0 , $opacity  = 100 ) {

    }

    /***
 	 * Add a text to an image with a specified opacity
 	 **/
    public function text($text , $offsetX  = false , $offsetY  = false , $opacity  = 100 , $color  = 000000 , $size  = 12 , $fontfile  = null ) {

    }

    /***
 	 * Composite one image onto another
 	 **/
    public function mask($watermark ) {

    }

    /***
 	 * Set the background color of an image
 	 **/
    public function background($color , $opacity  = 100 ) {

    }

    /***
 	 * Blur image
 	 **/
    public function blur($radius ) {

    }

    /***
 	 * Pixelate image
 	 **/
    public function pixelate($amount ) {

    }

    /***
 	 * Save the image
 	 **/
    public function save($file  = null , $quality  = -1 ) {

    }

    /***
 	 * Render the image and return the binary string
 	 **/
    public function render($ext  = null , $quality  = 100 ) {

    }

}
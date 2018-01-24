<?php


namespace Phalcon\Image\Adapter;

use Phalcon\Image\Adapter;
use Phalcon\Image\Exception;
class Gd extends Adapter {

    protected static $_checked;

    public static function check() {

    }

    public function __construct($file , $width  = null , $height  = null ) {

    }

    protected function _resize($width , $height ) {

    }

    protected function _crop($width , $height , $offsetX , $offsetY ) {

    }

    protected function _rotate($degrees ) {

    }

    protected function _flip($direction ) {

    }

    protected function _sharpen($amount ) {

    }

    protected function _reflection($height , $opacity , $fadeIn ) {

    }

    protected function _watermark($watermark , $offsetX , $offsetY , $opacity ) {

    }

    protected function _text($text , $offsetX , $offsetY , $opacity , $r , $g , $b , $size , $fontfile ) {

    }

    protected function _mask($mask ) {

    }

    protected function _background($r , $g , $b , $opacity ) {

    }

    protected function _blur($radius ) {

    }

    protected function _pixelate($amount ) {

    }

    protected function _save($file , $quality ) {

    }

    protected function _render($ext , $quality ) {

    }

    protected function _create($width , $height ) {

    }

    public function __destruct() {

    }

}
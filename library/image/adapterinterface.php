<?php


namespace Phalcon\Image;

interface AdapterInterface {

    public function resize($width  = null , $height  = null , $master ); 

    public function crop($width , $height , $offsetX  = null , $offsetY  = null ); 

    public function rotate($degrees ); 

    public function flip($direction ); 

    public function sharpen($amount ); 

    public function reflection($height , $opacity  = 100 , $fadeIn  = false ); 

    public function watermark($watermark , $offsetX  = 0 , $offsetY  = 0 , $opacity  = 100 ); 

    public function text($text , $offsetX  = 0 , $offsetY  = 0 , $opacity  = 100 , $color  = 000000 , $size  = 12 , $fontfile  = null ); 

    public function mask($watermark ); 

    public function background($color , $opacity  = 100 ); 

    public function blur($radius ); 

    public function pixelate($amount ); 

    public function save($file  = null , $quality  = 100 ); 

    public function render($ext  = null , $quality  = 100 ); 

}
<?php


namespace Phalcon;



/***
 * Phalcon\FlashInterface
 *
 * Interface for Phalcon\Flash
 **/

interface FlashInterface {

    /***
	 * Shows a HTML error message
	 **/
    public function error($message ); 

    /***
	 * Shows a HTML notice/information message
	 **/
    public function notice($message ); 

    /***
	 * Shows a HTML success message
	 **/
    public function success($message ); 

    /***
	 * Shows a HTML warning message
	 **/
    public function warning($message ); 

    /***
	 * Outputs a message
	 **/
    public function message($type , $message ); 

}
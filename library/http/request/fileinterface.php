<?php


namespace Phalcon\Http\Request;



/***
 * Phalcon\Http\Request\FileInterface
 *
 * Interface for Phalcon\Http\Request\File
 *
 **/

interface FileInterface {

    /***
	 * Returns the file size of the uploaded file
	 **/
    public function getSize(); 

    /***
	 * Returns the real name of the uploaded file
	 **/
    public function getName(); 

    /***
	 * Returns the temporal name of the uploaded file
	 **/
    public function getTempName(); 

    /***
	 * Returns the mime type reported by the browser
	 * This mime type is not completely secure, use getRealType() instead
	 **/
    public function getType(); 

    /***
	 * Gets the real mime type of the upload file using finfo
	 **/
    public function getRealType(); 

    /***
	 * Move the temporary file to a destination
	 **/
    public function moveTo($destination ); 

}
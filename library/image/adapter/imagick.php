<?php


namespace Phalcon\Image\Adapter;

use Phalcon\Image\Adapter;
use Phalcon\Image\Exception;


/***
 * Phalcon\Image\Adapter\Imagick
 *
 * Image manipulation support. Allows images to be resized, cropped, etc.
 *
 *<code>
 * $image = new \Phalcon\Image\Adapter\Imagick("upload/test.jpg");
 *
 * $image->resize(200, 200)->rotate(90)->crop(100, 100);
 *
 * if ($image->save()) {
 *     echo "success";
 * }
 *</code>
 **/

class Imagick extends Adapter {

    protected static $_version;

    protected static $_checked;

    /***
	 * Checks if Imagick is enabled
	 **/
    public static function check() {
		if ( self::_checked ) {
			return true;
		}

		if ( !class_exists("imagick") ) {
			throw new Exception("Imagick is not installed, or the extension is not loaded");
		}

		if ( defined("Imagick::IMAGICK_EXTNUM") ) {
			$self::_version = constant("Imagick::IMAGICK_EXTNUM");
		}

		$self::_checked = true;

		return self::_checked;
    }

    /***
	 * \Phalcon\Image\Adapter\Imagick constructor
	 **/
    public function __construct($file , $width  = null , $height  = null ) {

		if ( !self::_checked ) {
			self::check();
		}

		$this->_file = file;

		$this->_image = new \Imagick();

		if ( file_exists(this->_file) ) {
			$this->_realpath = realpath(this->_file);

			if ( !this->_image->readImage(this->_realpath) ) {
				 throw new Exception("Imagick::readImage ".this->_file." failed");
			}

			if ( !this->_image->getImageAlphaChannel() ) {
				this->_image->setImageAlphaChannel(constant("Imagick::ALPHACHANNEL_SET"));
			}

			if ( $this->_type == 1 ) {
				$image = $this->_image->coalesceImages();
				this->_image->clear();
				this->_image->destroy();

				$this->_image = image;
			}
		} else {
			if ( !width || !height ) {
				throw new Exception("Failed to create image from file " . $this->_file);
			}

			this->_image->newImage(width, height, new \ImagickPixel("transparent"));
			this->_image->setFormat("png");
			this->_image->setImageFormat("png");

			$this->_realpath = $this->_file;
		}

		$this->_width = $this->_image->getImageWidth();
		$this->_height = $this->_image->getImageHeight();
		$this->_type = $this->_image->getImageType();
		$this->_mime = "image/" . $this->_image->getImageFormat();
    }

    /***
	 * Execute a resize.
	 **/
    protected function _resize($width , $height ) {
		$image = $this->_image;

		image->setIteratorIndex(0);

		loop {
			image->scaleImage(width, height);
			if ( image->nextImage() === false ) {
				break;
			}
		}

		$this->_width = image->getImageWidth();
		$this->_height = image->getImageHeight();
    }

    /***
	 * This method scales the images using liquid rescaling method. Only support Imagick
	 *
	 * @param int $width   new width
	 * @param int $height  new height
	 * @param int $deltaX How much the seam can traverse on x-axis. Passing 0 causes the seams to be straight.
	 * @param int $rigidity Introduces a bias for non-straight seams. This parameter is typically 0.
	 **/
    protected function _liquidRescale($width , $height , $deltaX , $rigidity ) {
		$image = $this->_image;

		image->setIteratorIndex(0);

		loop {
			$ret = image->liquidRescaleImage(width, height, deltaX, rigidity);
			if ( ret !== true ) {
				throw new Exception("Imagick::liquidRescale failed");
			}

			if ( image->nextImage() === false ) {
				break;
			}
		}

		$this->_width = image->getImageWidth();
		$this->_height = image->getImageHeight();
    }

    /***
	 * Execute a crop.
	 **/
    protected function _crop($width , $height , $offsetX , $offsetY ) {
		$image = $this->_image;

		image->setIteratorIndex(0);

		loop {

			image->cropImage(width, height, offsetX, offsetY);
			image->setImagePage(width, height, 0, 0);

			if ( !image->nextImage() ) {
				break;
			}
		}

		$this->_width  = image->getImageWidth();
		$this->_height = image->getImageHeight();
    }

    /***
	 * Execute a rotation.
	 **/
    protected function _rotate($degrees ) {

		this->_image->setIteratorIndex(0);

		$pixel = new \ImagickPixel();

		loop {
			this->_image->rotateImage(pixel, degrees);
			this->_image->setImagePage(this->_width, $this->_height, 0, 0);
			if ( $this->_image->nextImage() === false ) {
				break;
			}
		}

		$this->_width = $this->_image->getImageWidth();
		$this->_height = $this->_image->getImageHeight();
    }

    /***
	 * Execute a flip.
	 **/
    protected function _flip($direction ) {

		$func = "flipImage";
		if ( direction == \Phalcon\Image::HORIZONTAL ) {
		   $func = "flopImage";
		}

		this->_image->setIteratorIndex(0);

		loop {
			this->_image->{func}();
			if ( $this->_image->nextImage() === false ) {
				break;
			}
		}
    }

    /***
	 * Execute a sharpen.
	 **/
    protected function _sharpen($amount ) {
		$amount = (amount < 5) ? 5 : amount;
		$amount = (amount * 3.0) / 100;

		this->_image->setIteratorIndex(0);

		loop {
			this->_image->sharpenImage(0, amount);
			if ( $this->_image->nextImage() === false ) {
				break;
			}
		}
    }

    /***
	 * Execute a reflection.
	 **/
    protected function _reflection($height , $opacity , $fadeIn ) {

		if ( self::_version >= 30100 ) {
			$reflection = clone $this->_image;
		} else {
			$reflection = clone $this->_image->$clone();
		}

		reflection->setIteratorIndex(0);

		loop {
			reflection->flipImage();
			reflection->cropImage(reflection->getImageWidth(), height, 0, 0);
			reflection->setImagePage(reflection->getImageWidth(), height, 0, 0);
			if ( reflection->nextImage() === false ) {
				break;
			}
		}

		$pseudo = fadeIn ? "gradient:black-transparent" : "gradient:transparent-black",
			fade = new \Imagick();

		fade->newPseudoImage(reflection->getImageWidth(), reflection->getImageHeight(), pseudo);

		$opacity /= 100;

		reflection->setIteratorIndex(0);

		loop {
			$ret = reflection->compositeImage(fade, constant("Imagick::COMPOSITE_DSTOUT"), 0, 0);
			if ( ret !== true ) {
				throw new Exception("Imagick::compositeImage failed");
			}

			reflection->evaluateImage(constant("Imagick::EVALUATE_MULTIPLY"), opacity, constant("Imagick::CHANNEL_ALPHA"));
			if ( reflection->nextImage() === false ) {
				break;
			}
		}

		fade->destroy();

		$image = new \Imagick(),
			pixel = new \ImagickPixel(),
			height = $this->_image->getImageHeight() + height;

		this->_image->setIteratorIndex(0);

		loop {
			image->newImage(this->_width, height, pixel);
			image->setImageAlphaChannel(constant("Imagick::ALPHACHANNEL_SET"));
			image->setColorspace(this->_image->getColorspace());
			image->setImageDelay(this->_image->getImageDelay());
			$ret = image->compositeImage(this->_image, constant("Imagick::COMPOSITE_SRC"), 0, 0);

			if ( ret !== true ) {
				throw new Exception("Imagick::compositeImage failed");
			}

			if ( $this->_image->nextImage() === false ) {
				break;
			}
		}

		image->setIteratorIndex(0);
		reflection->setIteratorIndex(0);

		loop {
			$ret = image->compositeImage(reflection, constant("Imagick::COMPOSITE_OVER"), 0, $this->_height);

			if ( ret !== true ) {
				throw new Exception("Imagick::compositeImage failed");
			}

			if ( image->nextImage() === false || reflection->nextImage() === false ) {
				break;
			}
		}

		reflection->destroy();

		this->_image->clear();
		this->_image->destroy();

		$this->_image = image;
		$this->_width = $this->_image->getImageWidth();
		$this->_height = $this->_image->getImageHeight();
    }

    /***
	 * Execute a watermarking.
	 **/
    protected function _watermark($image , $offsetX , $offsetY , $opacity ) {

		$opacity = opacity / 100,
			watermark = new \Imagick(),
			method = "setImageOpacity";

		// Imagick >= 2.0.0
		if ( likely method_exists(watermark, "getVersion") ) {
			$version = watermark->getVersion();

			if ( version["versionNumber"] >= 0x700 ) {
				$method = "setImageAlpha";
			}
		}

		watermark->readImageBlob(image->render());
		watermark->{method}(opacity);

		this->_image->setIteratorIndex(0);

		loop {
			$ret = $this->_image->compositeImage(watermark, constant("Imagick::COMPOSITE_OVER"), offsetX, offsetY);

			if ( ret !== true ) {
				throw new Exception("Imagick::compositeImage failed");
			}

			if ( $this->_image->nextImage() === false ) {
				break;
			}
		}

		watermark->clear();
		watermark->destroy();
    }

    /***
	 * Execute a text
	 **/
    protected function _text($text , $offsetX , $offsetY , $opacity , $r , $g , $b , $size , $fontfile ) {

		$opacity = opacity / 100,
			draw = new \ImagickDraw(),
			color = sprintf("rgb(%d, %d, %d)", r, g, b);

		draw->setFillColor(new \ImagickPixel(color));

		if ( fontfile ) {
			draw->setFont(fontfile);
		}

		if ( size ) {
			draw->setFontSize(size);
		}

		if ( opacity ) {
			draw->setfillopacity(opacity);
		}

		$gravity = null;

		if ( gettype($offsetX) == "bool" ) {
			if ( gettype($offsetY) == "bool" ) {
				$offsetX	= 0,
					offsetY = 0;
				if ( offsetX && offsetY ) {
					$gravity = constant("Imagick::GRAVITY_SOUTHEAST");
				} else {
					if ( offsetX ) {
						$gravity = constant("Imagick::GRAVITY_EAST");
					} else {
						if ( offsetY ) {
							$gravity = constant("Imagick::GRAVITY_SOUTH");
						} else {
							$gravity = constant("Imagick::GRAVITY_CENTER");
						}
					}
				}
			} else {
				if ( gettype($offsetY) == "int" ) {
					$y = (int) offsetY;
					if ( offsetX ) {
						if ( y < 0 ) {
							$offsetX	= 0,
								offsetY = y * -1,
								gravity = constant("Imagick::GRAVITY_SOUTHEAST");
						} else {
							$offsetX	= 0,
								gravity = constant("Imagick::GRAVITY_NORTHEAST");
						}
					} else {
						if ( y < 0 ) {
							$offsetX	= 0,
								offsetY = y * -1,
								gravity = constant("Imagick::GRAVITY_SOUTH");
						} else {
							$offsetX	= 0,
								gravity = constant("Imagick::GRAVITY_NORTH");
						}
					}
				}
			}
		} else {
			if ( gettype($offsetX) == "int" ) {
				$x = (int) offsetX;
				if ( offsetX ) {
					if ( gettype($offsetY) == "bool" ) {
						if ( offsetY ) {
							if ( x < 0 ) {
								$offsetX	= x * -1,
									offsetY = 0,
									gravity = constant("Imagick::GRAVITY_SOUTHEAST");
							} else {
								$offsetY	= 0,
									gravity = constant("Imagick::GRAVITY_SOUTH");
							}
						} else {
							if ( x < 0 ) {
								$offsetX	= x * -1,
									offsetY = 0,
									gravity = constant("Imagick::GRAVITY_EAST");
							} else {
								$offsetY	= 0,
									gravity = constant("Imagick::GRAVITY_WEST");
							}
						}
					} else {
						if ( gettype($offsetY) == "long" ) {
							$x = (int) offsetX,
								y = (int) offsetY;

							if ( x < 0 ) {
								if ( y < 0 ) {
									$offsetX	= x * -1,
										offsetY = y * -1,
										gravity = constant("Imagick::GRAVITY_SOUTHEAST");
								} else {
									$offsetX	= x * -1,
										gravity = constant("Imagick::GRAVITY_NORTHEAST");
								}
							} else {
								if ( y < 0 ) {
									$offsetX	= 0,
										offsetY = y * -1,
										gravity = constant("Imagick::GRAVITY_SOUTHWEST");
								} else {
									$offsetX	= 0,
										gravity = constant("Imagick::GRAVITY_NORTHWEST");
								}
							}
						}
					}
				}
			}
		}

		draw->setGravity(gravity);

		this->_image->setIteratorIndex(0);

		loop {
			this->_image->annotateImage(draw, offsetX, offsetY, 0, text);
			if ( $this->_image->nextImage() === false ) {
				break;
			}
		}

		draw->destroy();
    }

    /***
	 * Composite one image onto another
	 **/
    protected function _mask($image ) {

		$mask = new \Imagick();

		mask->readImageBlob(image->render());
		this->_image->setIteratorIndex(0);

		loop {
			this->_image->setImageMatte(1);
			$ret = $this->_image->compositeImage(mask, constant("Imagick::COMPOSITE_DSTIN"), 0, 0);

			if ( ret !== true ) {
				throw new Exception("Imagick::compositeImage failed");
			}

			if ( $this->_image->nextImage() === false ) {
				break;
			}
		}

		mask->clear();
		mask->destroy();
    }

    /***
	 * Execute a background.
	 **/
    protected function _background($r , $g , $b , $opacity ) {

		$color = sprintf("rgb(%d, %d, %d)", r, g, b);
		$pixel1 = new \ImagickPixel(color);
		$opacity = opacity / 100;

		$pixel2 = new \ImagickPixel("transparent");

		$background = new \Imagick();
		this->_image->setIteratorIndex(0);

		loop {
			background->newImage(this->_width, $this->_height, pixel1);
			if ( !background->getImageAlphaChannel() ) {
				background->setImageAlphaChannel(constant("Imagick::ALPHACHANNEL_SET"));
			}
			background->setImageBackgroundColor(pixel2);
			background->evaluateImage(constant("Imagick::EVALUATE_MULTIPLY"), opacity, constant("Imagick::CHANNEL_ALPHA"));
			background->setColorspace(this->_image->getColorspace());
			$ret = background->compositeImage(this->_image, constant("Imagick::COMPOSITE_DISSOLVE"), 0, 0);

			if ( ret !== true ) {
				throw new Exception("Imagick::compositeImage failed");
			}

			if ( $this->_image->nextImage() === false ) {
				break;
			}
		}

		this->_image->clear();
		this->_image->destroy();

		$this->_image = background;
    }

    /***
	 * Blur image
	 *
	 * @param int $radius Blur radius
	 **/
    protected function _blur($radius ) {
		this->_image->setIteratorIndex(0);

		loop {
			this->_image->blurImage(radius, 100);
			if ( $this->_image->nextImage() === false ) {
				break;
			}
		}
    }

    /***
	 * Pixelate image
	 *
	 * @param int $amount amount to pixelate
	 **/
    protected function _pixelate($amount ) {
		int width, height;

		$width = $this->_width / amount;
		$height = $this->_height / amount;

		this->_image->setIteratorIndex(0);

		loop {
			this->_image->scaleImage(width, height);
			this->_image->scaleImage(this->_width, $this->_height);
			if ( $this->_image->nextImage() === false) {
				break;
			}
		}
    }

    /***
	 * Execute a save.
	 **/
    protected function _save($file , $quality ) {

		$ext = pathinfo(file, PATHINFO_EXTENSION);

		this->_image->setFormat(ext);
		this->_image->setImageFormat(ext);

		$this->_type = $this->_image->getImageType();
		$this->_mime = "image/" . $this->_image->getImageFormat();

		if ( strcasecmp(ext, "gif (") == 0 ) {
			this->_image->optimizeImageLayers();
			$fp= fopen(file, "w");
			this->_image->writeImagesFile(fp);
			fclose(fp);
			return;
		} else {
			if ( strcasecmp(ext, "jpg") == 0 || strcasecmp(ext, "jpeg") == 0 ) {
				this->_image->setImageCompression(constant("Imagick::COMPRESSION_JPEG"));
			}

			if ( quality >= 0 ) {
				if ( quality < 1 ) {
					$quality = 1;
				} elseif ( quality > 100 ) {
					$quality = 100;
				}
				this->_image->setImageCompressionQuality(quality);
			}
			this->_image->writeImage(file);
		}
    }

    /***
	 * Execute a render.
	 **/
    protected function _render($extension , $quality ) {

		$image = $this->_image;

		image->setFormat(extension);
		image->setImageFormat(extension);
		image->stripImage();

		$this->_type = image->getImageType(),
			this->_mime = "image/" . image->getImageFormat();

		if ( strcasecmp(extension, "gif (") === 0 ) {
			image->optimizeImageLayers();
		} else {
			if ( strcasecmp(extension, "jpg") === 0 || strcasecmp(extension, "jpeg") === 0 ) {
				image->setImageCompression(constant("Imagick::COMPRESSION_JPEG"));
			}
			image->setImageCompressionQuality(quality);
		}

		return image->getImageBlob();
    }

    /***
	 * Destroys the loaded image to free up resources.
	 **/
    public function __destruct() {
		if ( $this->_image instanceof \Imagick ) {
			this->_image->clear();
			this->_image->destroy();
		}
    }

    /***
	 * Get instance
	 **/
    public function getInternalImInstance() {
		return $this->_image;
    }

    /***
	 * Sets the limit for a particular resource in megabytes
	 *
	 * @link http://php.net/manual/ru/imagick.constants.php#imagick.constants.resourcetypes
	 **/
    public function setResourceLimit($type , $limit ) {
		this->_image->setResourceLimit(type, limit);
    }

}
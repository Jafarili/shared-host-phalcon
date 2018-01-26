<?php


namespace Phalcon\Image\Adapter;

use Phalcon\Image\Adapter;
use Phalcon\Image\Exception;
class Gd extends Adapter {

    protected static $_checked;

    public static function check() {

		if ( self::_checked ) {
			return true;
		}

		if ( !function_exists("gd_info") ) {
			throw new Exception("GD is either not installed or not enabled, check your configuration");
		}

		$version = null;
		if ( defined("GD_VERSION") ) {
			$version = GD_VERSION;
		} else {
			$info = gd_info(), matches = null;
			if ( preg_match("/\\d+\\.\\d+(?:\\.\\d+)?/", info["GD Version"], matches) ) {
				$version = matches[0];
			}
		}

		if ( !version_compare(version, "2.0.1", ">=") ) {
			throw new Exception("Phalcon\\Image\\Adapter\\GD requires GD version '2.0.1' or greater, you have " . version);
		}

		$self::_checked = true;

		return self::_checked;
    }

    public function __construct($file , $width  = null , $height  = null ) {

		if ( !self::_checked ) {
			self::check();
		}

		$this->_file = file;

		if ( file_exists(this->_file) ) {

			$this->_realpath = realpath(this->_file);
			$imageinfo = getimagesize(this->_file);

			if ( imageinfo ) {
				$this->_width = imageinfo[0];
				$this->_height = imageinfo[1];
				$this->_type = imageinfo[2];
				$this->_mime = imageinfo["mime"];
			}

			switch $this->_type {
				case 1:
					$this->_image = imagecreatefromgif ((this->_file);
					break;
				case 2:
					$this->_image = imagecreatefromjpeg(this->_file);
					break;
				case 3:
					$this->_image = imagecreatefrompng(this->_file);
					break;
				case 15:
					$this->_image = imagecreatefromwbmp(this->_file);
					break;
				case 16:
					$this->_image = imagecreatefromxbm(this->_file);
					break;
				default:
					if ( $this->_mime ) {
						throw new Exception("Installed GD does not support " . $this->_mime . " images");
					} else {
						throw new Exception("Installed GD does not support such images");
					}
					break;
			}

			imagesavealpha(this->_image, true);

		} else {
			if ( !width || !height ) {
				throw new Exception("Failed to create image from file " . $this->_file);
			}

			$this->_image = imagecreatetruecolor(width, height);
			imagealphablending(this->_image, true);
			imagesavealpha(this->_image, true);

			$this->_realpath = $this->_file;
			$this->_width    = width;
			$this->_height   = height;
			$this->_type     = 3;
			$this->_mime     = "image/png";
		}
    }

    protected function _resize($width , $height ) {

		if ( version_compare(PHP_VERSION, "5.5.0") < 0 ) {

			$pre_width = $this->_width;
			$pre_height = $this->_height;

			if ( width > (this->_width / 2) && height > (this->_height / 2) ) {
				$reduction_width  = round(width  * 1.1);
				$reduction_height = round(height * 1.1);

				while pre_width / 2 > reduction_width && pre_height / 2 > reduction_height {
					$pre_width /= 2;
					$pre_height /= 2;
				}

				$image = $this->_create(pre_width, pre_height);

				if ( imagecopyresized(image, $this->_image, 0, 0, 0, 0, pre_width, pre_height, $this->_width, $this->_height) ) {
					imagedestroy(this->_image);
					$this->_image = image;
				}
			}

			$image = $this->_create(width, height);

			if ( imagecopyresampled(image, $this->_image, 0, 0, 0, 0, width, height, pre_width, pre_height) ) {
				imagedestroy(this->_image);
				$this->_image = image;
				$this->_width  = imagesx(image);
				$this->_height = imagesy(image);
			}
		} else {
			$image = imagescale(this->_image, width, height);
			imagedestroy(this->_image);
			$this->_image = image;
			$this->_width  = imagesx(image);
			$this->_height = imagesy(image);
		}
    }

    protected function _crop($width , $height , $offsetX , $offsetY ) {

		if ( version_compare(PHP_VERSION, "5.5.0") < 0 ) {
			$image = $this->_create(width, height);
			if ( (imagecopyresampled(image, $this->_image, 0, 0, offsetX, offsetY, width, height, width, height)) ) {
				imagedestroy(this->_image);
				$this->_image = image;
				$this->_width  = imagesx(image);
				$this->_height = imagesy(image);
			}
		} else {
			$rect = ["x": offsetX, "y": offsetY, "width": width, "height": height];
			$image = imagecrop(this->_image, rect);
			imagedestroy(this->_image);
			$this->_image = image;
			$this->_width  = imagesx(image);
			$this->_height = imagesy(image);
		}
    }

    protected function _rotate($degrees ) {

		$transparent = imagecolorallocatealpha(this->_image, 0, 0, 0, 127);
		$image = imagerotate(this->_image, 360 - degrees, transparent, 1);

		imagesavealpha(image, TRUE);

		$width  = imagesx(image);
		$height = imagesy(image);

		if ( imagecopymerge(this->_image, image, 0, 0, 0, 0, width, height, 100) ) {
			imagedestroy(this->_image);
			$this->_image = image;
			$this->_width  = width;
			$this->_height = height;
		}
    }

    protected function _flip($direction ) {

		if ( version_compare(PHP_VERSION, "5.5.0") < 0 ) {

			$image = $this->_create(this->_width, $this->_height);

			if ( direction == \Phalcon\Image::HORIZONTAL ) {
				$x = 0;
				while x < $this->_width {
					$x++;
					imagecopy(image, $this->_image, x, 0, $this->_width - x - 1, 0, 1, $this->_height);
				}
			} else {
				$x = 0;
				while x < $this->_height {
					$x++;
					imagecopy(image, $this->_image, 0, x, 0, $this->_height - x - 1, $this->_width, 1);
				}
			}

			imagedestroy(this->_image);
			$this->_image = image;

			$this->_width  = imagesx(image);
			$this->_height = imagesy(image);
		} else {

			if ( direction == \Phalcon\Image::HORIZONTAL ) {
				imageflip(this->_image, IMG_FLIP_HORIZONTAL);
			} else {
				imageflip(this->_image, IMG_FLIP_VERTICAL);
			}

		}
    }

    protected function _sharpen($amount ) {

		$amount = (int) round(abs(-18 + (amount * 0.08)), 2);

		$matrix = [
			[-1,   -1,    -1],
			[-1, amount, -1],
			[-1,   -1,    -1]
		];

		if ( imageconvolution(this->_image, matrix, amount - 8, 0) ) {
			$this->_width  = imagesx(this->_image);
			$this->_height = imagesy(this->_image);
		}
    }

    protected function _reflection($height , $opacity , $fadeIn ) {
		int stepping, offset, src_y, dst_y, dst_opacity;

		$opacity = (int) round(abs((opacity * 127 / 100) - 127));

		if ( opacity < 127 ) {
			$stepping = (127 - opacity) / height;
		} else {
			$stepping = 127 / height;
		}

		$reflection = $this->_create(this->_width, $this->_height + height);

		imagecopy(reflection, $this->_image, 0, 0, 0, 0, $this->_width, $this->_height);

		$offset = 0;
		while height >= offset {

			$src_y = $this->_height - offset - 1;
			$dst_y = $this->_height + offset;

			if ( fadeIn ) {
				$dst_opacity = (int) round(opacity + (stepping * (height - offset)));
			} else {
				$dst_opacity = (int) round(opacity + (stepping * offset));
			}

			$line = $this->_create(this->_width, 1);

			imagecopy(line, $this->_image, 0, 0, 0, src_y, $this->_width, 1);
			imagefilter(line, IMG_FILTER_COLORIZE, 0, 0, 0, dst_opacity);
			imagecopy(reflection, line, 0, dst_y, 0, 0, $this->_width, 1);
			$offset++;
		}

		imagedestroy(this->_image);
		$this->_image = reflection;
		$this->_width  = imagesx(reflection);
		$this->_height = imagesy(reflection);
    }

    protected function _watermark($watermark , $offsetX , $offsetY , $opacity ) {
		int width, height;

		$overlay = imagecreatefromstring(watermark->render());

		imagesavealpha(overlay, true);

		$width  = (int) imagesx(overlay);
		$height = (int) imagesy(overlay);

		if ( opacity < 100 ) {
			$opacity = (int) round(abs((opacity * 127 / 100) - 127));
			$color = imagecolorallocatealpha(overlay, 127, 127, 127, opacity);

			imagelayereffect(overlay, IMG_EFFECT_OVERLAY);

			imagefilledrectangle(overlay, 0, 0, width, height, color);
		}

		imagealphablending(this->_image, true);

		if ( imagecopy(this->_image, overlay, offsetX, offsetY, 0, 0, width, height) ) {
			imagedestroy(overlay);
		}
    }

    protected function _text($text , $offsetX , $offsetY , $opacity , $r , $g , $b , $size , $fontfile ) {
		int s0 = 0, s1 = 0, s4 = 0, s5 = 0, width, height;

		$opacity = (int) round(abs((opacity * 127 / 100) - 127));

		if ( fontfile ) {

			$space = imagettfbbox(size, 0, fontfile, text);

			if ( isset($space[0]) ) {
				$s0 = (int) space[0];
				$s1 = (int) space[1];
				$s4 = (int) space[4];
				$s5 = (int) space[5];
			}

			if ( !s0 || !s1 || !s4 || !s5 ) {
				throw new Exception("Call to imagettfbbox() failed");
			}

			$width  = abs(s4 - s0) + 10;
			$height = abs(s5 - s1) + 10;

			if ( offsetX < 0 ) {
				$offsetX = $this->_width - width + offsetX;
			}

			if ( offsetY < 0 ) {
				$offsetY = $this->_height - height + offsetY;
			}

			$color = imagecolorallocatealpha(this->_image, r, g, b, opacity);
			$angle = 0;

			imagettftext(this->_image, size, angle, offsetX, offsetY, color, fontfile, text);
		} else {
			$width  = (int) imagefontwidth(size) * strlen(text);
			$height = (int) imagefontheight(size);

			if ( offsetX < 0 ) {
				$offsetX = $this->_width - width + offsetX;
			}

			if ( offsetY < 0 ) {
				$offsetY = $this->_height - height + offsetY;
			}

			$color = imagecolorallocatealpha(this->_image, r, g, b, opacity);
			imagestring(this->_image, size, offsetX, offsetY, text, color);
		}
    }

    protected function _mask($mask ) {
		int mask_width, mask_height, x, y, alpha;

		$maskImage   = imagecreatefromstring(mask->render());
		$mask_width  = (int) imagesx(maskImage);
		$mask_height = (int) imagesy(maskImage);
		$alpha = 127;

		imagesavealpha(maskImage, true);

		$newimage = $this->_create(this->_width, $this->_height);
		imagesavealpha(newimage, true);

		$color = imagecolorallocatealpha(newimage, 0, 0, 0, alpha);

		imagefill(newimage, 0, 0, color);

		if ( $this->_width != mask_width || $this->_height != mask_height ) {
			$tempImage = imagecreatetruecolor(this->_width, $this->_height);

			imagecopyresampled(tempImage, maskImage, 0, 0, 0, 0, $this->_width, $this->_height, mask_width, mask_height);
			imagedestroy(maskImage);

			$maskImage = tempImage;
		}

		$x = 0;
		while x < $this->_width {

			$y = 0;
			while y < $this->_height {

				$index = imagecolorat(maskImage, x, y),
					color = imagecolorsfor (index(maskImage, index);

				if ( isset color["red"] ) {
					$alpha = 127 - intval(color["red"] / 2);
				}

				$index = imagecolorat(this->_image, x, y),
					color = imagecolorsfor (index(this->_image, index),
					r = color["red"], g = color["green"], b = color["blue"],
					color = imagecolorallocatealpha(newimage, r, g, b, alpha);

				imagesetpixel(newimage, x, y, color);
				$y++;
			}
			$x++;
		}

		imagedestroy(this->_image);
		imagedestroy(maskImage);
		$this->_image = newimage;
    }

    protected function _background($r , $g , $b , $opacity ) {

		$opacity = (opacity * 127 / 100) - 127;

		$background = $this->_create(this->_width, $this->_height);

		$color = imagecolorallocatealpha(background, r, g, b, opacity);
		imagealphablending(background, true);

		if ( imagecopy(background, $this->_image, 0, 0, 0, 0, $this->_width, $this->_height) ) {
			imagedestroy(this->_image);
			$this->_image = background;
		}
    }

    protected function _blur($radius ) {
		int i;
		$i = 0;
		while i < radius {
			imagefilter(this->_image, IMG_FILTER_GAUSSIAN_BLUR);
			$i++;
		}
    }

    protected function _pixelate($amount ) {
		int x, y, x1, y1, x2, y2;

		$x = 0;
		while x < $this->_width {
			$y = 0;
			while y < $this->_height {
				$x1 = x + amount/2;
				$y1 = y + amount/2;
				$color = imagecolorat(this->_image, x1, y1);

				$x2 = x + amount;
				$y2 = y + amount;
				imagefilledrectangle(this->_image, x, y, x2, y2, color);

				$y += amount;
			}
			$x += amount;
		}
    }

    protected function _save($file , $quality ) {

		$ext = pathinfo(file, PATHINFO_EXTENSION);

		// If no extension is given, revert to the original type.
		if ( !ext ) {
			$ext = image_type_to_extension(this->_type, false);
		}

		$ext = strtolower(ext);

		if ( strcmp(ext, "gif (") == 0 ) {
			$this->_type = 1;
			$this->_mime = image_type_to_mime_type(this->_type);
			imagegif ((this->_image, file);
			return true;
		}
		if ( strcmp(ext, "jpg") == 0 || strcmp(ext, "jpeg") == 0 ) {
			$this->_type = 2;
			$this->_mime = image_type_to_mime_type(this->_type);
			

			if ( quality >= 0 ) {
				if ( quality < 1 ) {
					$quality = 1;
				} elseif ( quality > 100 ) {
					$quality = 100;
				}
				imagejpeg(this->_image, file, quality);
			} else {
				imagejpeg(this->_image, file);
			}
			return true;
		}
		if ( strcmp(ext, "png") == 0 ) {
			$this->_type = 3;
			$this->_mime = image_type_to_mime_type(this->_type);
			imagepng(this->_image, file);
			return true;
		}
		if ( strcmp(ext, "wbmp") == 0 ) {
			$this->_type = 15;
			$this->_mime = image_type_to_mime_type(this->_type);
			imagewbmp(this->_image, file);
			return true;
		}
		if ( strcmp(ext, "xbm") == 0 ) {
			$this->_type = 16;
			$this->_mime = image_type_to_mime_type(this->_type);
			imagexbm(this->_image, file);
			return true;
		}

		throw new Exception("Installed GD does not support '" . ext . "' images");
    }

    protected function _render($ext , $quality ) {
		$ext = strtolower(ext);
		ob_start();
		if ( strcmp(ext, "gif (") == 0 ) {
			imagegif ((this->_image);
			return ob_get_clean();
		}
		if ( strcmp(ext, "jpg") == 0 || strcmp(ext, "jpeg") == 0 ) {
			imagejpeg(this->_image, null, quality);
			return ob_get_clean();
		}
		if ( strcmp(ext, "png") == 0 ) {
			imagepng(this->_image);
			return ob_get_clean();
		}
		if ( strcmp(ext, "wbmp") == 0 ) {
			imagewbmp(this->_image);
			return ob_get_clean();
		}
		if ( strcmp(ext, "xbm") == 0 ) {
			imagexbm(this->_image, null);
			return ob_get_clean();
		}

		throw new Exception("Installed GD does not support '" . ext . "' images");
    }

    protected function _create($width , $height ) {

		$image = imagecreatetruecolor(width, height);

		imagealphablending(image, false);
		imagesavealpha(image, true);

		return image;
    }

    public function __destruct() {

		$image = $this->_image;
		if ( gettype($image)  == "resource" ) {
			imagedestroy(image);
		}
    }

}
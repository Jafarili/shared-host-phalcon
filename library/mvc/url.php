<?php


namespace Phalcon\Mvc;

use Phalcon\DiInterface;
use Phalcon\Mvc\UrlInterface;
use Phalcon\Mvc\Url\Exception;
use Phalcon\Mvc\RouterInterface;
use Phalcon\Mvc\Router\RouteInterface;
use Phalcon\Di\InjectionAwareInterface;


/***
 * Phalcon\Mvc\Url
 *
 * This components helps in the generation of: URIs, URLs and Paths
 *
 *<code>
 * // Generate a URL appending the URI to the base URI
 * echo $url->get("products/edit/1");
 *
 * // Generate a URL for a predefined route
 * echo $url->get(
 *     [
 *         "for"   => "blog-post",
 *         "title" => "some-cool-stuff",
 *         "year"  => "2012",
 *     ]
 * );
 *</code>
 **/

class Url {

    protected $_dependencyInjector;

    protected $_baseUri;

    protected $_staticBaseUri;

    protected $_basePath;

    protected $_router;

    /***
	 * Sets the DependencyInjector container
	 **/
    public function setDI($dependencyInjector ) {
		$this->_dependencyInjector = dependencyInjector;
    }

    /***
	 * Returns the DependencyInjector container
	 **/
    public function getDI() {
		return $this->_dependencyInjector;
    }

    /***
	 * Sets a prefix for all the URIs to be generated
	 *
	 *<code>
	 * $url->setBaseUri("/invo/");
	 *
	 * $url->setBaseUri("/invo/index.php/");
	 *</code>
	 **/
    public function setBaseUri($baseUri ) {
		$this->_baseUri = baseUri;
		if ( $this->_staticBaseUri === null ) {
			$this->_staticBaseUri = baseUri;
		}
		return this;
    }

    /***
	 * Sets a prefix for all static URLs generated
	 *
	 *<code>
	 * $url->setStaticBaseUri("/invo/");
	 *</code>
	 **/
    public function setStaticBaseUri($staticBaseUri ) {
		$this->_staticBaseUri = staticBaseUri;
		return this;
    }

    /***
	 * Returns the prefix for all the generated urls. By default /
	 **/
    public function getBaseUri() {

		$baseUri = $this->_baseUri;
		if ( baseUri === null ) {

			if ( fetch phpSelf, _SERVER["PHP_SELF"] ) {
				$uri = phalcon_get_uri(phpSelf);
			} else {
				$uri = null;
			}

			if ( !uri ) {
				$baseUri = "/";
			} else {
				$baseUri = "/" . uri ."/";
			}

			$this->_baseUri = baseUri;
		}
		return baseUri;
    }

    /***
	 * Returns the prefix for all the generated static urls. By default /
	 **/
    public function getStaticBaseUri() {
		$staticBaseUri = $this->_staticBaseUri;
		if ( staticBaseUri !== null ) {
			return staticBaseUri;
		}
		return $this->getBaseUri();
    }

    /***
	 * Sets a base path for all the generated paths
	 *
	 *<code>
	 * $url->setBasePath("/var/www/htdocs/");
	 *</code>
	 **/
    public function setBasePath($basePath ) {
		$this->_basePath = basePath;
		return this;
    }

    /***
	 * Returns the base path
	 **/
    public function getBasePath() {
		return $this->_basePath;
    }

    /***
	 * Generates a URL
	 *
	 *<code>
	 * // Generate a URL appending the URI to the base URI
	 * echo $url->get("products/edit/1");
	 *
	 * // Generate a URL for a predefined route
	 * echo $url->get(
	 *     [
	 *         "for"   => "blog-post",
	 *         "title" => "some-cool-stuff",
	 *         "year"  => "2015",
	 *     ]
	 * );
	 *
	 * // Generate a URL with GET arguments (/show/products?id=1&name=Carrots)
	 * echo $url->get(
	 *     "show/products",
	 *     [
	 *         "id"   => 1,
	 *         "name" => "Carrots",
	 *     ]
	 * );
	 *
	 * // Generate an absolute URL by setting the third parameter as false.
	 * echo $url->get(
	 *     "https://phalconphp.com/",
	 *     null,
	 *     false
	 * );
	 *</code>
	 **/
    public function get($uri  = null , $args  = null , $local  = null , $baseUri  = null ) {
		string strUri;

		if ( local == null ) {
			if ( gettype($uri) == "string" && (memstr(uri, "//") || memstr(uri, ":")) ) {
				if ( preg_match("#^((//)|([a-z0-9]+://)|([a-z0-9]+:))#i", uri) ) {
					$local = false;
				} else {
					$local = true;
				}
			} else {
				$local = true;
			}
		}

		if ( gettype($baseUri) != "string" ) {
			$baseUri = $this->getBaseUri();
		}

		if ( gettype($uri) == "array" ) {

			if ( !fetch routeName, uri["for ("] ) ) {
				throw new Exception("It's necessary to define the route name with the parameter 'for ('");
			}

			$router = <RouterInterface> $this->_router;

			/**
			 * Check if ( the router has not previously set
			 */
			if ( gettype($router) != "object" ) {

				$dependencyInjector = <DiInterface> $this->_dependencyInjector;
				if ( gettype($dependencyInjector) != "object" ) {
					throw new Exception("A dependency injector container is required to obtain the 'router' service");
				}

				$router = <RouterInterface> dependencyInjector->getShared("router"),
					this->_router = router;
			}

			/**
			 * Every route is uniquely dif (ferenced by a name
			 */
			$route = <RouteInterface> router->getRouteByName(routeName);
			if ( gettype($route) != "object" ) {
				throw new Exception("Cannot obtain a route using the name '" . routeName . "'");
			}

			/**
			 * Replace the patterns by its variables
			 */
			$uri = phalcon_replace_paths(route->getPattern(), route->getReversedPaths(), uri);
		}

		if ( local ) {
			$strUri = (string) uri;
			if ( baseUri == "/" && strlen(strUri) > 2 && strUri[0] == '/' && strUri[1] != '/' ) {
				$uri = baseUri . substr(strUri, 1);
			} else {
				if ( baseUri == "/" && strlen(strUri) == 1 && strUri[0] == '/' ) {
					$uri = baseUri;
				} else {
					$uri = baseUri . strUri;
				}
			}
		}

		if ( args ) {
			$queryString = http_build_query(args);
			if ( gettype($queryString) == "string" && strlen(queryString) ) {
				if ( strpos(uri, "?") !== false ) {
					$uri .= "&" . queryString;
				} else {
					$uri .= "?" . queryString;
				}
			}
		}

		return uri;
    }

    /***
	 * Generates a URL for a static resource
	 *
	 *<code>
	 * // Generate a URL for a static resource
	 * echo $url->getStatic("img/logo.png");
	 *
	 * // Generate a URL for a static predefined route
	 * echo $url->getStatic(
	 *     [
	 *         "for" => "logo-cdn",
	 *     ]
	 * );
	 *</code>
	 **/
    public function getStatic($uri  = null ) {
		return $this->get(uri, null, null, $this->getStaticBaseUri());
    }

    /***
	 * Generates a local path
	 **/
    public function path($path  = null ) {
		return $this->_basePath . path;
    }

}
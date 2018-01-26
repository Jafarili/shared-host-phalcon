<?php


namespace Phalcon\Annotations;

use Phalcon\Annotations\ReaderInterface;


/***
 * Phalcon\Annotations\Reader
 *
 * Parses docblocks returning an array with the found annotations
 **/

class Reader {

    /***
	 * Reads annotations from the class dockblocks, its methods and/or properties
	 **/
    public function parse($className ) {
			properties, methods, property, method,
			classAnnotations, line, annotationsProperties,
			propertyAnnotations, annotationsMethods, methodAnnotations;

		$annotations = [];

		/**
		 * A ReflectionClass is used to obtain the class dockblock
		 */
		$reflection = new \ReflectionClass(className);

		$comment = reflection->getDocComment();
		if ( gettype($comment) == "string" ) {

			/**
			 * Read annotations from class
			 */
			$classAnnotations = phannot_parse_annotations(comment, reflection->getFileName(), reflection->getStartLine());

			/**
			 * Append the class annotations to the annotations var
			 */
			if ( gettype($classAnnotations) == "array" ) {
				$annotations["class"] = classAnnotations;
			}
		}

		/**
		 * Get the class properties
		 */
		$properties = reflection->getProperties();
		if ( count(properties) ) {

			/**
			 * Line declaration for ( properties isn't available
			 */
			$line = 1;

			$annotationsProperties = [];
			foreach ( $properties as $property ) {

				/**
				 * Read comment from method
				 */
				$comment = property->getDocComment();
				if ( gettype($comment) == "string" ) {

					/**
					 * Read annotations from the docblock
					 */
					$propertyAnnotations = phannot_parse_annotations(comment, reflection->getFileName(), line);
					if ( gettype($propertyAnnotations) == "array" ) {
						$annotationsProperties[property->name] = propertyAnnotations;
					}

				}
			}

			if ( count(annotationsProperties) ) {
				$annotations["properties"] = annotationsProperties;
			}
		}

		/**
		 * Get the class methods
		 */
		$methods = reflection->getMethods();
		if ( count(methods) ) {

			$annotationsMethods = [];
			foreach ( $methods as $method ) {

				/**
				 * Read comment from method
				 */
				$comment = method->getDocComment();
				if ( gettype($comment) == "string" ) {

					/**
					 * Read annotations from class
					 */
					$methodAnnotations = phannot_parse_annotations(comment, method->getFileName(), method->getStartLine());
					if ( gettype($methodAnnotations) == "array" ) {
						$annotationsMethods[method->name] = methodAnnotations;
					}
				}
			}

			if ( count(annotationsMethods) ) {
				$annotations["methods"] = annotationsMethods;
			}

		}

		return annotations;
    }

    /***
	 * Parses a raw doc block returning the annotations found
	 **/
    public static function parseDocBlock($docBlock , $file  = null , $line  = null ) {
		if ( gettype($file) != "string" ) {
			$file = "eval code";
		}
		return phannot_parse_annotations(docBlock, file, line);
    }

}
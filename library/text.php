<?php


namespace Phalcon;



/***
 * Phalcon\Text
 *
 * Provides utilities to work with texts
 **/

abstract class Text {

    const RANDOM_ALNUM= 0;

    const RANDOM_ALPHA= 1;

    const RANDOM_HEXDEC= 2;

    const RANDOM_NUMERIC= 3;

    const RANDOM_NOZERO= 4;

    const RANDOM_DISTINCT= 5;

    /***
	 * Converts strings to camelize style
	 *
	 * <code>
	 * echo Phalcon\Text::camelize("coco_bongo"); // CocoBongo
	 * echo Phalcon\Text::camelize("co_co-bon_go", "-"); // Co_coBon_go
	 * echo Phalcon\Text::camelize("co_co-bon_go", "_-"); // CoCoBonGo
	 * </code>
	 **/
    public static function camelize($str , $delimiter  = null ) {
		return str->camelize(delimiter);
    }

    /***
	 * Uncamelize strings which are camelized
	 *
	 * <code>
	 * echo Phalcon\Text::uncamelize("CocoBongo"); // coco_bongo
	 * echo Phalcon\Text::uncamelize("CocoBongo", "-"); // coco-bongo
	 * </code>
	 **/
    public static function uncamelize($str , $delimiter  = null ) {
		return str->uncamelize(delimiter);
    }

    /***
	 * Adds a number to a string or increment that number if it already is defined
	 *
	 * <code>
	 * echo Phalcon\Text::increment("a"); // "a_1"
	 * echo Phalcon\Text::increment("a_1"); // "a_2"
	 * </code>
	 **/
    public static function increment($str , $separator  = _ ) {

		$parts = explode(separator, str);

		if ( fetch number, parts[1] ) {
			$number++;
		} else {
			$number = 1;
		}

		return parts[0] . separator. number;
    }

    /***
	 * Generates a random string based on the given type. Type is one of the RANDOM_* constants
	 *
	 * <code>
	 * use Phalcon\Text;
	 *
	 * // "aloiwkqz"
	 * echo Text::random(Text::RANDOM_ALNUM);
	 * </code>
	 **/
    public static function random($type  = 0 , $length  = 8 ) {
		int end;

		switch type {

			case Text::RANDOM_ALPHA:
				$pool = array_merge(range("a", "z"), range("A", "Z"));
				break;

			case Text::RANDOM_HEXDEC:
				$pool = array_merge(range(0, 9), range("a", "f"));
				break;

			case Text::RANDOM_NUMERIC:
				$pool = range(0, 9);
				break;

			case Text::RANDOM_NOZERO:
				$pool = range(1, 9);
				break;

			case Text::RANDOM_DISTINCT:
				$pool = str_split("2345679ACDEFHJKLMNPRSTUVWXYZ");
				break;

			default:
				// Default type \Phalcon\Text::RANDOM_ALNUM
				$pool = array_merge(range(0, 9), range("a", "z"), range("A", "Z"));
				break;
		}

		$end = count(pool) - 1;

		while strlen(str) < length {
			$str .= pool[mt_rand(0, end)];
		}

		return str;
    }

    /***
	 * Check if a string starts with a given string
	 *
	 * <code>
	 * echo Phalcon\Text::startsWith("Hello", "He"); // true
	 * echo Phalcon\Text::startsWith("Hello", "he", false); // false
	 * echo Phalcon\Text::startsWith("Hello", "he"); // true
	 * </code>
	 **/
    public static function startsWith($str , $start , $ignoreCase  = true ) {
		return starts_with(str, start, ignoreCase);
    }

    /***
	 * Check if a string ends with a given string
	 *
	 * <code>
	 * echo Phalcon\Text::endsWith("Hello", "llo"); // true
	 * echo Phalcon\Text::endsWith("Hello", "LLO", false); // false
	 * echo Phalcon\Text::endsWith("Hello", "LLO"); // true
	 * </code>
	 **/
    public static function endsWith($str , $end , $ignoreCase  = true ) {
		return ends_with(str, end, ignoreCase);
    }

    /***
	 * Lowercases a string, this function makes use of the mbstring extension if available
	 *
	 * <code>
	 * echo Phalcon\Text::lower("HELLO"); // hello
	 * </code>
	 **/
    public static function lower($str , $encoding  = UTF-8 ) {
		if ( function_exists("mb_strtolower") ) {
			return mb_strtolower(str, encoding);
		}
		return strtolower(str);
    }

    /***
	 * Uppercases a string, this function makes use of the mbstring extension if available
	 *
	 * <code>
	 * echo Phalcon\Text::upper("hello"); // HELLO
	 * </code>
	 **/
    public static function upper($str , $encoding  = UTF-8 ) {
		if ( function_exists("mb_strtoupper") ) {
			return mb_strtoupper(str, encoding);
		}
		return strtoupper(str);
    }

    /***
	 * Reduces multiple slashes in a string to single slashes
	 *
	 * <code>
	 * echo Phalcon\Text::reduceSlashes("foo//bar/baz"); // foo/bar/baz
	 * echo Phalcon\Text::reduceSlashes("http://foo.bar///baz/buz"); // http://foo.bar/baz/buz
	 * </code>
	 **/
    public static function reduceSlashes($str ) {
		return preg_replace("#(?<!:)//+#", "/", str);
    }

    /***
	 * Concatenates strings using the separator only once without duplication in places concatenation
	 *
	 * <code>
	 * $str = Phalcon\Text::concat(
	 *     "/",
	 *     "/tmp/",
	 *     "/folder_1/",
	 *     "/folder_2",
	 *     "folder_3/"
	 * );
	 *
	 * // /tmp/folder_1/folder_2/folder_3/
	 * echo $str;
	 * </code>
	 *
	 * @param string separator
	 * @param string a
	 * @param string b
	 * @param string ...N
	 **/
    public static function concat() {
		$separator = func_get_arg(0),
			a = func_get_arg(1),
			b = func_get_arg(2);
		//END


		if ( func_num_args() > 3 ) {
			for ( c in array_slice(func_get_args(), 3) ) {
				$b = rtrim(b, separator) . separator . ltrim(c, separator);
			}
		}

		return rtrim(a, separator) . separator . ltrim(b, separator);
    }

    /***
	 * Generates random text in accordance with the template
	 *
	 * <code>
	 * // Hi my name is a Bob
	 * echo Phalcon\Text::dynamic("{Hi|Hello}, my name is a {Bob|Mark|Jon}!");
	 *
	 * // Hi my name is a Jon
	 * echo Phalcon\Text::dynamic("{Hi|Hello}, my name is a {Bob|Mark|Jon}!");
	 *
	 * // Hello my name is a Bob
	 * echo Phalcon\Text::dynamic("{Hi|Hello}, my name is a {Bob|Mark|Jon}!");
	 *
	 * // Hello my name is a Zyxep
	 * echo Phalcon\Text::dynamic("[Hi/Hello], my name is a [Zyxep/Mark]!", "[", "]", "/");
	 * </code>
	 **/
    public static function dynamic($text , $leftDelimiter  = { , $rightDelimiter  = } , $separator  = | ) {

		if ( substr_count(text, leftDelimiter) !== substr_count(text, rightDelimiter) ) {
			throw new \RuntimeException("Syntax error in string \"" . text . "\"");
		}

		$ldS = preg_quote(leftDelimiter),
			rdS = preg_quote(rightDelimiter),
			pattern = "/" . ldS . "([^" . ldS . rdS . "]+)" . rdS . "/",
			matches = [];

		if ( !preg_match_all(pattern, text, matches, 2) ) {
			return text;
		}

		if ( gettype($matches) == "array" ) {
			foreach ( $matches as $match ) {
				if ( !isset($match[0]) || !isset match[1] ) {
					continue;
				}

				$words = explode(separator, match[1]),
					word = words[array_rand(words)],
					sub = preg_quote(match[0], separator),
					text = preg_replace("/" . sub . "/", word, text, 1);
			}
		}

		return text;
    }

    /***
	 * Makes a phrase underscored instead of spaced
	 *
	 * <code>
	 * echo Phalcon\Text::underscore("look behind"); // "look_behind"
	 * echo Phalcon\Text::underscore("Awesome Phalcon"); // "Awesome_Phalcon"
	 * </code>
	 **/
    public static function underscore($text ) {
		return preg_replace("#\s+#", "_", trim(text));
    }

    /***
	 * Makes an underscored or dashed phrase human-readable
	 *
	 * <code>
	 * echo Phalcon\Text::humanize("start-a-horse"); // "start a horse"
	 * echo Phalcon\Text::humanize("five_cats"); // "five cats"
	 * </code>
	 **/
    public static function humanize($text ) {
		return preg_replace("#[_-]+#", " ", trim(text));
    }

}
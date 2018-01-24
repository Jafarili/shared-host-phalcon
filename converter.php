<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set('memory_limit', '-1');


class Miner {
    public $objects;
    public $types;

    function listFolderFiles($dir){
        $return = array();
        $ffs = scandir($dir);
        unset($ffs[array_search('.', $ffs, true)]);
        unset($ffs[array_search('..', $ffs, true)]);
        if (count($ffs) < 1)
            return;
        foreach($ffs as $ff){
            $return[basename($dir.'/'.$ff)] = $ff;
            if(is_dir($dir.'/'.$ff))
                $return[basename($dir.'/'.$ff)] = $this->listFolderFiles($dir.'/'.$ff);
        }
        return $return;
    }

    function toPHP($base = './zephir') {
        $tree = $this->listFolderFiles($base);
        foreach ($tree as $key=>$value) {
            if (is_array($value)) {
                $this->toPHP($base . '/' . $key);
            }else {
                $path   = $base.'/'.$value;
                $retval = zephir_parse_file(file_get_contents($path), $path);

                foreach ($retval as $object) {
                    $this->objects[] = $object;
                }
            }
        }
    }

}
function rrmdir($path) {
    // Open the source directory to read in files
    $i = new DirectoryIterator($path);
    foreach($i as $f) {
        if($f->isFile()) {
            unlink($f->getRealPath());
        } else if(!$f->isDot() && $f->isDir()) {
            rrmdir($f->getRealPath());
        }
    }
    rmdir($path);
}

$miner = new Miner();
$miner->toPHP();
rrmdir("./library");

foreach ($miner->objects as $object) {
    $file = str_replace("./zephir","./library",str_replace(".zep",".php",$object["file"]));
    if (!is_dir(dirname($file)))
        mkdir(dirname($file),0777,true);

    if (!file_exists($file)) {
        file_put_contents($file,"<?php\n\n\n",FILE_APPEND);
        chmod($file, 0777);
    }

    if ($object["type"] == "namespace") {
        file_put_contents($file,"namespace ".$object["name"].";\n\n",FILE_APPEND);
    } elseif ($object["type"] == "use") {
        if (isset($object["aliases"]) and is_array($object["aliases"])) {
            foreach ($object["aliases"] as $alias) {
                if (isset($alias["alias"]))
                    file_put_contents($file,"use ".$alias["name"]." as ".$alias["alias"].";\n",FILE_APPEND);
                else
                    file_put_contents($file,"use ".$alias["name"].";\n",FILE_APPEND);
            }
        }
    }elseif ($object["type"] == "comment") {
        file_put_contents($file,"\n\n/*".$object["value"]."*/\n\n",FILE_APPEND);

    }elseif ($object["type"] == "interface") {
        //print_r($object);
        file_put_contents($file,interfaceMaker($object),FILE_APPEND);

    }elseif ($object["type"] == "class") {
        //print_r($object);
        //file_put_contents("classes.json",json_encode($object,JSON_PRETTY_PRINT)."\n\n\n\n\n",FILE_APPEND);
        file_put_contents($file,classMaker($object),FILE_APPEND);

    } elseif ($object["type"] != "cblock") {
        die(print_r($object));
    }

}

function classMaker($object) {
    $class = "";
    if ($object["abstract"] == 1)
        $class .= "abstract ";
    $class .= "class ".$object["name"];
    if (isset($object["extends"]))
        $class .= " extends ".$object["extends"];
    $class .= " {\n\n";
    if (isset($object["definition"]) and is_array($object["definition"])) {
        if (isset($object["definition"]["constants"]) and is_array($object["definition"]["constants"])) {
            foreach ($object["definition"]["constants"] as $constant) {
                if (isset($constant["docblock"])) {
                    $class .= "    /*".$constant["docblock"]."*/\n";
                }
                $class .= "    ";
                if (isset($constant["type"])) {
                    $class .= $constant["type"]." ";
                }
                $class .= $constant["name"];
                if (isset($constant["default"])) {
                    if ($constant["default"]["type"] == "null") {
                        $class .= "= null;";
                    }elseif ($constant["default"]["type"] == "empty-array") {
                        $class .= "= [];";
                    }elseif ($constant["default"]["type"] == "static-constant-access") {
                        $class .= "= ".$constant["default"]["left"]["value"]."::".$constant["default"]["right"]["value"];
                    }else {
                        $class .= "= ".$constant["default"]["value"].";";
                    }
                }
                $class .= ";\n\n";

            }
        }
        if (isset($object["definition"]["properties"]) and is_array($object["definition"]["properties"])) {
            foreach ($object["definition"]["properties"] as $property) {
                if (isset($property["docblock"])) {
                    $class .= "    /*".$property["docblock"]."*/\n";
                }
                $class .= "    ";
                if (isset($property["visibility"]) and is_array($property["visibility"])) {
                    foreach ($property["visibility"] as $visibility) {
                        if ($visibility != "deprecated")
                            $class .= $visibility." ";
                    }
                }
                $class .= "$".$property["name"];
                if (isset($property["default"])) {
                    if ($property["default"]["type"] == "static-constant-access") {
                        $class .= " = ".$property["default"]["left"]["value"]."::".$property["default"]["right"]["value"];
                    }
                }
                $class .= ";\n\n";

            }
        }
        if (isset($object["definition"]["methods"]) and is_array($object["definition"]["methods"])) {
            foreach ($object["definition"]["methods"] as $method) {
                if (isset($method["docblock"])) {
                    $class .= "    /*".$method["docblock"]."*/\n";
                }
                $class .= "    ";
                if (isset($method["visibility"]) and is_array($method["visibility"])) {
                    foreach ($method["visibility"] as $visibility) {
                        if ($visibility != "deprecated")
                            $class .= $visibility." ";
                    }
                }
                $class .= "function ".$method["name"]."(";
                if (isset($method["parameters"]) and is_array($method["parameters"])) {
                    foreach ($method["parameters"] as $key => $parameter) {
                        if ($key != 0)
                            $class .= ", ";
                        $class .= "$".$parameter["name"]." ";
                        if (isset($parameter["default"]) and is_array($parameter["default"])) {
                            if ($parameter["default"]["type"] == "null")
                                $class .= " = null ";
                            elseif (isset($parameter["default"]["value"]))
                                $class .= " = ".$parameter["default"]["value"]." ";
                        }
                    }
                }
                $class .= ") {\n";
/*                if (isset($method["statements"]) and is_array($method["statements"])) {
                    foreach ($method["statements"] as $key => $statement) {
                        if ($statement["type"] == "let") {
                            if (isset($statement["assignments"]) and is_array($statement["assignments"])) {
                                foreach ($statement["assignments"] as $key => $assignment) {
                                    if ($assignment["assign-type"] == "object-property") {
                                        if (isset($assignment["expr"]["value"])) {
                                            $class .= "        $".$assignment["variable"]."->".$assignment["property"]." = $".$assignment["expr"]["value"].";\n";
                                        }else {
                                            if (isset($assignment["expr"]["type"])) {
                                                if ($assignment["expr"]["type"] == "array-access") {
                                                    $class .= "        $".$assignment["variable"]."->".$assignment["property"]." = $".$assignment["expr"]["left"]["value"]."['".$assignment["expr"]["right"]["value"]."'];\n";
                                                }elseif ($assignment["expr"]["type"] == "array")
                                                    $class .= "        $".$assignment["variable"]."->".$assignment["property"]."[$".$assignment["expr"]["left"]["value"]."];\n";
                                            }else {
                                                $class .= "        $".$assignment["variable"]."->".$assignment["property"]." = ['".$assignment["expr"]["left"][0]["key"]["value"]."' , ".$assignment["expr"]["left"][0]["value"]["value"]."];\n";
                                            }
                                        }
                                    }elseif ($assignment["assign-type"] == "object-property-append") {
                                        if ($assignment["expr"]["type"] == "new") {
                                            $class .= "        $".$assignment["variable"]."->".$assignment["property"]."[] = new ".$assignment["expr"]["class"]."(";
                                            if (isset($assignment["expr"]["parameters"]) and is_array($assignment["expr"]["parameters"])) {
                                                foreach ($assignment["expr"]["parameters"] as $key => $parameter) {
                                                    $parameter = $parameter["parameter"];
                                                    if ($key != 0)
                                                        $class .= ", ";
                                                    $class .= "$".$parameter["value"];
                                                }
                                            }
                                            $class .= ");\n";
                                        }elseif ($assignment["expr"]["type"] == "array") {
                                            $class .= "        $".$assignment["variable"]."->".$assignment["property"]."[] = [";
                                            if (isset($assignment["expr"]["left"]) and is_array($assignment["expr"]["left"])) {
                                                foreach ($assignment["expr"]["left"] as $key => $value) {
                                                    $value = $value["value"];
                                                    if ($key != 0)
                                                        $class .= ", ";
                                                    if ($value["type"] == "variable")
                                                        $class .= "$".$value["value"];
                                                    elseif ($value["type"] == "fcall")
                                                        $class .= $value["name"]."()";
                                                }
                                            }
                                            $class .= "];\n";
                                        }else {
                                            if (!isset($assignment["expr"]["value"]))
                                                die(print_r($assignment));
                                            $class .= "        $".$assignment["variable"]."->".$assignment["property"]." = $".$assignment["expr"]["value"].";\n";
                                        }
                                    }else
                                        ;//die(print_r($statement));
                                }
                            }
                        }
                    }
                }*/
                $class .= "\n    }\n\n";
            }
        }
    }
    $class .= "}";
    return $class;
}

function interfaceMaker($object) {
    $interface = "interface ".$object["name"]. " {\n\n";
    if (isset($object["definition"]) and is_array($object["definition"])) {
        if (isset($object["definition"]["methods"]) and is_array($object["definition"]["methods"])) {
            foreach ($object["definition"]["methods"] as $method) {
                if (isset($method["docblock"])) {
                    $interface .= "    /*".$method["docblock"]."*/\n";
                }
                $interface .= "    ";
                if (isset($method["visibility"]) and is_array($method["visibility"])) {
                    foreach ($method["visibility"] as $visibility) {
                        if ($visibility != "deprecated")
                            $interface .= $visibility." ";
                    }
                }
                $interface .= "function ".$method["name"]."(";
                if (isset($method["parameters"]) and is_array($method["parameters"])) {
                    foreach ($method["parameters"] as $key => $parameter) {
                        if ($key != 0)
                            $interface .= ", ";
                        $interface .= "$".$parameter["name"]." ";
                        if (isset($parameter["default"]) and is_array($parameter["default"])) {
                            if ($parameter["default"]["type"] == "null")
                                $interface .= " = null ";
                            elseif (isset($parameter["default"]["value"]))
                                $interface .= " = ".$parameter["default"]["value"]." ";
                        }
                    }
                }
                $interface .= "); \n\n";

            }
        }
    }
    $interface .= "}";
    return $interface;
}

<?php

namespace Core;

/**
 * Class Autoloader
 * @package Core
 */
class Autoloader
{

    /**
     * @var array
     */
    private $_namespaces = ["Zend"];

    /**
     * @param array $namespaces
     */
    public function __construct(array $namespaces = ["Zend"])
    {
        spl_autoload_register([$this, "load"]);
        $this->_namespaces = $namespaces;
    }

    /**
     * @param $namespace
     */
    public function addAutoloadNamespace($namespace)
    {
        if (!in_array($namespace, $this->_namespaces)){
            $this->_namespaces[] = $namespace;
        }
    }

    /**
     * @param $name
     */
    public function load($name)
    {
        if (preg_match('/' . implode('|',$this->_namespaces). '/',
                $name
            ) == 1
        ) {
            $name = str_replace("_", "/", $name);
            require_once $name . ".php";
        } else if (strpos($name, "\\") !== false) {
            $name = str_replace("\\", "/", $name);
            require_once "./modules/" . $name . ".php";
        }
    }

}

<?php
namespace Wax\Core;
use Wax\Core\Parser\INI;
use Symfony\Component\Yaml\Yaml as Yaml;



class Configuration {
  
  protected $config = [];
  
  
  public function get($key) {
    if(isset($this->config[$key])) return $this->config[$key];
    elseif($result = $this->get_by_key($key)) return $result;
  }
  
  public function add_config($config, $as) {
    $this->config[$as] = $config;
  }
  
  public function add_resource($resource) {
    if(is_dir($resource)) $this->add_directory($resource);
	  if(substr($resource, -3)=="yml") {
      $parse = Yaml::parse($resource);
	    $this->add_config($parse, $this->resource_name($resource));
	  }
	  if(substr($resource, -3)=="php") {
      $res = include($resource);
	    $this->add_config($res, $this->resource_name($resource));
    }
	  if(substr($resource, -3)=="ini") {
	    $res = INI::parse($resource, true);
      $this->add_config($res, $this->resource_name($resource));
    }
  }
  
  protected function resource_name($resource) {
    $info = pathinfo($resource);
    return  basename($resource,'.'.$info['extension']);
  }
  
  protected function add_directory($directory) {
    $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($directory),\RecursiveIteratorIterator::CHILD_FIRST);
    foreach($iterator as $resource) {
      if(!$resource->isDot()) $this->add_resource($resource->getPathName());
    }
  }
  
  protected function get_by_key($lookup) {
    return $this->key_lookup($this->config, $lookup, "/");
  }
  
  protected function key_lookup(&$context, $name, $separator=".") {
    $pieces = explode($separator, $name);
    foreach ($pieces as $piece) {
        if (!is_array($context) || !array_key_exists($piece, $context)) {
            // error occurred
            return null;
        }
        $context = &$context[$piece];
    }
    return $context;
  }
  
  
  
}
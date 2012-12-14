<?php
namespace Wax\Core\Tests;
use Wax\Core\Configuration;


class ConfigurationTest extends \PHPUnit_Framework_TestCase {

  public function setup() {

  }
  
  public function teardown() {
    
  }
  
  public function test_init() {
    $config = new Configuration;
    $resources = __DIR__."/resources";
    $config->add_resource($resources);
    $eg = $config->get("example");
    $this->assertTrue(isset($eg));
  }
  
}
<?php
namespace Wax\Core;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;

$reflector = new \ReflectionClass('');
var_dump($reflector->getNamespaceName());

$collection = new RouteCollection();
$collection->add(
  'default_home', new Route('/', ['controller' => 'Controller\PageController'])
);

$collection->add(
  'default_action', 
  new Route('/{action}.{_format}', 
    [ 'controller'  => 'Controller\PageController', 
      'action'      =>"index",
      "_format"=>"html"
    ],
      [])
);

$collection->add(
  'default_action_id', 
  new Route('/{action}/{id}.{_format}', 
    [ 'controller'  => 'Controller\PageController', 
      'action'      =>"index",
      'id'          =>false,
      "_format"=>"html"
    ],
      [])
);

$collection->add(
  'default_action_id_params', 
  new Route('/{action}/{id}/{params}.{_format}', 
    [ 'controller'  => 'Controller\PageController', 
      'action'      =>"index",
      'id'          =>false,
      "params"      =>false,
      "_format"=>"html"
    ],
      ["params"=>"[^\.]+"])
);





return $collection;
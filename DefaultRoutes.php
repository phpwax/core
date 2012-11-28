<?php
namespace Wax\Core;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;

$collection = new RouteCollection();
$collection->add(
  'default_home', new Route('/', ['controller' => __NAMESPACE__.'\Controller\PageController'])
);

$collection->add(
  'default_action_id_params', 
  new Route('/{action}/{id}/{params}.{format}', 
    [ 'controller'  => __NAMESPACE__.'\Controller\PageController', 
      'action'      =>"index",
      'id'          =>false,
      "params"      =>false,
      "format"=>"html"
    ],
    ["params"=>".+"])
);
$collection->add(
  'default_action_id', 
    new Route('/{action}/{id}.{format}', 
    [ 'controller'  => __NAMESPACE__.'\Controller\PageController', 
      'action'      =>"index",
      'id'          =>false,
      "format"      =>"html"
    ],["id"=>".+"])
);
$collection->add(
  'default_action', 
    new Route('/{action}.{format}', 
    [ 'controller'  => __NAMESPACE__.'\Controller\PageController', 
      'action'      =>"index",
      "format"      =>"html"
    ], ['action' => '.+'])
);

return $collection;
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
  new Route('/{action}/{id}/{params}.{_format}', 
    [ 'controller'  => __NAMESPACE__.'\Controller\PageController', 
      'action'      =>"index",
      'id'          =>false,
      "params"      =>false,
      "_format"=>"html"
    ],
      ["params"=>".+","action"=>"[^/\.]+.","id"=>"[^/\.]+."])
);


return $collection;
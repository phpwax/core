<?php
namespace Wax\Core;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;



$collection = new RouteCollection();
$collection->add(
'default_home', new Route('/', ['controller' => $loader->default_controler, 'action'=>'index'])
);

$collection->add(
  'default_action', 
  new Route('/{action}.{_format}', 
    [ 'controller'  => $loader->default_controler, 
      'action'      =>"index",
      "_format"=>"html"
    ],
      [])
);

$collection->add(
  'default_action_id', 
  new Route('/{action}/{id}.{_format}', 
    [ 'controller'  => $loader->default_controler, 
      'action'      =>"index",
      'id'          =>false,
      "_format"=>"html"
    ],
      [])
);

$collection->add(
  'default_action_id_params', 
  new Route('/{action}/{id}/{params}.{_format}', 
    [ 'controller'  => $loader->default_controler, 
      'action'      =>"index",
      'id'          =>false,
      "params"      =>false,
      "_format"=>"html"
    ],
      ["params"=>"[^\.]+"])
);





return $collection;
<?php
namespace Wax\Core;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;



$collection = new RouteCollection();
$collection->add(
'default_home', new Route('/', ['controller' => '__DEFAULT__', 'action'=>'index'])
);

$collection->add(
  'default_action', 
  new Route('/{action}.{_format}', 
    [ 'controller'  => '__DEFAULT__', 
      'action'      =>"index",
      "_format"=>"html"
    ],
      [])
);

$collection->add(
  'default_action_id', 
  new Route('/{action}/{id}.{_format}', 
    [ 'controller'  => '__DEFAULT__', 
      'action'      =>"index",
      'id'          =>false,
      "_format"=>"html"
    ],
      [])
);

$collection->add(
  'default_action_id_params', 
  new Route('/{action}/{id}/{params}.{_format}', 
    [ 'controller'  => '__DEFAULT__', 
      'action'      =>"index",
      'id'          =>false,
      "params"      =>false,
      "_format"=>"html"
    ],
      ["params"=>"[^\.]+"])
);





return $collection;
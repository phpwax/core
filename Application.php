<?php
namespace Wax\Core;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;
use Symfony\Component\Routing;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\FileLoader;
use Symfony\Component\Routing\Loader\PhpFileLoader;
use Symfony\Component\Routing\Loader\YamlFileLoader;

  
class Application {

  protected $bundles;

  protected $bundlemap;
  protected $container;
  protected $rootdir;
  protected $environment;
  protected $debug;
  protected $booted;
  protected $name;
  protected $startTime;
  protected $classes;
  protected $errorReportingLevel;
  public $default_controller = false;
  

  public function __construct($environment, $debug) {
    $this->environment = $environment;
    $this->debug = (Boolean) $debug;
    $this->booted = false;
    $this->rootdir = $this->get_root_dir();
    $this->name = $this->get_name();
    if($this->debug) $this->startTime = microtime(true);
  }

  public function get_root_dir() {
    if (null === $this->rootdir) {
      $r = new \ReflectionObject($this);
      $this->rootdir = str_replace('\\', '/', dirname($r->getFileName()));
    }
    return $this->rootdir;
  }
  
  public function get_name() {
    if (null === $this->name) {
      $this->name = preg_replace('/[^a-zA-Z0-9_]+/', '', basename($this->rootdir));
    }
    return $this->name;
  }
  
  public function get_root_namespace() {
    $r = new \ReflectionObject($this);
    return $r->getNamespaceName();
  }
  
  /**
  * Boots the current kernel.
  *
  * @api
  */
  public function boot() {
    if (true === $this->booted) return;
    
    // Setup Routes
    $this->router = new RouteCollection;
    $locator = new FileLocator([$this->get_root_dir().'/config/']);
    
    try {
      $loader = new PhpFileLoader($locator);
      $this->router->addCollection($loader->load('routes.php'));
    
      $loader = new YamlFileLoader($locator);
      $this->router->addCollection($loader->load('routes.yml'));
    } catch (\InvalidArgumentException $e) {
      
    }
    
    
    


    // init bundles
    $this->initialize_bundles();

    foreach ($this->get_bundles() as $bundle) {
      $bundle->set_application($this);
      $bundle->boot();
    }
    $this->booted = true;
  }
  
  /**
  * Initializes the data structures related to the bundle management.
  *
  * - the bundles property maps a bundle name to the bundle instance,
  * - the bundleMap property maps a bundle name to the bundle inheritance hierarchy (most derived bundle first).
  *
  * @throws \LogicException if two bundles share a common name
  * @throws \LogicException if a bundle tries to extend a non-registered bundle
  * @throws \LogicException if a bundle tries to extend itself
  * @throws \LogicException if two bundles extend the same ancestor
  */
  protected function initialize_bundles() {
    // init bundles
    $this->bundles = array();
    $topMostBundles = array();
    $directChildren = array();

    foreach($this->register_bundles() as $bundle) {
      $name = $bundle->get_name();
      if(isset($this->bundles[$name])) {
          throw new \LogicException(sprintf('Trying to register two bundles with the same name "%s"', $name));
      }
      $this->bundles[$name] = $bundle;

      if($parentName = $bundle->get_parent()) {
        if (isset($directChildren[$parentName])) {
          throw new \LogicException(sprintf('Bundle "%s" is directly extended by two bundles "%s" and "%s".', $parentName, $name, $directChildren[$parentName]));
        }
        if($parentName == $name) {
          throw new \LogicException(sprintf('Bundle "%s" can not extend itself.', $name));
        }
        $directChildren[$parentName] = $name;
      } else $topMostBundles[$name] = $bundle;
    }

    // look for orphans
    if (count($diff = array_values(array_diff(array_keys($directChildren), array_keys($this->bundles))))) {
      throw new \LogicException(sprintf('Bundle "%s" extends bundle "%s", which is not registered.', $directChildren[$diff[0]], $diff[0]));
    }

    // inheritance
    $this->bundlemap = array();
    foreach ($topMostBundles as $name => $bundle) {
      $bundleMap = array($bundle);
      $hierarchy = array($name);

      while (isset($directChildren[$name])) {
        $name = $directChildren[$name];
        array_unshift($bundleMap, $this->bundles[$name]);
        $hierarchy[] = $name;
      }

      foreach ($hierarchy as $bundle) {
        $this->bundleMap[$bundle] = $bundleMap;
        array_pop($bundleMap);
      }
    }

  }
  
  /**
  * {@inheritdoc}
  *
  * @api
  */
  public function get_bundles() {
      return $this->bundles;
  }
  
  public function register_bundles(){
    return [];
  }
  
  
  /**
  * {@inheritdoc}
  *
  * @api
  */
  public function handle() {
    if (false === $this->booted) $this->boot();
    try {
      $request = Request::createFromGlobals();
      $this->request = $request;
      
      // Load in the default routes
      $loader = new PhpFileLoader(new FileLocator(array(__DIR__)));
      $default_collection = $loader->load('DefaultRoutes.php');
      
      // Map the default controller to the current namespace
      if($this->default_controller) $default = $this->default_controller;
      else $default = $this->get_root_namespace()."\\Controller\PageController"; 
      die($default);
      foreach($default_collection as &$route) {
        if($route->getOption("controller")=="__DEFAULT__") {
          $route->setOption("controller",$default);
        }
      }
      
      $this->router->addCollection($default_collection);
      
            
      $context = new Routing\RequestContext();
      $context->fromRequest($request);
      $matcher = new Routing\Matcher\UrlMatcher($this->router, $context);
      $attributes = $matcher->match($request->getPathInfo());
      $response = $this->dispatch($attributes);
      
      
     } catch (\Exception $e) {
       $response = new Response('A fatal error occurred: ' . $e->getMessage(), 500);
     }
   
     return $response;
   }
   
   /**
    * Uses the routed controller and action
    * Returns response.
    *
    * @param string $attributes 
    * @return $response
    */
   public function dispatch($attributes) {


    print_r($attributes); exit;
   }
    
    


}


<?php

namespace ultimo\mvc\plugins;

class CachePlugin implements ApplicationPlugin, ModulePlugin, ControllerPlugin, \ultimo\util\cache\Cache {
  /**
   * Cache object used with the MVC.
   * @var \ultimo\util\cache\Cache 
   */
  protected $cache;
  
  /**
   * Namespace to prepend to keys.
   * @var string
   */
  protected $namespace;
  
  /**
   * Constructor
   * @param \ultimo\util\cache\Cache $cache Cache object used with the MVC.
   * @param string $namespace Namespace to prepend to keys.
   */
  public function __construct(\ultimo\util\cache\Cache $cache, $namespace='') {
    $this->cache = $cache;
    $this->namespace = $namespace;
  }
  
  /**
   * Sets the cache object used with the MVC.
   * @param \ultimo\util\cache\Cache $cache Cache object used with the MVC.
   */
  public function setCache(\ultimo\util\cache\Cache $cache) {
    $this->cache = $cache;
  }
  
  /**
   * Returns the cache object used with the MVC.
   * @return \ultimo\util\cache\Cache Cache object used with the MVC.
   */
  public function getCache() {
    return $this->cache;
  }
  
  /**
   * Writes data to the cache.
   * @param string $key The cache key.
   * @param mixed $data Data to write.
   * @param integer $ttl The ttl of the data in seconds.
   */
  public function save($key, $data, $ttl=null) {
    $this->cache->save($this->assembleKey($key), $data, $ttl);
  }
  
  /**
   * Reads data from the cache.
   * @param string $key The cache key.
   * @param bool $ignoreExpiration Whether to return the data when it is
   * expired.
   * @return mixed The data in the cache belonging to the test key.
   */
  public function load($key, $ignoreExpiration=false) {
    return $this->cache->load($this->assembleKey($key), $ignoreExpiration);
  }
  
  /**
   * Tests and returns whether a cache key is not expired and puts the data
   * in the parameter $data.
   * @param string $key The cache key.
   * @param mixed $data The variable to put the data in.
   * @param integer $ttl The number of seconds to extend the ttl if data is
   * expired.
   * @return bool Whether the data is expired.
   */
  public function testLoad($key, &$data, $ttl=0) {
    return $this->cache->testLoad($this->assembleKey($key), $data, $ttl);
  }
  
  /**
   * Returns wheter the data of the specified key is present and not expired.
   * @param string $key The key of the data to test.
   * @return boolean Whether the data was present and not expired.
   */
  public function test($key) {
    return $this->cache->test($this->assembleKey($key));
  }
  
  /**
   * Sets the time to live of data. Set to equal or less than zero to let data
   * expire.
   * @param string $key The key of the data to touch.
   * @param integer $ttl The number of seconds to set the time to live of the
   * data to.
   */
  public function touch($key, $ttl) {
    $this->cache->touch($this->assembleKey($key), $ttl);
  }
  
  /**
   * Deletes the key and its data in the cache.
   * @param string $key The cache key.
   */
  public function delete($key) {
    $this->cache->delete($this->assembleKey($key));
  }
  
  /**
   * Returns the contents of the cache or updates the cache and returns that
   * content.
   * @param string $key The cache key.
   * @param callback $dataCallback A callback that returns updated data.
   * @param integer $ttl The ttl of the data in seconds.
   * @param integer $ttlExtend The number of seconds to extend the ttl if data
   * is expired.
   * @return mixed The data from cache or callback.
   */
  public function loadOrUpdate($key, $dataCallback, $ttl=null, $ttlExtend=0) {
    $key = $this->assembleKey($key);
    
    $data = null;
    if (!$this->cache->testLoad($key, $data, $ttlExtend)) {
      $data = $dataCallback();
      $this->cache->save($key, $data, $ttl);
    }
    return $data;
  }
  
  /**
   * Assembles the key using the namespace.
   * @param string $key The cache key.
   * @return The assembled cache key.
   */
  protected function assembleKey($key) {
    return $this->namespace . $key;
  }
  
  /**
   * Called after the plugin is added to an application.
   * @param \ultimo\mvc\Application $application The application the plugin is
   * added to.
   */
  public function onPluginAdded(\ultimo\mvc\Application $application) { }
  
  /**
   * Called after a module is created by the application.
   * @param \ultimo\mvc\Module $module The created module.
   */
  public function onModuleCreated(\ultimo\mvc\Module $module) {
    $module->addPlugin($this, 'cache');
    
    // add decorator to phptpl Cache helper to use this cache
    $view = $module->getView();
    if ($view instanceof \ultimo\phptpl\Engine) {
      $view->addDecoratorClass('Cache', 'ultimo\phptpl\mvc\helpers\decorators\Cache', array('cachePlugin' => $this));
    }
  }
  
  /**
   * Called before routing.
   * @param \ultimo\mvc\Application $application The application calling the
   * router.
   * @param \ultimo\mvc\Request $request The unrouted request.
   */
  public function onRoute(\ultimo\mvc\Application $application, \ultimo\mvc\Request $request) { }
  
  /**
   * Called after routing.
   * @param \ultimo\mvc\Application $application The application that called the
   * router.
   * @param \ultimo\mvc\Request $request The routed request.
   */
  public function onRouted(\ultimo\mvc\Application $application, \ultimo\mvc\Request $request=null) { }
  
  /**
   * Called before dispatch.
   * @param \ultimo\mvc\Application $application The application dispathcing.
   */
  public function onDispatch(\ultimo\mvc\Application $application) { }
  
  /**
   * Called after dispatch.
   * @param \ultimo\mvc\Application $application The application that
   * dispatched.
   */
  public function onDispatched(\ultimo\mvc\Application $application) { }
  
  /**
   * Called after a module created a controller.
   * @param \ultimo\mvc\Controller $controller The created controller.
   */
  public function onControllerCreated(\ultimo\mvc\Controller $controller) {
    $controller->addPlugin($this, 'cache');
  }
  
  /**
   * Called before a controller action is called.
   * @param \ultimo\mvc\Controller $controller The controller the action is
   * being called on.
   * @param string $actionName The name of the action.
   */
  public function onActionCall(\ultimo\mvc\Controller $controller, &$actionName) { }
  
  /**
   * Called after a controller action was called.
   * @param \ultimo\mvc\Controller $controller The controller the action was
   * calloed on.
   * @param string $actionName The name of the called action.
   */
  public function onActionCalled(\ultimo\mvc\Controller $controller, $actionName) { }
}
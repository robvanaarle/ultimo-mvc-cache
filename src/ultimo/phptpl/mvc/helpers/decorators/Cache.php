<?php

namespace ultimo\phptpl\mvc\helpers\decorators;

class Cache extends \ultimo\phptpl\HelperDecorator {
  
  public function __construct(\ultimo\phptpl\Helper $helper, array $config = array()) {
    parent::__construct($helper, $config);
    
    if (isset($config['cachePlugin'])) {
      $helper->setCache($config['cachePlugin']);
    }
  }
  
  /**
   * Helper initial function.
   * @return HeadLink This class.
   */
  public function __invoke() {
    return $this;
  }
}
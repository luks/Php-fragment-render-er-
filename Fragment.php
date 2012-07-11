<?php

/**
 * @author Luka Musin
 * @Magic method autoload - load everything in his folder and recursevly his subfolders
 * @param type $class_name 
 */
function __autoload($class_name) {
 
  $path = '';
  $it = new FileExtensionFinder(dirname(__FILE__), 'php');
  foreach ($it as $entry) {
    if ($class_name === $entry->getBasename('.php')) {
      $path = $entry->getRealPath();
      break;
    }
  }
  if (empty($path)) {
    throw new Exception("It seems that class: {$class_name} doesn't exist! ");
  } else {
    require_once $path;
  }
}

/**
 * class extends SPL Filter iterator - very usefull and fast - 
 * it is implemented in php core 
 */
class FileExtensionFinder extends FilterIterator {

  protected $predicate, $path;
/**
 * 
 * @param String $path
 * @param String $predicate extension to look at 
 */
  public function __construct($path, $predicate) {
    $this->predicate = $predicate;
    $this->path = $path;
    $it = new RecursiveDirectoryIterator($path);
    $flatIterator = new RecursiveIteratorIterator($it);
    parent::__construct($flatIterator);
  }

  public function accept() {
    $pathInfo = pathinfo($this->current());
    $extension = $pathInfo['extension'];
    return ($extension == $this->predicate);
  }

}

/**
 * @author Luka Musin
 * Class fragment is singleton to be used for load defined fragments defined as a classes 
 * in include/fragment folders and subfolders. 
 * Main rule is that class file name has to be same as class name 
 */
class Fragment {  
  /**
   *
   * @var Array - Will hold all object instiate with this method 
   */
  protected static $_instances = array();
  /**
   * private __construct so it could not be initialised 
   */ 
  private function __construct() {
    
  }
  /**
   * private __clone so it can not be cloned 
   */
  private function __clone() {
    
  }
  /**
   *
   * @param String $class class name case sensitive etc: "MyClass"
   * @param String $method Method to call
   * @param Array $params  Parameters to pass in.
   */
  public static function exec($class, $method, $params = array()) {
   
    if(!array_key_exists($class, self::$_instances)) {
      self::$_instances[$class] = new $class;    
    }      
    $object = self::$_instances[$class];
    if($object instanceof MainFragment) {
      $handler = array($object, $method);   
      if (is_callable($handler) && method_exists($object, $method)) {
        return call_user_func_array($handler, $params);
      } else {      
        throw new Exception("It look's like class:\"{$class}\" or method:\"{$method}\" could not be called!");      
      }
    } else {
      throw new Exception("It look's like class:\"{$class}\" is not instanc of MainFragment");      
    }
    return false;
  }
}

?>

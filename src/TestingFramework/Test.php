<?php
/*
 *
 */

require 'Runnable.php';

/**
 *
 */
class Test implements Runnable {

  /**
   *
   */
  private $object;

  /**
   *
   */
  private $procedure = array();

  /**
   *
   */
  public function __construct($object = null) {
    $this->object = $object;
  }

  public function __destruct() {
    $this->documentation = "haha!";
    print $this->documentation;
  }

  /**
   *
   */
  public function __call($method, $args) {
    $this->procedure[] = array($method, $args);

    if ($this->object) {
      return call_user_func_array(array(&$this->object, $method), $args);
    }

    return true;
  }

  /**
   *
   */
  public function encode() {
    print json_encode(get_object_vars($this));
  }

  /**
   *
   */
  public function getProcedure() {
    $rVal = array();

    foreach ($this->procedure as $c) {
      print "$c[0](".implode(', ', $c[1])."); \n";
    }
  }

  /**
   *
   */
  public function run(&$object) {
    foreach ($this->procedure as $call) {
      call_user_func_array(array(&$object, $call[0]), $call[1]);
    }
  }
}

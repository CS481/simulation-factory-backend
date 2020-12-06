<?php
/*
 *
 */

namespace SimulationFactoryBackend\db;

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
  private $result;

  private $expectedOutput = array();

  private $actualOutput = array();

  /**
   *
   */
  public function __construct($object = null) {
    $this->object = $object;
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
  public function __toString() {
    if ($this->actualOutput != $this->expectedOutput) {
      return "[!]  Expected output  {$this->expectedOutput}" . "\n"
           . "       Actual output  {$this->actualOutput}"   . "\n";
    } else {
      return "[ ]    Actual output  {$this->actualOutput}"   . "\n";
    }
  }

  /**
   *
   */
  public function getProcedure() {
    $rVal = array();

    foreach ($this->procedure as $c) {
      echo "$c[0](".implode(', ', $c[1])."); \n";
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

  public function setExpectedOutput($expectedOutput = null) {
    $this->expectedOutput = $expectedOutput;
  }

  public function getExpectedOutput() {
    return $this->expectedOutput;
  }

  public function setActualOutput($actualOutput = null) {
    $this->actualOutput = $actualOutput;
  }

  public function getActualOutput() {
    return $this->actualOutput;
  }

  public function setResult($result = null) {
    $this->result = $result;
  }

  public function getResult() {
    return $this->result;
  }
}

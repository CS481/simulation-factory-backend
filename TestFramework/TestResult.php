<?php

namespace SimulationFactoryBackend\TestFramework;

//require "TestFailure.php";

class TestResult {

  /**
   *
   */
  private $testFailures = [];

  /**
   *
   */
//  private $testPasses = [];

  /**
   *
   */
  public function __construct() {
    $this->testFailures = (object)["testData" => NULL];
  }

  /**
   *
   */
  public function addTestFailure(?string $testName, $testData) {
    $this->testFailures->testName = $testName;
//    array_push($this->testFailures->testData, $testData);
//    array_push($this->testFailures->testData, $testData);
  }

  /**
   *
   */
//  public function addTestPass(TestPass $testPass) {
//    array_push($this->testPasses, $testPass);
//  }

  /**
   * Returns an array of TestFailure objects for the test failures.
   *
   * @return TestFailure[]
   */
  public function getTestFailures() {
    return $this->testFailures;
  }

  /**
   *
   */
//  public function getTestPasses() : array {
//    return $this->testPasses;
//  }
}

?>

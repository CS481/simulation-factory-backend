<?php

namespace SimulationFactoryBackend\TestFramework;

require "TestResult.php";

final class TestCase implements Test {

  private $output = "";

  public function __construct() {
    
  }

  public function run(TestResult $testResult = null) : TestResult {
    $this->output = "Hello.";

    return $testResult;
  }
}

?>

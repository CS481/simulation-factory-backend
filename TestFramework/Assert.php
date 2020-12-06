<?php

namespace SimulationFactoryBackend\TestFramework;

require "TestResult.php";

class Assert {

  public static function assertEquals($expected, $actual) {
    if (!($expected == $actual)) {
      return new TestResult(TRUE, "The two variables are not equal.");
    } else {
      return new TestResult(FALSE, "The two variables are equal.");
    }
  }

  public static function assertDummy($expected, $actual) {
    if (!($expected == $actual)) {
      return new TestResult(TRUE);
    } else {
      return new TestResult(FALSE);
    }
  }
}

?>

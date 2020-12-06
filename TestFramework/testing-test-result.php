<?php

namespace SimulationFactoryBackend\TestFramework;

require "TestResult.php";

$myTestResult = new TestResult();
$myTestResult->addTestFailure("hello test", ["this thing"=>"gug", "another thing"=>"wow!"]);
var_dump($myTestResult->getTestFailures());
?>

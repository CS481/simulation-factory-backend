<?php

namespace SimulationFactoryBackend\TestFramework;

require 'Assert.php';

$myAssert = new Assert();
$myAssert::assertEquals("hello", "hello");
$myAssert::assertEquals("hello", "no");


$myDummy = new Assert();
$myDummy::assertDummy("hello", "hello");
$myDummy::assertDummy("hello", "no");

?>

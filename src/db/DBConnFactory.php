<?php namespace SimulationFactoryBackend;
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/MongoConn.php';

// Why is everything in this stupid language a string?
function DBConnFactory() : string {
  return 'SimulationFactoryBackend\MongoConn';
}
?>

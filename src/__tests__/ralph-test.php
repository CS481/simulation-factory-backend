<?php namespace SimulationFactoryBackend\db;
require_once __DIR__ . '../../../vendor/autoload.php';
require_once __DIR__ . '/../db/IDBConn.php';
use MongoDB\Client;
use MongoDB\BSON\ObjectId;
use MongoDB\Driver\ReadPreference;
use MongoDB\Driver\Exception\AuthenticationException;
use Exception;
use stdClass;


   $m = new MongoClient();

  echo "This is it.\n";
?>

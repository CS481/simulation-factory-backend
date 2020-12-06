<?php
/*
 *
 */

namespace SimulationFactoryBackend\db;

require 'MongoConnTest.php';



$getConnectionStringTest = new MongoConnTest();

$username = 'taxes';
$password = 'me';

$mongoConn = new MongoConn($username, $password);



print "\n" . "Running getConnectionString method test cases." . "\n";

print "Try with expected correct output." . "\n";

$getConnectionStringTest = new MongoConnTest($mongoConn);

$getConnectionStringTest->testGetConnectionString($username, $password);

$getConnectionStringTest->setExpectedOutput('mongodb://taxes:me@localhost/SimulationFactory');
$getConnectionStringTest->run($mongoConn);

print $getConnectionStringTest;

print "Try with expected incorrect output." . "\n";

$getConnectionStringTest->setExpectedOutput('mongodb://taxes:me@localhost/WonderFactory');
$getConnectionStringTest->run($mongoConn);

print $getConnectionStringTest;



print "\n" . "Running select method test cases." . "\n";

$collection = 'collection';
$query      = (object)['_id' => '5fc4559f3a0ff8548a1d9183'];

print "Try with expected correct output." . "\n";

$selectTest = new MongoConnTest($mongoConn);

$selectTest->testSelect($collection, $query, $keys = []);

$selectTest->setExpectedOutput('{"_id":{"$oid":"5fc4559f3a0ff8548a1d9183"},"newthing":"wow"}');
$selectTest->run($mongoConn);

print $selectTest;

print "Try with expected incorrect output." . "\n";

$selectTest->setExpectedOutput('{"_id":{"$oid":"5fc4559f3a0ff8548a1d9183"},"newthing":"oh no"}');
$selectTest->run($mongoConn);

print $selectTest;



print "\n" . "Running selectOne method test cases." . "\n";

$collection = 'collection';
$query      = (object)['newthing' => 'wow'];

print "Try with expected correct output." . "\n";

$selectOneTest = new MongoConnTest($mongoConn);

$selectOneTest->testSelectOne($collection, $query, $keys = []);

$selectOneTest->setExpectedOutput('"wow"');
$selectOneTest->run($mongoConn);

print $selectOneTest;

print "Try with expected incorrect output." . "\n";

$selectOneTest->setExpectedOutput('"whoa"');
$selectOneTest->run($mongoConn);

print $selectOneTest;



print "\n" . "Running update method test cases." . "\n";

$collection = 'collection';
$data       = (object)['newthing' => 'are you working?'];
$query      = (object)['_id' => '5fc4559f3a0ff8548a1d9183'];

print "Try with expected correct output." . "\n";

$updateTest = new MongoConnTest($mongoConn);

$updateTest->testUpdate($collection, $data, $query);

$updateTest->setExpectedOutput('{"_id":{"$oid":"5fc4559f3a0ff8548a1d9183"},"newthing":"are you working?"}');
$updateTest->run($mongoConn);

print $updateTest;

print "Try with expected incorrect output." . "\n";

$updateTest->setExpectedOutput('{"_id":{"$oid":"5fc4559f3a0ff8548a1d9183"},"newthing":"no, you are not working."}');
$updateTest->run($mongoConn);

print $updateTest;



print "\n";

$mongoConn->update($collection, (object)['newthing' => 'wow'], $query);




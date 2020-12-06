<?php
/*
 *
 */

namespace SimulationFactoryBackend\db;

require 'MongoConn.php';
require 'Test.php';

/**
 *
 */
class MongoConnTest extends Test {

  /**
   *
   */
  private $output;

  /**
   *
   */
  public function testGetConnectionString($username, $password) {
    try {
      parent::setActualOutput($this->getConnectionString($username, $password));
    } catch (Exception $e) {

    }
  }

  /**
   *
   */
  public function testSelect(string $collection, object $query, array $keys) {
    try {
      $result = $this->select($collection, $query);

      foreach ($result as $document) {
        parent::setActualOutput(json_encode($document));
      }
    } catch (Exception $e) {

    }
  }

  /**
   *
   */
  public function testSelectOne(string $collection, object $query, array $keys) {
    try {
      $result = $this->selectOne($collection, $query);

      foreach ($result as $document) {
        parent::setActualOutput(json_encode($document));
      }
    } catch (Exception $e) {

    }
  }

  /**
   *
   */
  public function testUpdate(string $collection, object $data, object $query) {
    try {
      $this->update($collection, $data, $query);
      $result = $this->selectOne($collection, $query);

      foreach ($result as $document) {
        parent::setActualOutput(json_encode($result));
      }
    } catch (Exception $e) {

    }
  }
}

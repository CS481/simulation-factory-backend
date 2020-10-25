<?php

class Database_Transaction {

  protected $pdo;
  public $lastInsertId;

  public function __construct($pdo) {
    $this->pdo = $pdo;
  }

  public function startTransaction() {
    $this->pdo->beginTransaction();
  }

  public function insertTransaction($sql, $data) {
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute(array($data));
  }

  public function submitTransaction() {
    try {
      $this->pdo->commit();
    } catch (PDOException $e) {
      $this->pdo->rollBack();

      return false;
    }

    return true;
  }
}

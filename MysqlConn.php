<?php
class MysqlConn {

  protected $pdo;
  protected $known_tables;

  public function __construct($user, $pass) {
    $this->pdo = new PDO('mysql:host=localhost;dbname=testdb', $user, $pass, array(PDO::ATTR_PERSISTENT => true));
    $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // In order to access arbitrary tables securely, we need to know all of the tables & columns that can be accessed ahead of time
    $this->known_tables->geo = 'coordinate';
  }

  public function beginTransaction() {
    $this->pdo->beginTransaction();
  }

  public function insert(string $table, object $data) {
   $column = $this->getColumn($table);
   // If we find ourselves calling insert with the same $table frequently with a single MysqlConn instance, we can cache this for performance benefits
   $this->pdo->prepare("INSERT INTO ${table} (${column}) VALUES (?)")->execute(array(json_encode($data)));
  }

  public function select(string $table, object $critera) {
    $column = $this->getColumn($table);
    return $this->pdo->prepare("SELECT ${column} FROM ${table} WHERE(?)")->execute(array(json_encode($data)));
  }

  public function submitTransaction() {
    try {
      $this->pdo->commit();
      return true;
    } catch (PDOException $e) {
      $this->pdo->rollBack();
      return false;
    }
  }

  public function abortTransaction() {
    $this->pdo->rollBack();
  }

  protected function getColumn(string $table) {
    if (isset($this->known_tables->$table)) {
       return $this->known_tables->$table;
    } else {
      throw new Exception("Table $table is not recognized by MysqlConn");
    }
  }
}

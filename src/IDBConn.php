<?php namespace SimulationFactoryBackend
interface IDBConn {
  // Use database native login function for user/pass
  public function __construct(string $user, string $pass);
  public static function newUser(string $user, string $pass);

  public function beginTransaction();
  public function submitTransaction();
  public function abortTransaction();

  // May have to create rules about row-by-row, user-by-user permissions, which may vary from table to table
  public function insert(string $table, object $data);
  public function select(string $table, object $critera);
  public function update(string $table, object $critera, object $data);
}
?>

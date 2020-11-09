<?php namespace SimulationFactoryBackend\db;
interface IDBConn {
  // Login to the database with the given username and password
  public function __construct(string $user, string $pass);
  // Login to the database using the username and password in $json->user
  public static function constructFromJson(object $json) : IDBConn;

  // Create a new user with the given username and password. The created user will have read/write permissions for only the SimulationFactory database
  public static function createUser(string $user, string $pass);
  // Create a new user with the username and password in $json->user. The new user's permission are the same as createUser
  public static function createUserFromJson(object $json);

  // Begin a database transaction
  public function beginTransaction();
  // Commit the current transaction
  public function submitTransaction();
  // Rollback the current transaction
  public function abortTransaction();

  // Insert $object into the table named $table. Returns the id of the new database entry
  public function insert(string $table, object $data) : string;
  // Select objects from the table named $table, which match $query.
  // All set values of $query are ANDed, unless or() or not_set() is used.
  // Omitted values are ignored.
  // Returns a cursor which can be used to iterate through all of the results
  public function select(string $table, object $query) : \Traversable;
  // Just like select, except selectOne returns the first object to match the query, rather than a cursor
  public function selectOne(string $table, object $query) : object;
  // Updates the first existing record that matches the critera
  // The record is selected using $query, exactly like select()
  // The existing record is merged with $data, which keeps all keys in both objects.
  // If the same key exists in $data and the database, the database entry is overwritten
  public function update(string $table, object $data, object $query);
  // Behaves just like update, except replace completely replaces the target document with $data.
  // The id field, however, is not changed
  public function replace(string $table, object $data, object $query);
  // Deletes the first existing record that matches the criteria.
  // The record is selected using $query, exactly like select()
  public function delete(string $collection, object $query);

  // Returns an object that can be used as a query for select.
  // If additional keys are added to this query, they will be ANDed to the OR.
  // The resulting query behaves like so:
  //   ($possibilites[0] OR $possibilities[1] OR ... $possibilities[n]) AND $key_added_after_1 AND ... $key_added_after_n
  // Each of the $possibilities can be a fully fledged query object on it's own, allowing for arbitrarily complex queries
  public function or(object ...$possibilities) : object;
  // Returns a value that, when used in a query, will only select objects where the specified member is not set.
  // For example, in order to create a query that selects a record only if player2 is not set:
  //   $query->player2 = $conn->not_set();
  public function not_set();
}
?>

<?php namespace SimulationFactoryBackend;
require __DIR__ . '/../vendor/autoload.php';
use MongoDB\Client;
use MongoDB\Driver\ReadPreference;
use Exception;

class MongoConn {
  protected $conn;
  protected $database;
  private static $username;
  private static $password;
  private static $host;

  // TODO: default $database to 'SimulationFactory'
  public function __construct(string $username, string $password, string $database='test') {
    if (!isset(MongoConn::$username)) {
      MongoConn::SetCredentials();
    }
    $this->database = $database;
    $conn_user = urlencode($username);
    $conn_pass = urlencode($password);
    $conn_host = MongoConn::$host;
    echo  "mongodb://${conn_user}:${conn_pass}@${conn_host}/${database}";
    $this->conn = new Client("mongodb://${conn_user}:${conn_pass}@${conn_host}/${database}");
  }

  public static function createUser(string $username, string $password, string $database='test') {
    if (!isset(MongoConn::$username)) {
      MongoConn::SetCredentials();
    }

    $conn_user = urlencode(MongoConn::$username);
    $conn_pass = urlencode(MongoConn::$password);
    $conn_host = MongoConn::$host;
    $conn = new Client("mongodb://${conn_user}:${conn_pass}@${conn_host}/${database}");
    $coll = $conn->$database;
    $coll->command(
      [
        'createUser' => $username,
        'pwd' => $password,
        'roles' => ['readWrite'],
      ],
      [
        'readPreference' => new ReadPreference(ReadPreference::RP_PRIMARY),
      ]
    );
  }

  public function beginTransaction() {}
  public function submitTransaction() {}
  public function abortTransaction() {}

  public function insert(string $collection, object $data) {
    $database = $this->database;
    $coll = $this->conn->$database->$collection;
    $insertOneResult = $coll->insertOne($data);
    if ($insertOneResult->getInsertedCount() != 1) {
      throw new Exception('Failed to insert data into the database');
    }
  }

  public function select(string $collection, object $query, array $keys=[]) {
    $database = $this->database;
    $coll = $this->conn->$database->$collection;
    $projection_keys = [];
    foreach($keys as $key) {
      $projection_keys[$key] = 1;
    }
    $projection = ['projection' => $projection_keys];
    return $coll->find($query, $projection);
  }

  public function update(string $collection, object $data, object $query) {
    $database = $this->database;
    $coll = $this->conn->$database->$collection;
    $updateOneResult = $coll->updateOne($query, ['$set' => $data]);
    if ($updateOneResult->getMatchedCount() != 1) {
      throw new Exception('Failed to update data in the database');
    }
  }

  private static function SetCredentials() {
    $ini_path = getenv('MONGODB_CREDENTIALS');
    $ini_contents = parse_ini_file($ini_path);
    if ($ini_contents == false) {
      throw new Exception('Cannot read mongodb credentials file');
    } else {
      MongoConn::$username = $ini_contents['username'];
      MongoConn::$password = $ini_contents['password'];
      MongoConn::$host = $ini_contents['host'];
    }
  }
}
?>

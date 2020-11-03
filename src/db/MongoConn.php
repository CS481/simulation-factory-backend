<?php namespace SimulationFactoryBackend;
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/IDBConn.php';
use MongoDB\Client;
use MongoDB\BSON\ObjectId;
use MongoDB\Driver\ReadPreference;
use MongoDB\Driver\Exception\AuthenticationException;
use Exception;
use stdClass;

// TODO: Enforce only allowing access to known tables
class MongoConn implements IDBConn {
  protected $conn;
  private static $database = "SimulationFactory";
  private static $username;
  private static $password;
  private static $host;

  public function __construct(string $username, string $password) {
    if (!isset(MongoConn::$username)) {
      MongoConn::setCredentials();
    }
    $database = MongoConn::$database;
    $conn_user = urlencode($username);
    $conn_pass = urlencode($password);
    $conn_host = MongoConn::$host;
    try {
      $this->conn = new Client("mongodb://${conn_user}:${conn_pass}@${conn_host}/${database}");
      $this->select("nodata", new stdClass()); // Try to view some data, in order to ensure the user is authenticated properly
  } catch (AuthenticationException $_) {
      header("Unauthorized", true, 401);
      echo "User cannot be authenticated";
      exit;
    }
  }

  public static function constructFromJson(object $json) : IDBConn {
    return new MongoConn($json->user->username, $json->user->password);
  }

  public static function createUser(string $username, string $password) {
    if (!isset(MongoConn::$username)) {
      MongoConn::setCredentials();
    }

    $database = MongoConn::$database;
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

  public static function createUserFromJson(object $json) {
    return MongoConn::createUser($json->user->username, $json->user->password);
  }

  //TODO: More user-level operations

  //TODO: transactions?
  public function beginTransaction() {}
  public function submitTransaction() {}
  public function abortTransaction() {}

  public function insert(string $collection, object $data) : string {
    $database = MongoConn::$database;
    $coll = $this->conn->$database->$collection;
    $insertOneResult = $coll->insertOne($data);
    if ($insertOneResult->getInsertedCount() != 1) {
      throw new DBOpException('Failed to insert data into the database');
    } else {
      return (string)($insertOneResult->getInsertedId());
    }
  }

  public function select(string $collection, object $query, array $keys=[]) : \Traversable {
    $database = MongoConn::$database;
    $coll = $this->conn->$database->$collection;
    $projection_keys = [];
    foreach($keys as $key) {
      $projection_keys[$key] = 1;
    }
    $projection = ['projection' => $projection_keys];
    $query = $this->normalize($query);
    return $coll->find($query, $projection);
  }

  public function selectOne(string $collection, object $query, array $keys=[]) : object {
    $database = MongoConn::$database;
    $coll = $this->conn->$database->$collection;
    $projection_keys = [];
    foreach($keys as $key) {
      $projection_keys[$key] = 1;
    }
    $projection = ['projection' => $projection_keys];
    $query = $this->normalize($query);
    return $coll->findOne($query, $projection);

  }

  public function update(string $collection, object $data, object $query) {
    $database = MongoConn::$database;
    $coll = $this->conn->$database->$collection;
    $query = $this->normalize($query);
    $updateOneResult = $coll->updateOne($query, ['$set' => $data]);
    if ($updateOneResult->getMatchedCount() != 1) {
      throw new DBOpException('Failed to update data in the database');
    }
  }

  public function delete(string $collection, object $query) {
    $database = MongoConn::$database;
    $coll = $this->conn->$database->$collection;
    $query = $this->normalizeQuery($query);
    $deleteOneResult = $coll->deleteOne($query);
    if ($deleteOneResult->getDeletedCount() != 1) {
      throw new DBOpException('Failed to delete data from the database');
    }
  }

  public function or(object ...$possibilities) : object {
    return (object)['$or' => $possibilities];
  }

  public function not_set() {
    return ['$exists' => false];
  }

  private function normalize(object $data) {
   if (isset($data->_id)) {
      $data->_id = new ObjectId($data->_id);
    }
    return $data;
  }

  private static function setCredentials() {
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

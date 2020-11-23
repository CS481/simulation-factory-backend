<?php namespace SimulationFactoryBackend\controller;
require_once __DIR__ . '/../util/validate_simulation_owner.php';
require_once __DIR__ . '/../db/DBConnFactory.php';

// Creates a .sav file and forces the user's browser to download it
// $conn is an instance of IDBComm
// $owner is the User json that owns the simulation
// $sim_id is the id of the simulation to get the instances of
function download_responses($conn, object $owner, string $sim_id) {
  \SimulationFactoryBackend\util\validate_simulation_owner($conn, $owner, $sim_id);
  $__DIR__ = __DIR__;
  $conn_classname = \SimulationFactoryBackend\db\DbConnFactory();
  $conn_string = $conn_classname::getConnectionString($owner->username, $owner->password);
  $database = $conn_classname::getDatabase();
  $file_name = `${__DIR__}/to_sav.py '${conn_string}' '${database}' '${sim_id}' 2>&1`;
  $file_path = trim($file_name);

  if (file_exists($file_path)) {
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="'.basename($file_path).'"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($file_path));
    readfile($file_path);
    unlink($file_path);
  } else {
    throw new \Exception("Temp file from to_sav.py does not exist!");
  }
}
?>

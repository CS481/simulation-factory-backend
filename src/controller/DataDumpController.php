<?php namespace SimulationFactoryBackend\controller;
require_once __DIR__ . '/../util/validate_simulation_owner.php';

// Creates a .sav file and forces the user's browser to download it
// $conn is an instance of IDBComm
// $owner is the User json that owns the simulation
// $sim_id is the id of the simulation to get the instances of
function download_responses($conn, object $owner, string $sim_id) {
  \SimulationFactoryBackend\util\validate_simulation_owner($conn, $owner, $sim_id);
  $query = (object)['simulation_id' => $sim_id];
  if ($completed_only) {
    $query->turn_number = -1;
  }
  $curs = $conn->select('ResponseRecords', $query);
  $records_array = [];
  foreach ($curs as $record) {
    array_push($records_array, normalize_record($record));
  }
  $record_object->records = $records_array;
  $record_json = str_replace('"', '\"', json_encode($record_object));
  $__DIR__ = __DIR__;
  $file_name = `${__DIR__}/to_sav.py "${record_json}" 2>&1`;
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

// Normalizes a response record for sending to to_sav.py
function normalize_record(object $record) : object {
  // Flatten the resources subobject
  $resources = $record->resources;
  unset($record->resources);
  foreach($resources as $resource => $value) {
    $record->$resource = $value;
  }

  // Unset some ids and stuff
  unset($record->_id);
  unset($record->simulation_id);
  return $record;
}
?>

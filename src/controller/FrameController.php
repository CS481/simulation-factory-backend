<?php namespace SimulationFactoryBackend\controller;
require_once __DIR__ . '/../util/validate_simulation_owner.php';

// Creates a new frame and returns the id of the new frame
// $conn is an instance of IDBConn
// $user is the User json to use to create the frame
// $sim_id is the id of the sim to add the frame to
function initialize_frame($conn, object $user, string $sim_id) : string {
  \SimulationFactoryBackend\util\validate_simulation_owner($conn, $user, $sim_id);
  $new_frame->simulation_id = $sim_id;
  $frame_id = $conn->insert('Frames', $new_frame);
  return $frame_id;
}

// Modifies an existing frame in a simulation
// $conn is an instance of IDBConn
// $user is the User json to use to modify the frame
// $frame_data is the new data to update the frame with
// $frame_id is the id of the frame to modifiy
function modify_frame($conn, object $user, object $frame_data, string $frame_id) {
  $frame_query = (object)['_id' => $frame_id];
  $frame = $conn->selectOne('Frames', $frame_query);
  \SimulationFactoryBackend\util\validate_simulation_owner($conn, $user, $frame->simulation_id);

 // These may be set from the JSON POST data. Best to unset them just in case
  unset($frame_data->user);
  unset($frame_data->frame_id);

  // Some values cannot be allowed to change
  $frame_data->simulation_id = $frame->simulation_id;

  $conn->replace('Frames', $frame_data, $frame_query);
}

// Deletes an existing frame from it's simulation
// $conn is an instance of IDBConn
// $user is the User json to use to modify the frame
// $frame_id is the id of the frame to delete
function delete_frame($conn, object $user, string $frame_id) {
  $frame_query = (object)['_id' => $frame_id];
  $frame = $conn->selectOne('Frames', $frame_query);
  \SimulationFactoryBackend\util\validate_simulation_owner($conn, $user, $frame->simulation_id);

  $conn->delete('Frames', $frame_query);
}
?>

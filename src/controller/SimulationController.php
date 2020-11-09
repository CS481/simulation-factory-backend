<?php namespace SimulationFactoryBackend\controller;
require_once __DIR__ . '/../util/validate_simulation_owner.php';
require_once __DIR__ . '/FrameController.php';

// Creates a new sim and returns the id of the new simulation
// $conn is an instance of IDBComm
// $user is the User json to use to create the simulation
function initialize_sim($conn, object $user) : string {
  $new_sim->username = $user->username;
  $sim_id = $conn->insert('Simulations', $new_sim);

  $frame_id = initialize_frame($conn, $user, $sim_id);
  $default_end_frame = (object)['prompt' => 'This simulation has ended. Thank you for your participation.',
                                'rounds' => [-1],
                                'responses' => []
                               ];
  modify_frame($conn, $user, $default_end_frame, $frame_id);
  return $sim_id;
}

// Modifies an existing simulation
// $conn is an instance of IDBConn
// $user is the User json to use to modify the frame
// $sim_data is the new data to update the simulation with
// $sim_id is the id of the simulation to modifiy
function modify_sim($conn, object $user, object $sim_data, string $sim_id) {
  \SimulationFactoryBackend\util\validate_simulation_owner($conn, $user, $sim_id);

 // These may be set from the JSON POST data. Best to unset them just in case
  unset($frame_data->user);
  unset($frame_data->simulation_id);

  // Some values cannot be allowed to change
  $sim_data->username = $user->username;

  $conn->replace('Simulations', $sim_data, (object)['_id' => $sim_id]);
}
?>

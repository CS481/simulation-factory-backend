<?php namespace SimulationFactory\controller;
// updates a response record and progresses the sim to the next round
// $conn is the IDBConn instance to use
// sim_instance is the simulation instance to use
// response is the response that thte user selected
// cur_user is the user (player1 or player2) which needs to be updated
// other_user is the user (player1 or player2) which has already been inserted into the log
function update_response_record($conn, object $sim_instance, string $response, string $cur_user, string $other_user) {
  if (!(($cur_user == 'player1' && $other_user == 'player2') || ($cur_user == 'player2' && $other_user == 'player1'))) {
    throw new \Exception("One of cur_user and other_user arguments must be equal to 'player1', and the other".
			 "must be equal to 'player2'. Instead got cur_user=${cur_user} and other_user=${other_user}");
  }

  $search_for = (object)['rounds' => $sim_instance->turn_number,
                         'simulation_id' => $sim_instance->simulation_id
                        ];
  $frame = $conn->selectOne('Frames', $search_for);

  $search_for = (object)[$other_user => $sim_instance->$other_user,
                         'simulation_id' => $sim_instance->simulation_id,
                         'round' => $sim_instance->turn_number
                        ];
  $log_entry = $conn->selectOne('ResponseRecords', $search_for);

  $log_update = (object)[$cur_user => $sim_instance->$cur_user,
                         $cur_user.'_response' => $response
                        ];

  foreach((array)($sim_instance->resources) as $resource => $value) {
    if ($cur_user == 'player1') {
      $player1_response_index = get_index($response, $frame->responses);
      $player2_response_key = $other_user.'_response';
      $player2_response_index = get_index($log_entry->$player2_response_key, $frame->responses);
    } else {
      $player2_response_index = get_index($response, $frame->responses);
      $player1_response_key = $other_user.'_response';
      $player1_response_index = get_index($log_entry->$player1_response_key, $frame->responses);
    }

    $sim_instance->resources->$resource += $value * $frame->effects->$resource[$player1_response_index][$player2_response_index];
    $log_update->resources->$resource = $sim_instance->resources->$resource;
  }
  $sim_instance->turn_number++;
  $sim_instance->player1_waiting = false;
  $sim_instance->player2_waiting = false;
  $sim_instance->deadline = time()+$sim_instance->response_timeout;
  $conn->update('ResponseRecords', $log_update, $search_for);
  $query = (object) ['simulation_id' => $sim_instance->simulation_id,
                     $cur_user => $sim_instance->$cur_user
                    ];
  $query->simulation_id = $sim_instance->simulation_id;
  $conn->update('SimulationInstances', $sim_instance, $query);
}

// Creates a new response record, but does not update the sim
// $conn is the IDBConn instance to use
// sim_instance is the simulation instance to use
// response is the response that the user selected
// cur_user is the user (player1 or player2) which responded
function create_response_record($conn, object $sim_instance, string $response, string $cur_user) {
  if ($cur_user != 'player1' && $cur_user != 'player2') {
    throw new \Exception("cur_user must be either 'player1' or 'player2'. Instead, cur_user=$cur_user");
  }

  $insert = (object)[$cur_user => $sim_instance->$cur_user,
                     $cur_user.'_response' => $response,
                     'simulation_id' => $sim_instance->simulation_id,
                     'round' => $sim_instance->turn_number
                    ];
  $conn->insert('ResponseRecords', $insert);
  $user_waiting = $cur_user.'_waiting';
  $sim_instance->$user_waiting = true;
  $query = (object) ['simulation_id' => $sim_instance->simulation_id,
                     $cur_user => $sim_instance->$cur_user
                    ];
  $conn->update('SimulationInstances', $sim_instance, $query);
}

// Gets the index of the first instance of value in the given array
function get_index($value, $arr) {
  foreach($arr as $index => $arr_val) {
    if ($arr_val == $value) {
      return $index;
    }
  }
}
?>

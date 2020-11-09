<?php namespace SimulationFactoryBackend\util;
// Validates that the simulation with the given id is owned by the given user
// $conn is an instance of IDBConn
// $user is the User json to check if it is the owner
// $sim_id is the id of the sim to check the owner of
// Throws a DBOpException if the simulation cannot be found
// Throws a \Exception if $user is not the owner of the simulation
function validate_simulation_owner($conn, object $user, string $sim_id) : object {
  $sim = $conn->selectOne('Simulations', (object)['_id' => $sim_id]);
  if ($sim->username != $user->username) {
    throw new \Exception('This simulation is not owned by this user');
  }
  return $sim;
}
?>

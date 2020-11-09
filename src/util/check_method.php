<?php namespace SimulationFactoryBackend\util;
// Forces new http requests to use the specified method
function only_allow_method(string $method) {
  if ($_SERVER['REQUEST_METHOD'] != $method) {
    header('Method Not Allowed', true, 405);
    echo "Only $method methods are allowed for this page. You used ${_SERVER['REQUEST_METHOD']}";
    exit;
  }
}
?>

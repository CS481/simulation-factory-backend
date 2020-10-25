<?php
// Have to assigned file path to a variable, because it doesn't work if I don't
$mysql_credentials_ini = getenv('MYSQL_HOME') . '/my.cnf';
$mysql_credentials_array = parse_ini_file($mysql_credentials_ini);
if($mysql_credentials_array == FALSE) {
    throw new Exception('Cannot read mysql credentials file');
}
$mysql_conn = new PDO("mysql:host=${mysql_credentials_array['host']};dbname=${mysql_credentials_array['database']}",
                      $mysql_credentials_array['user'], $mysql_credentials_array['password'], array(
                      PDO::ATTR_PERSISTENT => true
));
$mysql_conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
?>

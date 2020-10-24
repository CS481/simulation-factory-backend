# simulation-factory-backend
Simulation Factory Backend files that are not exposed to the www

## mysql_conn
mysql_conn.php exposes a variable name $mysql_conn, which is a persistent PDO connection that can be used to efficiently connect to the database, and resolves the issue of keeping passwords in version control. Connection credentials are read from a cnf file that can also be used for the myql command line application

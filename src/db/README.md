# DB
High level database abstraction layer

## IDBConn
Interface that all DBConns must inherit from

## MongoConn
A mongodb-based implementation of IDBConn. Rather than tables, mongodb uses collections, which are roughly analgous. 
Keep in mind that mongodb is not strict like a SQL database, and as such it will not enforce a schema, and you must be vERY CAREFUL to use the correct collection(table).

Users are not stored in a collection, but rather as mongodb's native users system. 
A "SimFactory" user exists to manage user permissions. The login for this user is stored in a file pointed to by the MONGODB_CREDENTIALS environment variable. 
DO NOT delete this user, modify the credentials file, or modify this user's permissions. Doing so will break the account creation/modification process.
Users are uniquely identified by their username, so duplicate usernames are disallowed. It's antiquated, I know. We can fix it later, but that's just the way that it is right now.

### Using Mongodb shell
In order to login to the mongodb shell, execute the following command: `mongo admin`. Login with the command `db.auth("usr", "pw")`. Note that the superuser login credentials are the same as the MYSQL database
The database we are using is named 'SimulationFactory'. Switch to this database with the command `use SimulationFactory`.  

Note that the mongodb shell is not quite like a SQL shell, although the concept is similar. Here's a few notes to help you understand how to use it.
- Shell quick reference https://docs.mongodb.com/manual/reference/mongo-shell/
- Use `show collections`  to list the collections, and `show users` to list the users.
- When the documentations says `db.collection` for a method signature, use `db.{collection_name}` to perform that action on that collection

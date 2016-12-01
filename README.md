PHP PDO Database Class
============================

A database class for PHP-MySQL which uses the PDO extension.

Everytime an exception is thrown by the database class a log file gets created or modified.

The log file is a simple plain text file with the current date('year-month-day') as filename.

## Examples

#### Insert Data

```php
<?php
$db = DB_Class::getInstance();
$db->bind("name", $name);
$db->bind("email", $email);
$db->bind("password", $password);
$db->query("INSERT INTO users (name, email, password) VALUES (:name, :email, :password)");

// Get last insert Id
$id = $db->lastInsertId();
```

#### Fetch single row
```php
<?php
$db->bind("id", $id);
$db->query("SELECT * FROM users WHERE id = :id");
$result = $db->single();
```
#### Fetch All
```php
<?php
$db->bind("active", 1);
$db->query("SELECT * FROM users WHERE is_active: active");
$result = $db->resultset();
```

More about PDO : http://php.net/manual/en/book.pdo.php

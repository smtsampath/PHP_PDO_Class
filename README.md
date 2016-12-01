PHP PDO Database Class
============================

A database class for PHP-MySQL which uses the PDO extension.

## Examples

#### Insert Data

```php

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
$db = DB_Class::getInstance();
$db->bind("id", $id);
$db->query("SELECT * FROM users WHERE id = :id");
$result = $db->single();
```
#### Fetch All
```php
$db = DB_Class::getInstance();
$db->bind("active", 1);
$db->query("SELECT * FROM users WHERE is_active: active");
$results = $db->resultset();
```

More about PDO : http://php.net/manual/en/book.pdo.php

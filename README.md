# EasyPDO
Simplest database wrapper for PDO that makes coding more fun.

## How to use
To use the class, establish a PDO connection the usual way and pass it to the EasyPDO class.
```php
<?php

use Wildhoof\EasyPDO;

$pdo = ...
$db = new EasyPDO($pdo);
```

### Select, using prepared statements

```php
$result = $db->query('SELECT * FROM `users` WHERE `id` = :id')
    ->bind('id', PDO::PARAM_INT, 1)
    ->fetch(PDO::FETCH_ASSOC);
```

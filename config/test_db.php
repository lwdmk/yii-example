<?php
$db = require __DIR__ . '/db.php';
$db['dsn'] = 'pgsql:host=localhost;dbname=example_test';

return $db;

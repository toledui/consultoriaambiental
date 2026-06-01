<?php
$config = require __DIR__ . '/config/database.php';
$pdo = new PDO('mysql:host='.$config['host'].';dbname='.$config['dbname'].';charset='.$config['charset'], $config['username'], $config['password']);
$r = $pdo->query("SELECT `value` FROM settings WHERE `key`='brand_logo'");
echo "brand_logo in DB: " . $r->fetchColumn() . "\n";

// Test URL
$val = $r->fetchColumn();
echo "Test URL: http://localhost:8000/" . $val . "\n";

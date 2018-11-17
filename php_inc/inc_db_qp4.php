<?php
$host = "localhost";
$user = "root";
$passwd = "4,M?}km<P;";
$DBName = "dubarub";
$dsn = "mysql:host=$host;dbname=$DBName";

$opt = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
	$conn = new PDO($dsn, $user, $passwd, $opt);
} catch (PDOException $e) {
	echo "Connection Failed: " . $e->getMessage();
	$conn = null;
} 
?>
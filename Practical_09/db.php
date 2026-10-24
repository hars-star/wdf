<?php
/**
 * StudentHub - Practical 8: Database Connection Library (PDO & MySQLi)
 */

$db_host = '127.0.0.1';
$db_user = 'root';
$db_pass = '';
$db_name = 'student_portal';
$db_port = 3306;

// 1. PDO Connection Function
function getPDOConnection() {
    global $db_host, $db_user, $db_pass, $db_name, $db_port;
    $dsn = "mysql:host={$db_host};port={$db_port};dbname={$db_name};charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    try {
        return new PDO($dsn, $db_user, $db_pass, $options);
    } catch (PDOException $e) {
        die("PDO Database Connection Failed: " . $e->getMessage());
    }
}

// 2. MySQLi Connection Function
function getMySQLiConnection() {
    global $db_host, $db_user, $db_pass, $db_name, $db_port;
    $mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name, $db_port);
    if ($mysqli->connect_error) {
        die("MySQLi Database Connection Failed: " . $mysqli->connect_error);
    }
    $mysqli->set_charset("utf8mb4");
    return $mysqli;
}

<?php
require_once 'db_connect.php';
$res = db_query("DESCRIBE settings");
$schema = [];
while($row = $res->fetch_assoc()) $schema[] = $row;
echo json_encode($schema, JSON_PRETTY_PRINT);

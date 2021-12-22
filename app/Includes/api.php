<?php

namespace App\Includes;

header('Content-Type: application/json; charset=utf8');

require 'config.php';
$sql = "SELECT id, sensor, value, UNIX_TIMESTAMP(reading_time) as ts FROM sensordata order by reading_time";
// $query = mysqli_query($db, $sql);
// $result = mysqli_fetch_all($query, MYSQLI_ASSOC);
// json_encode(array_column($result, 'count'), JSON_NUMERIC_CHECK);
$sth = mysqli_query($db, $sql);
$rows = array();
while ($r = mysqli_fetch_assoc($sth)) {
    $rows[] = $r;
}
$res = $rows;
// print json_encode($rows);

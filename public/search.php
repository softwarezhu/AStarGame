<?php

require '../lib/astar.php';
$contents = trim($_POST['map']);

$lines = explode("\n", $contents);
$line = $lines[0];
// Map data
$chars = explode(' ', $line);
$h = $chars[0];
$w = $chars[1];

$map = array_slice($lines, 1, $h);
$startTime = microtime(true);
$aStar = new AStar($map);
$point = $aStar->find(0, 0, $w-1, $h-1);
if ($point) {

    $path = $aStar->getPath($point);

    $result = [];
    while(!$path->isEmpty()) {
        $node = $path->pop();
        $result[] = $node->toJson();
    }

    echo json_encode([
        'status' => 1,
        'msg' => '',
        'memory' => memory_get_usage(true),
        'elapsed' => microtime(true) - $startTime,
        'data' => $result,
    ]);
} else {
    echo json_encode([
        'status' => 0,
        'memory' => memory_get_usage(true),
        'elapsed' => microtime(true) - $startTime,
        'msg' => ''
    ]);
}


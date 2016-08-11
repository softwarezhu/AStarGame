<?php

require '../lib/astar.php';

$fileName = $argv[1];

$contents = file_get_contents($fileName);

$lines = explode("\r\n", $contents);
$idx = 0;
while ($idx < count($lines))
{
    $line = $lines[$idx];
    // Map data
    $chars = str_split($line);
    $h = $chars[0];
    $w = $chars[2];

    $map = array_slice($lines, $idx+1, $h);
    $idx += $h+1; 

    $aStar = new AStar($map);
    $point = $aStar->find(0, 0, $w-1, $h-1);
    if ($point) {
        echo 'It takes ' . $point->f . ' seconds to reach the target position. Let me show you the way' . PHP_EOL;

        $path = $aStar->getPath($point);

        while(!$path->isEmpty()) {
            $node = $path->pop();
            echo $node->x . ' ' . $node->y . PHP_EOL;
        }

    } else {
        echo 'God please help our poor hero. ' . PHP_EOL;
    }

    echo 'FINISH.' . PHP_EOL;

}


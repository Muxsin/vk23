<?php
include 'Map.php';

$startCell = [0, 0];
$targetCell = [2, 3];

$map = new Map([
    [1, 4, 3, 0],
    [2, 0, 1, 2],
    [1, 3, 5, 1],
]);

$checkList = $map->createCheckList();
$checkList[$startCell[0]][$startCell[1]] = 1;

$buffer = [$startCell];

$mapPrices = $map->createMapPrice();
$mapPrices[$startCell[0]][$startCell[1]] = 0;

$i = 0;
while($i < count($buffer)) {
    foreach($buffer as $item) {
        $canGoCells = $map->canGoTo($item[0], $item[1]);
        
        foreach($canGoCells as $cell) {
            if ($checkList[$cell[0]][$cell[1]] === 0) {
                $checkList[$cell[0]][$cell[1]] = 1;
                $buffer[] = [$cell[0], $cell[1]];
            }

            if ($mapPrices[$cell[0]][$cell[1]] === -1) {
                $mapPrices[$cell[0]][$cell[1]] = $mapPrices[$item[0]][$item[1]] + $map->getCellValue($cell[0], $cell[1]);
            } else {
                $mapPrices[$cell[0]][$cell[1]] = min($mapPrices[$cell[0]][$cell[1]], $mapPrices[$item[0]][$item[1]] + $map->getCellValue($cell[0], $cell[1]));
            }
        }
    }

    $i++;
}

if ($mapPrices[$targetCell[0]][$targetCell[1]] === -1) {
    echo "No way" . PHP_EOL;
} else {
    echo "Самый быстрый путь: " . $mapPrices[$targetCell[0]][$targetCell[1]] . PHP_EOL;

    $i = $targetCell[0];
    $j = $targetCell[1];
    $path = [[$i, $j]];

    while($mapPrices[$i][$j] !== 0) {
        $canGoCells = $map->canGoTo($i, $j);
        $goTo = $mapPrices[$i][$j] - $map->getCellValue($i, $j);

        foreach($canGoCells as $cell) {
            if ($goTo === $mapPrices[$cell[0]][$cell[1]]) {
                $i = $cell[0];
                $j = $cell[1];
                $path[] = [$i, $j];
            }
        }
    }

    foreach(array_reverse($path) as $item) {
        echo "$item[0] $item[1]";
    }
}
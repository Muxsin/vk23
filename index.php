<?php

if (isset($_POST['submit'])) {
    include 'Map.php';

    $data = explode("\n", str_replace("\r", "", $_REQUEST['map']));
    $map_data = [];

    for ($i = 0; $i < count($data); $i++) {
        $map_data[] = array_map('intval', str_split($data[$i]));
    }

    $startCell = [intval($_REQUEST['startCellX']), intval($_REQUEST['startCellY'])];
    $targetCell = [intval($_REQUEST['targetCellX']), intval($_REQUEST['targetCellY'])];

    $map = new Map($map_data);

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
        $result = '<div class="alert alert-info" role="alert">Путь ненайден!</div>';
    } else {
        $result = '<div class="alert alert-success shadow-sm" role="alert">Самый быстрый путь: ' 
            . $mapPrices[$targetCell[0]][$targetCell[1]] 
            . '</div>'
        ;

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

        for ($i = 0; $i < $map->rowsCount(); $i++) {
            $result .= "<div class='row'>";
            
            for ($j = 0; $j < $map->colsCount(); $j++) {
                if ([$i, $j] === $startCell) {
                    $result .= '<div class="m-1 p-2 rounded shadow-sm col bg-primary">' . $map->getCellValue($i, $j) . '</div>';  
                } else if ([$i, $j] === $targetCell) {
                    $result .= '<div class="m-1 p-2 rounded shadow-sm col bg-danger">' . $map->getCellValue($i, $j) . '</div>'; 
                } else if ($map->getCellValue($i, $j) == 0) {
                    $result .= '<div class="m-1 p-2 rounded shadow-sm col bg-black">' . $map->getCellValue($i, $j) . '</div>'; 
                } else if (in_array([$i, $j], $path)) {
                    $result .= '<div class="m-1 p-2 rounded shadow-sm col bg-success">' . $map->getCellValue($i, $j) . '</div>';    
                } else {
                    $result .= '<div class="m-1 p-2 rounded shadow-sm col bg-secondary">' . $map->getCellValue($i, $j) . '</div>';
                }
            }            
            
            $result .= "</div>";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <title>VK</title>
</head>
<body>
    <div class="container">
        <div class="row gap-3">
            <form class="p-4 shadow-sm rounded col" method="POST">
                <h3>Найдите кратчайший путь из одной точки в другую</h3>

                <div class="form-floating my-2">
                    <textarea name="map" class="form-control" placeholder="Вводите структура лабиринта" id="map" style="height: 100px"></textarea>
                    <label for="map">Структура лабиринта</label>
                </div>

                <div class="row">
                    <div class="col">
                        <label class="form-label">Начальные координаты</label>
                        <div class="mb-2">
                            <input type="text" name="startCellX" class="form-control" placeholder="X" aria-label="X">
                        </div>
                        <div class="mb-2">
                            <input type="text" name="startCellY" class="form-control" placeholder="Y" aria-label="Y">
                        </div>
                    </div>
                    
                    <div class="col">
                        <label class="form-label">Конечные координаты</label>
                        <div class="mb-2">
                            <input type="text" name="targetCellX" class="form-control" placeholder="X" aria-label="X">
                        </div>
                        <div class="mb-2">
                            <input type="text" name="targetCellY" class="form-control" placeholder="Y" aria-label="Y">
                        </div>
                    </div>
                </div>

                <div class="mt-2 col-12">
                    <button type="submit" name="submit" class="btn btn-primary">Найти</button>
                </div>
            </form>

            <div class="col">
                <?php
                    echo $result;
                ?>
            </div>
        </div>
    </div>
</body>
</html>

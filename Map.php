<?php

class Map {
    protected array $map;

    public function __construct(array $map)
    {
        $this->map = $map;
    }

    public function getMap(): array 
    {
        return $this->map;
    }

    public function setMap(array $map): self 
    {
        $this->map = $map;

        return $this;
    }

    public function rowsCount(): int
    {
        return count($this->map);
    }

    public function colsCount(): int
    {
        return count($this->map[0]);
    }

    public function isCellAvailable(int $i, int $j): bool
    {
        if ($this->rowsCount() <= $i 
            || $this->colsCount() <= $j 
            || $i < 0 
            || $j < 0
            || $this->map[$i][$j] === 0
        ) {
            return false; 
        }

        return true;
    }

    public function getCellValue(int $i, int $j) 
    {
        if($this->isCellAvailable($i, $j)) {
            return $this->map[$i][$j];
        }
    }

    public function canGoTo(int $i, int $j): array
    {
        if ($this->isCellAvailable($i, $j)) {
            $points = [];

            if ($this->isCellAvailable($i, $j + 1)) {
                $points[] = [$i, $j + 1];
            }

            if ($this->isCellAvailable($i + 1, $j)) {
                $points[] = [$i + 1, $j];
            }

            if ($this->isCellAvailable($i, $j - 1)) {
                $points[] = [$i, $j - 1];
            }

            if ($this->isCellAvailable($i - 1, $j)) {
                $points[] = [$i - 1, $j];
            }

            return $points;
        }
    }

    public function createCheckList(): array
    {
        $checkList = [];

        for($i = 0; $i < $this->rowsCount(); $i++) {
            for($j = 0; $j < $this->colsCount(); $j++) {
                $checkList[$i][$j] = 0;
            }
        }

        return $checkList;
    }

    public function createMapPrice(): array
    {
        $mapPrice = [];

        for($i = 0; $i < $this->rowsCount(); $i++) {
            for($j = 0; $j < $this->colsCount(); $j++) {
                $mapPrice[$i][$j] = -1;
            }
        }

        return $mapPrice;
    }
}

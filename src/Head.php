<?php

namespace Synopsis\Csv;

/**
 * Trida pro praci s hlavickou csv souboru
 * @author Lukas Brzobohaty
 */
class Head extends Row {

    /**
     * Vrati index sloupecku s danym jmenem v hlavicce
     * @param string $name
     * @return int
     * @throws OutOfRangeException
     */
    public function getIndex($name) {
        $index = array_search($name, $this->toArray());
        if ($index === FALSE) {
            throw new OutOfRangeException("Cannot read an undeclared column \"$name\".");
        }

        return $index;
    }

}

<?php

namespace Synopsis\Csv;

/**
 * Trida pro praci s radkem csv souboru
 * @author Lukas Brzobohaty
 */
class Row extends \stdClass implements \ArrayAccess, \Countable, \IteratorAggregate {

    const REPLACE_DELIMITER = '$DELIMITER$';
    const REPLACE_ENCLOSURE = '$ENCLOSURE$';
    const REPLACE_ESCAPE = '$ESCAPE$';
    const REPLACE_LINE_DELIMITER = '$LINE_DELIMITER$';

    /** @var Head */
    private $header;    // Objekt hlavicky

    /** @var array */
    private $row;

    /** @var string */
    private $rawRow;

    /**
     *
     * @param string $row
     * @param Head $header - objekt hlavicky, nebo null
     */
    public function __construct($row, $header) {
        $this->rawRow = $row;
        $this->header = $header;
    }

    public function __get($name) {
        if (!is_null($this->header)) {
            return $this->offsetGet($this->header->getIndex($name));
        }

        return NULL;
    }

    public function __set($name, $value) {
        if (!is_null($this->header)) {
            $this->offsetSet($this->header->getIndex($name), $value);
        }
    }

    /**
     * Rozparsovani radku a zpetne nahrazeni specialnich znaku
     * @param string $delimiter
     * @param string $enclosure
     * @param string $escape
     * @param string $lineDelimiter
     */
    public function parseRow($delimiter, $enclosure, $escape, $lineDelimiter) {
        $this->row = explode($delimiter, $this->rawRow);
        foreach ($this->row as $key => $column) {
            if (strlen($column) > 0 && $column[0] == $enclosure && $column[strlen($column) - 1] == $enclosure) {
                preg_match(sprintf('/%s(.*)%s/', $enclosure, $enclosure), $column, $matches);
                $column = $matches[1];
            }

            $column = preg_replace(sprintf('/%s+/', $enclosure), $enclosure, $column);
            $column = $this->replaceDelimiter($column, $delimiter);
            $column = $this->replaceEscapeEnclosure($column, $enclosure, $escape);
            $column = $this->replaceLineDelimiter($column, $lineDelimiter);
            $this->row[$key] = $column;
        }
    }

    /**
     * Nahrazeni specialniho znaku oddelovace za puvodni
     * @param string $string
     * @param string $delimiter
     * @return string
     */
    private function replaceDelimiter($string, $delimiter) {
        return str_replace(self::REPLACE_DELIMITER, $delimiter, $string);
    }

    /**
     * Nahrazeni specialniho znaku uvozovek za puvodni
     * @param string $string
     * @param string $enclosure
     * @param string $escape
     * @return string
     */
    private function replaceEscapeEnclosure($string, $enclosure, $escape) {
        return str_replace(self::REPLACE_ESCAPE . self::REPLACE_ENCLOSURE, $escape . $enclosure, $string);
    }

    /**
     * Nahrazeni specialniho znaku odradkovani za puvodni
     * @param string $string
     * @param string $lineDelimiter
     * @return string
     */
    private function replaceLineDelimiter($string, $lineDelimiter) {
        return str_replace(self::REPLACE_LINE_DELIMITER, $lineDelimiter, $string);
    }

    /**
     * Returns an iterator over all items.
     * @return \RecursiveArrayIterator
     */
    public function getIterator() {
        return new \RecursiveArrayIterator($this->row);
    }


    /**
     * Returns items count.
     * @return int
     */
    public function count() {
        return count((array)$this->row);
    }

    /**
     * Replaces or appends a item.
     * @return void
     * @throws InvalidArgumentException
     */
    public function offsetSet($key, $value) {
        if (!is_scalar($key)) { // prevents NULL
            throw new InvalidArgumentException(sprintf('Key must be either a string or an integer, %s given.', gettype($key)));
        }
        $this->row[$key] = $value;
    }


    /**
     * Returns a item.
     * @return mixed
     * @throws InvalidArgumentException
     * @throws OutOfRangeException
     */
    public function offsetGet($key) {
        if (!is_scalar($key)) { // prevents NULL
            throw new InvalidArgumentException(sprintf('Key must be either a string or an integer, %s given.', gettype($key)));
        }
        if ($this->offsetExists($key)) {
            return $this->row[$key];
        } else {
            throw new OutOfRangeException("Cannot read an undeclared column \"$key\".");
        }
    }


    /**
     * Determines whether a item exists.
     * @return bool
     * @throws InvalidArgumentException
     */
    public function offsetExists($key) {
        if (!is_scalar($key)) { // prevents NULL
            throw new InvalidArgumentException(sprintf('Key must be either a string or an integer, %s given.', gettype($key)));
        }

        return isset($this->row[$key]);
    }


    /**
     * Removes the element from this list.
     * @return void
     * @throws InvalidArgumentException
     */
    public function offsetUnset($key) {
        if (!is_scalar($key)) { // prevents NULL
            throw new InvalidArgumentException(sprintf('Key must be either a string or an integer, %s given.', gettype($key)));
        }
        if ($this->offsetExists($key)) {
            unset($this->row[$key]);
        }
    }

    /**
     * Vrati radek jako pole
     * @return array
     */
    public function toArray() {
        return $this->row;
    }

    /**
     * Vrati objekt hlavicky
     * @return Head
     */
    public function getHeader() {
        return $this->header;
    }
}

<?php

namespace Synopsis\Csv;

/**
 * Trida pro parsovani csv souboru
 * @author Lukas Brzobohaty
 */
class Csv extends \stdClass implements \ArrayAccess, \Countable, \IteratorAggregate {

    const REPLACE_DELIMITER = '$DELIMITER$';
    const REPLACE_ENCLOSURE = '$ENCLOSURE$';
    const REPLACE_ESCAPE = '$ESCAPE$';
    const REPLACE_LINE_DELIMITER = '$LINE_DELIMITER$';

    /** @var string The field delimiter */
    private $delimiter;

    /** @var string The enclosure character */
    private $enclosure;

    /** @var string The escape character */
    private $escape;

    /** @var string The line delimiter */
    private $lineDelimiter;

    /** @var string The encoding input */
    private $inputEncoding;

    /** @var string The encoding output */
    private $outputEncoding;

    /** @var null|Head */
    private $header = NULL;

    /** @var Row[] */
    private $rows = array();

    /**
     * Csv constructor.
     * @param string $delimiter - The field delimiter
     * @param string $enclosure - The enclosure character
     * @param string $escape - The escape character
     * @param string $lineDelimiter - The line delimiter
     * @param string $inputEncoding - The encoding input
     * @param string $outputEncoding - The encoding output
     */
    public function __construct(
        $delimiter = ',',
        $enclosure = '"',
        $escape = '\\',
        $lineDelimiter = "\n",
        $inputEncoding = 'utf8',
        $outputEncoding = 'utf8'
    ) {
        $this->delimiter = $delimiter;
        $this->enclosure = $enclosure;
        $this->escape = $escape;
        $this->lineDelimiter = $lineDelimiter;
        $this->inputEncoding = $inputEncoding;
        $this->outputEncoding = $outputEncoding;
    }

    /**
     * Set the field delimiter
     * @param string $delimiter
     */
    public function setDelimiter($delimiter) {
        $this->delimiter = $delimiter;
    }

    /**
     * Get the field delimiter
     * @return string
     */
    public function getDelimiter() {
        return $this->delimiter;
    }

    /**
     * Set the enclosure character
     * @param string $enclosure
     */
    public function setEnclosure($enclosure) {
        $this->enclosure = $enclosure;
    }

    /**
     * Get the enclosure character
     * @return string
     */
    public function getEnclosure() {
        return $this->enclosure;
    }

    /**
     * Set the escape character
     * @param string $escape
     */
    public function setEscape($escape) {
        $this->escape = $escape;
    }

    /**
     * Get the escape character
     * @return string
     */
    public function getEscape() {
        return $this->escape;
    }

    /**
     * Set the line delimiter
     * @param string $delimiter
     */
    public function setLineDelimiter($delimiter) {
        $this->lineDelimiter = $delimiter;
    }

    /**
     * Get the line delimiter
     * @return string
     */
    public function getLineDelimiter() {
        return $this->lineDelimiter;
    }

    /**
     * Set the encoding input
     * @param $encoding
     */
    public function setInputEncoding($encoding) {
        $this->inputEncoding = $encoding;
    }

    /**
     * Get the encoding input
     * @return string
     */
    public function getInputEncoding() {
        return $this->inputEncoding;
    }

    /**
     * Set the encoding output
     * @param $encoding
     */
    public function setOutputEncoding($encoding) {
        $this->outputEncoding = $encoding;
    }

    /**
     * Get the encoding output
     * @return string
     */
    public function getOutputEncoding() {
        return $this->outputEncoding;
    }

    /**
     * Rozparsovani souboru do dvourozmerneho pole
     * @param string $string - vstupni csv retezec
     * @param boolean $header - prvni radek hlavicka
     */
    public function parseString($string, $header = TRUE) {
        $string = iconv($this->inputEncoding, $this->outputEncoding, $string);
        $string = $this->replaceEscapeEnclosure($string);
        $string = $this->replaceDelimiterEnclosured($string);
        $string = $this->replaceLineDelimiterEnclosured($string);

        $lines = explode($this->lineDelimiter, $string);

        // rozparsovani hlavicky
        if ($header) {
            $this->header = new Head($lines[0], NULL);
            $this->header->parseRow($this->delimiter, $this->enclosure, $this->escape, $this->lineDelimiter);
            array_shift($lines);
        }

        // rozparsovani jednotlivych radku
        foreach ($lines as $line) {
            if ($line == '') {
                continue;
            }
            $row = new Row($line, $this->header);
            $row->parseRow($this->delimiter, $this->enclosure, $this->escape, $this->lineDelimiter);
            $this->rows[] = $row;
        }
    }

    /**
     * Rozparsovani souboru do dvourozmerneho pole
     * @param string $filename - nazev souboru
     * @param boolean $header - prvni radek hlavicka
     */
    public function parseFile($filename, $header = TRUE) {
        $this->parseString(file_get_contents($filename), $header);
    }

    /**
     * Nahrazeni uvozovek vnorenych do ohranicujicich uvozovek
     */
    public function replaceEscapeEnclosure($string) {
        $escape = $this->escape;
        $enclosure = $this->enclosure;
        $replace = self::REPLACE_ESCAPE . self::REPLACE_ENCLOSURE;

        return preg_replace_callback(sprintf('/\%s.*[^\%s\%s%s]+\%s/Us', $enclosure, $escape, $escape, $enclosure, $enclosure), function ($array) use ($escape, $enclosure, $replace) {
            return str_replace(sprintf("%s%s", $escape, $enclosure), $replace, $array[0]);
        }, $string);
    }

    /**
     * Nahrazeni oddelovace vnorenych do ohranicujicich uvozovek
     */
    public function replaceDelimiterEnclosured($string) {
        $delimiter = $this->delimiter;
        $replace = self::REPLACE_DELIMITER;

        return preg_replace_callback(sprintf('/\%s.*\%s/Us', $this->enclosure, $this->enclosure), function ($array) use ($delimiter, $replace) {
            return str_replace($delimiter, $replace, $array[0]);
        }, $string);
    }

    /**
     * Nahrazeni odradkovani vnorenych do ohranicujicich uvozovek
     */
    public function replaceLineDelimiterEnclosured($string) {
        $lineDelimiter = $this->lineDelimiter;
        $replace = self::REPLACE_LINE_DELIMITER;

        return preg_replace_callback(sprintf('/\%s.*\%s/Us', $this->enclosure, $this->enclosure), function ($array) use ($lineDelimiter, $replace) {
            return str_replace($lineDelimiter, $replace, $array[0]);
        }, $string);
    }

    /**
     * Returns header
     * @return null|Head
     */
    public function getHeader() {
        return $this->header;
    }

    /**
     * Returns all rows
     * @return Row[]
     */
    public function getRows() {
        return $this->rows;
    }

    /**
     * Returns an iterator over all items.
     * @return \RecursiveArrayIterator
     */
    public function getIterator() {
        return new \RecursiveArrayIterator($this->rows);
    }


    /**
     * Returns items count.
     * @return int
     */
    public function count() {
        return count((array)$this->rows);
    }

    /**
     * Replaces or appends a item.
     * @throws NotImplementedException
     */
    public function offsetSet($key, $value) {
        throw new NotImplementedException('Method offsetSet not implemented');
    }


    /**
     * Returns a item.
     * @return Row
     * @throws InvalidArgumentException
     * @throws OutOfRangeException
     */
    public function offsetGet($key) {
        if (!is_scalar($key)) { // prevents NULL
            throw new InvalidArgumentException(sprintf('Key must be either a string or an integer, %s given.', gettype($key)));
        }
        if ($this->offsetExists($key)) {
            return $this->rows[$key];
        } else {
            throw new OutOfRangeException("Cannot read an undeclared row \"$key\".");
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

        return isset($this->rows[$key]);
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
            unset($this->rows[$key]);
        }
    }

    /**
     * Vrati csv jako dvourozmerne pole
     * @return array
     */
    public function toArray() {
        $array = array();
        foreach ($this->rows as $row) {
            $array[] = $row->toArray();
        }

        return $array;
    }
}

class CsvException extends \Exception {

}

class NotImplementedException extends CsvException {

}

class InvalidArgumentException extends CsvException {

}

class OutOfRangeException extends CsvException {

}

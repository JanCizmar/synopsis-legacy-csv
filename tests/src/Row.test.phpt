<?php

namespace Synopsis\Csv;

require __DIR__ . '/../bootstrap.php';

use Tester\Assert;
use Tester\TestCase;

/**
 * Class RowTest
 * @package Synopsis\Csv
 */
class RowTest extends TestCase {

    /**
     * @var Csv
     */
    protected $csv;
    /**
     * @var Row[]
     */
    protected $objects;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() {
        $this->csv = new Csv();

        // nacteni radku bez hlavicky
        foreach ($this->getDataConstruct() as $string) {
            $string = $this->csv->replaceEscapeEnclosure($string);
            $string = $this->csv->replaceDelimiterEnclosured($string);
            $string = $this->csv->replaceLineDelimiterEnclosured($string);

            $lines = explode($this->csv->getLineDelimiter(), $string);

            foreach ($lines as $line) {
                $this->objects[] = new Row($line, NULL);
            }
        }

        // nacteni radku s hlavickou
        foreach ($this->getDataConstructWithHead() as $string) {
            $string = $this->csv->replaceEscapeEnclosure($string);
            $string = $this->csv->replaceDelimiterEnclosured($string);
            $string = $this->csv->replaceLineDelimiterEnclosured($string);

            $lines = explode($this->csv->getLineDelimiter(), $string);

            $head = new Head($lines[0], NULL);
            $head->parseRow($this->csv->getDelimiter(), $this->csv->getEnclosure(), $this->csv->getEscape(), $this->csv->getLineDelimiter());
            array_shift($lines);

            foreach ($lines as $line) {
                $this->objects[] = new Row($line, $head);
            }
        }
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown() {
        $this->csv = NULL;
        $this->objects = array();
    }

    /**
     * @dataProvider getDataTest__get
     * @covers       Row::__get
     */
    public function test__get($name, $value) {
        foreach ($this->objects as $object) {
            $object->parseRow($this->csv->getDelimiter(), $this->csv->getEnclosure(), $this->csv->getEscape(), $this->csv->getLineDelimiter());
            if (!is_null($object->getHeader())) {
                Assert::equal($value, $object->$name);
            } else {
                Assert::null($object->$name);
            }
        }
    }

    /**
     * @dataProvider getDataTest__set
     * @covers       Row::__set
     */
    public function test__set($name, $value) {
        foreach ($this->objects as $object) {
            if (!is_null($object->getHeader())) {
                $object->parseRow($this->csv->getDelimiter(), $this->csv->getEnclosure(), $this->csv->getEscape(), $this->csv->getLineDelimiter());
                $object->$name = $value;
                Assert::equal($value, $object->$name);
            }
        }
    }

    /**
     * @covers Row::parseRow
     */
    public function testParseRow() {
        foreach ($this->objects as $object) {
            $object->parseRow($this->csv->getDelimiter(), $this->csv->getEnclosure(), $this->csv->getEscape(), $this->csv->getLineDelimiter());
            Assert::true(is_array($object->toArray()));
        }
    }

    /**
     * @covers Row::getIterator
     */
    public function testGetIterator() {
        foreach ($this->objects as $object) {
            $object->parseRow($this->csv->getDelimiter(), $this->csv->getEnclosure(), $this->csv->getEscape(), $this->csv->getLineDelimiter());
            foreach ($object as $item) {
                Assert::true(is_string($item));
            }
        }
    }

    /**
     * @dataProvider getDataTestCount
     * @covers       Row::count
     */
    public function testCount($count) {
        foreach ($this->objects as $object) {
            $object->parseRow($this->csv->getDelimiter(), $this->csv->getEnclosure(), $this->csv->getEscape(), $this->csv->getLineDelimiter());
            Assert::equal($count, $object->count());
        }
    }

    /**
     * @dataProvider getDataTestOffsetSet
     * @covers       Row::offsetSet
     */
    public function testOffsetSet($key, $value) {
        foreach ($this->objects as $object) {
            $object->offsetSet($key, $value);
            Assert::equal($value, $object->offsetGet($key));
        }
    }

    /**
     * @dataProvider getDataTestOffsetSet
     * @covers       Row::offsetSet
     */
    public function testOffsetSetNull($key, $value) {
        foreach ($this->objects as $object) {
            Assert::exception(function () use ($object, $value) {
                $object->offsetSet(NULL, $value);
            }, 'Synopsis\Csv\InvalidArgumentException');
        }
    }

    /**
     * @dataProvider getDataTestOffsetGet
     * @covers       Row::offsetGet
     */
    public function testOffsetGet($key, $value) {
        foreach ($this->objects as $object) {
            $object->parseRow($this->csv->getDelimiter(), $this->csv->getEnclosure(), $this->csv->getEscape(), $this->csv->getLineDelimiter());
            Assert::equal($value, $object->offsetGet($key));
        }
    }

    /**
     * @dataProvider getDataTestOffsetGet
     * @covers       Row::offsetGet
     */
    public function testOffsetGetNull($key, $value) {
        foreach ($this->objects as $object) {
            $object->parseRow($this->csv->getDelimiter(), $this->csv->getEnclosure(), $this->csv->getEscape(), $this->csv->getLineDelimiter());
            Assert::exception(function () use ($object) {
                $object->offsetGet(NULL);
            }, 'Synopsis\Csv\InvalidArgumentException');
        }
    }

    /**
     * @dataProvider getDataTestOffsetGet
     * @covers       Row::offsetGet
     */
    public function testOffsetGetUndeclared($key, $value) {
        foreach ($this->objects as $object) {
            $object->parseRow($this->csv->getDelimiter(), $this->csv->getEnclosure(), $this->csv->getEscape(), $this->csv->getLineDelimiter());
            Assert::exception(function () use ($object) {
                $object->offsetGet(-1);
            }, 'Synopsis\Csv\OutOfRangeException');
        }
    }

    /**
     * @dataProvider getDataTestOffsetExistsUnset
     * @covers       Row::offsetExists
     */
    public function testOffsetExists($key) {
        foreach ($this->objects as $object) {
            $object->parseRow($this->csv->getDelimiter(), $this->csv->getEnclosure(), $this->csv->getEscape(), $this->csv->getLineDelimiter());
            Assert::true($object->offsetExists($key));
        }
    }

    /**
     * @dataProvider getDataTestOffsetExistsUnset
     * @covers       Row::offsetExists
     */
    public function testOffsetExistsNull($key) {
        foreach ($this->objects as $object) {
            $object->parseRow($this->csv->getDelimiter(), $this->csv->getEnclosure(), $this->csv->getEscape(), $this->csv->getLineDelimiter());
            Assert::exception(function () use ($object) {
                $object->offsetExists(NULL);
            }, 'Synopsis\Csv\InvalidArgumentException');
        }
    }

    /**
     * @dataProvider getDataTestOffsetExistsUnset
     * @covers       Row::offsetUnset
     */
    public function testOffsetUnset($key) {
        foreach ($this->objects as $object) {
            $object->parseRow($this->csv->getDelimiter(), $this->csv->getEnclosure(), $this->csv->getEscape(), $this->csv->getLineDelimiter());
            $object->offsetUnset($key);
            Assert::true(!$object->offsetExists($key));
        }
    }

    /**
     * @dataProvider getDataTestOffsetExistsUnset
     * @covers       Row::offsetUnset
     */
    public function testOffsetUnsetNull($key) {
        foreach ($this->objects as $object) {
            $object->parseRow($this->csv->getDelimiter(), $this->csv->getEnclosure(), $this->csv->getEscape(), $this->csv->getLineDelimiter());
            Assert::exception(function () use ($object) {
                $object->offsetUnset(NULL);
            }, 'Synopsis\Csv\InvalidArgumentException');
        }
    }

    /**
     * @covers Row::toArray
     */
    public function testToArray() {
        foreach ($this->objects as $object) {
            $object->parseRow($this->csv->getDelimiter(), $this->csv->getEnclosure(), $this->csv->getEscape(), $this->csv->getLineDelimiter());
            Assert::true(is_array($object->toArray()));
        }
    }

    /**
     * Data pro konstruktor
     * @return array
     */
    public function getDataConstruct() {
        return array(
            "aaa,\"bbb\",\"c c c\",\"d,d,d\",\"e\ne\ne\"",
        );
    }

    /**
     * Data pro konstruktor s hlavickou
     * @return array
     */
    public function getDataConstructWithHead() {
        return array(
            "a,b,c,d,e\n"
            . "aaa,\"bbb\",\"c c c\",\"d,d,d\",\"e\ne\ne\"",
        );
    }

    /**
     * Data pro test__get()
     * @return array
     */
    public function getDataTest__get() {
        return array(
            array("a", "aaa"),          // nazev sloupce, ocekavana hodnota
            array("b", "bbb"),
            array("c", "c c c"),
            array("d", "d,d,d"),
            array("e", "e\ne\ne"),
        );
    }

    /**
     * Data pro test__set()
     * @return array
     */
    public function getDataTest__set() {
        return array(
            array("a", "a"),            // nazev sloupce, nastavovana hodnota
            array("b", "b"),
            array("c", "c"),
            array("d", "d"),
            array("e", "e"),
        );
    }

    /**
     * Data pro testCount()
     * @return array
     */
    public function getDataTestCount() {
        return array(
            array(5)                    // ocekavany pocet sloupcu
        );
    }

    /**
     * Data pro testOffsetSet()
     * @return array
     */
    public function getDataTestOffsetSet() {
        return array(
            array(0, "a"),              // cislo sloupce, hodnota
            array(1, "b"),
            array(2, "c"),
            array(3, "d"),
            array(4, "e"),
        );
    }

    /**
     * Data pro testOffsetGet()
     * @return array
     */
    public function getDataTestOffsetGet() {
        return array(
            array(0, "aaa"),            // cislo sloupce, ocekavana hodnota
            array(1, "bbb"),
            array(2, "c c c"),
            array(3, "d,d,d"),
            array(4, "e\ne\ne"),
        );
    }

    /**
     * Data pro testOffsetExists() a testOffsetUnset()
     * @return array
     */
    public function getDataTestOffsetExistsUnset() {
        return array(
            array(0),                   // cislo sloupce
            array(1),
            array(2),
            array(3),
            array(4),
        );
    }
}

$testCase = new RowTest;
$testCase->run();
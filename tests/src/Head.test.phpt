<?php

namespace Synopsis\Csv;

require __DIR__ . '/../bootstrap.php';

use Tester\Assert;
use Tester\TestCase;

/**
 * Class HeadTest
 * @package Synopsis\Csv
 */
class HeadTest extends TestCase {

    /**
     * @var Csv
     */
    protected $csv;
    /**
     * @var Head[]
     */
    protected $objects;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() {
        $this->csv = new Csv();
        foreach ($this->getDataConstruct() as $string) {
            $string = $this->csv->replaceEscapeEnclosure($string);
            $string = $this->csv->replaceDelimiterEnclosured($string);
            $string = $this->csv->replaceLineDelimiterEnclosured($string);

            $this->objects[] = new Head($string, NULL);
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
     * @dataProvider getDataTestGetIndex
     * @covers       Head::getIndex
     */
    public function testGetIndex($name, $index) {
        foreach ($this->objects as $object) {
            $object->parseRow($this->csv->getDelimiter(), $this->csv->getEnclosure(), $this->csv->getEscape(), $this->csv->getLineDelimiter());
            Assert::equal($index, $object->getIndex($name));
        }
    }

    /**
     * @dataProvider getDataTestGetIndexFalse
     * @covers       Head::getIndex
     */
    public function testGetIndexFalse($name) {
        foreach ($this->objects as $object) {
            $object->parseRow($this->csv->getDelimiter(), $this->csv->getEnclosure(), $this->csv->getEscape(), $this->csv->getLineDelimiter());
            Assert::exception(function () use ($object, $name) {
                $object->getIndex($name);
            }, 'Synopsis\Csv\OutOfRangeException');
        }
    }

    /**
     * Data pro konstruktor
     * @return array
     */
    public function getDataConstruct() {
        return array(
            'aaa,"bbb","c c c","d,d,d","e\ne\ne"',
        );
    }

    /**
     * Data pro testGetIndex()
     * @return array
     */
    public function getDataTestGetIndex() {
        return array(
            array('aaa', 0),
            array('bbb', 1),
            array('c c c', 2),
            array('d,d,d', 3),
            array('e\ne\ne', 4),
        );
    }

    /**
     * Data pro testGetIndexFalse()
     * @return array
     */
    public function getDataTestGetIndexFalse() {
        return array(
            array('aa'),
            array('bb'),
            array('c c'),
            array('d,d'),
            array('e\ne'),
        );
    }

}

$testCase = new HeadTest;
$testCase->run();
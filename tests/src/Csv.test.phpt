<?php

namespace Synopsis\Csv;

require __DIR__ . '/../bootstrap.php';

use Tester\Assert;
use Tester\TestCase;

/**
 * Class CsvTest
 * @package Synopsis\Csv
 */
class CsvTest extends TestCase {

    /**
     * @var Csv
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() {
        $this->object = new Csv();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown() {
        $this->object = NULL;
    }

    /**
     * @covers Csv::setDelimiter
     */
    public function testSetDelimiter() {
        $this->object->setDelimiter(';');
        Assert::equal(';', $this->object->getDelimiter());
    }

    /**
     * @covers Csv::getDelimiter
     */
    public function testGetDelimiter() {
        Assert::equal(',', $this->object->getDelimiter());
    }

    /**
     * @covers Csv::setEnclosure
     */
    public function testSetEnclosure() {
        $this->object->setEnclosure('\'');
        Assert::equal('\'', $this->object->getEnclosure());
    }

    /**
     * @covers Csv::getEnclosure
     */
    public function testGetEnclosure() {
        Assert::equal('"', $this->object->getEnclosure());
    }

    /**
     * @covers Csv::setEscape
     */
    public function testSetEscape() {
        $this->object->setEscape('/');
        Assert::equal('/', $this->object->getEscape());
    }

    /**
     * @covers Csv::getEscape
     */
    public function testGetEscape() {
        Assert::equal('\\', $this->object->getEscape());
    }

    /**
     * @covers Csv::setLineDelimiter
     */
    public function testSetLineDelimiter() {
        $this->object->setLineDelimiter("\c");
        Assert::equal("\c", $this->object->getLineDelimiter());
    }

    /**
     * @covers Csv::getLineDelimiter
     */
    public function testGetLineDelimiter() {
        Assert::equal("\n", $this->object->getLineDelimiter());
    }

    /**
     * @cover Csv::setInputEncoding
     */
    public function testSetInputEncoding() {
        $this->object->setInputEncoding('cp1250');
        Assert::equal('cp1250', $this->object->getInputEncoding());
    }

    /**
     * @covers Csv::getInputEncoding
     */
    public function testGetInputEncoding() {
        Assert::equal('utf8', $this->object->getInputEncoding());
    }

    /**
     * @cover Csv::setInputEncoding
     */
    public function testSetOutputEncoding() {
        $this->object->setOutputEncoding('cp1250');
        Assert::equal('cp1250', $this->object->getOutputEncoding());
    }

    /**
     * @covers Csv::getOutputEncoding
     */
    public function testGetOutputEncoding() {
        Assert::equal('utf8', $this->object->getOutputEncoding());
    }

    /**
     * @dataProvider getDataTestParseString
     * @covers       Csv::parseString
     */
    public function testParseString($string, $result) {
        $this->object->parseString($string, TRUE);
        Assert::equal($result, $this->object->offsetGet(0)->toArray());
    }

    /**
     * @dataProvider getDataTestParseFile
     * @covers       Csv::parseFile
     */
    public function testParseFile($file, $countRows, $countCols, $delimiter, $enclosure, $escape, $lineDelimiter, $inputEncoding, $outputEncoding) {
        $this->object->setDelimiter($delimiter);
        $this->object->setEnclosure($enclosure);
        $this->object->setEscape($escape);
        $this->object->setLineDelimiter($lineDelimiter);
        $this->object->setInputEncoding($inputEncoding);
        $this->object->setOutputEncoding($outputEncoding);
        $this->object->parseFile($file, TRUE);
        Assert::equal($countRows, $this->object->count());
    }

    /**
     * @dataProvider getDataTestParseFile
     * @covers       Csv::parseFile
     */
    public function testParseFile2($file, $countRows, $countCols, $delimiter, $enclosure, $escape, $lineDelimiter, $inputEncoding, $outputEncoding) {
        $this->object->setDelimiter($delimiter);
        $this->object->setEnclosure($enclosure);
        $this->object->setEscape($escape);
        $this->object->setLineDelimiter($lineDelimiter);
        $this->object->setInputEncoding($inputEncoding);
        $this->object->setOutputEncoding($outputEncoding);
        $this->object->parseFile($file, TRUE);
        foreach ($this->object as $row) {
            Assert::equal($countCols, $row->count());
        }
    }

    /**
     * @dataProvider getDataTestReplaceEscapeEnclosure
     * @covers       Csv::replaceEscapeEnclosure
     */
    public function testReplaceEscapeEnclosure($string, $result) {
        Assert::equal($result, $this->object->replaceEscapeEnclosure($string));
    }

    /**
     * @dataProvider getDataTestReplaceDelimiterEnclosured
     * @covers       Csv::replaceDelimiterEnclosured
     */
    public function testReplaceDelimiterEnclosured($string, $result) {
        Assert::equal($result, $this->object->replaceDelimiterEnclosured($string));
    }

    /**
     * @dataProvider getDataTestReplaceLineDelimiterEnclosured
     * @covers       Csv::replaceLineDelimiterEnclosured
     */
    public function testReplaceLineDelimiterEnclosured($string, $result) {
        Assert::equal($result, $this->object->replaceLineDelimiterEnclosured($string));
    }

    /**
     * @dataProvider getDataTestParseString
     * @covers       Csv::getHeader
     */
    public function testGetHeader($string) {
        $this->object->parseString($string, TRUE);
        Assert::type('Synopsis\Csv\Head', $this->object->getHeader());
    }

    /**
     * @dataProvider getDataTestParseString
     * @covers       Csv::getRows
     */
    public function testGetRows($string) {
        $this->object->parseString($string, TRUE);
        foreach ($this->object->getRows() as $row) {
            Assert::type('Synopsis\Csv\Row', $row);
        }
    }

    /**
     * @dataProvider getDataTestParseString
     * @covers       Csv::getIterator
     */
    public function testGetIterator($string) {
        $this->object->parseString($string);
        foreach ($this->object as $item) {
            Assert::type('Synopsis\Csv\Row', $item);
        }
    }

    /**
     * @dataProvider getDataTestCount
     * @covers       Csv::count
     */
    public function testCount($string, $result) {
        $this->object->parseString($string);
        Assert::equal($result, $this->object->count());
    }

    /**
     * @covers Csv::offsetSet
     */
    public function testOffsetSet() {
        Assert::exception(function () {
            $this->object->offsetSet('a', 1);
        }, 'Synopsis\Csv\NotImplementedException');
    }

    /**
     * @dataProvider getDataTestOffset
     * @covers       Csv::offsetGet
     */
    public function testOffsetGet($string, $key) {
        $this->object->parseString($string);
        Assert::type('Synopsis\Csv\Row', $this->object->offsetGet($key));
    }

    /**
     * @dataProvider getDataTestOffset
     * @covers       Csv::offsetGet
     */
    public function testOffsetGetNull($string) {
        $this->object->parseString($string);
        Assert::exception(function () {
            $this->object->offsetGet(NULL);
        }, 'Synopsis\Csv\InvalidArgumentException');
    }

    /**
     * @dataProvider getDataTestOffset
     * @covers       Csv::offsetGet
     */
    public function testOffsetGetUndeclared($string) {
        $this->object->parseString($string);
        Assert::exception(function () {
            $this->object->offsetGet(-1);
        }, 'Synopsis\Csv\OutOfRangeException');
    }

    /**
     * @dataProvider getDataTestOffset
     * @covers       Csv::offsetExists
     */
    public function testOffsetExists($string, $key) {
        $this->object->parseString($string);
        Assert::true($this->object->offsetExists($key));
    }

    /**
     * @dataProvider getDataTestOffset
     * @covers       Csv::offsetExists
     */
    public function testOffsetExistsNull($string) {
        $this->object->parseString($string);
        Assert::exception(function () {
            $this->object->offsetExists(NULL);
        }, 'Synopsis\Csv\InvalidArgumentException');
    }

    /**
     * @dataProvider getDataTestOffset
     * @covers       Csv::offsetUnset
     */
    public function testOffsetUnset($string, $key) {
        $this->object->parseString($string);
        $this->object->offsetUnset($key);
        Assert::false($this->object->offsetExists($key));
    }

    /**
     * @dataProvider getDataTestOffset
     * @covers       Csv::offsetUnset
     */
    public function testOffsetUnsetNull($string) {
        $this->object->parseString($string);
        Assert::exception(function () {
            $this->object->offsetUnset(NULL);
        }, 'Synopsis\Csv\InvalidArgumentException');
    }

    /**
     * @dataProvider getDataTestParseString
     * @covers       Csv::toArray
     */
    public function testToArray($string) {
        $this->object->parseString($string);
        Assert::true(is_array($this->object->toArray()));
    }

    /**
     * Data pro testParseString() a ostatni funkce pouzivajici funkci parseString
     * @return array
     */
    public function getDataTestParseString() {
        return array(
            array(
                "a,b,c,d,e\n"
                . "aaa,\"bbb\",\"c c c\",\"d,d,d\",\"e\ne\ne\"",
                array("aaa", "bbb", "c c c", "d,d,d", "e\ne\ne"),
            ),       // csv string (s hlavickou)
            array(
                "kod;nazev;popis;mj;cena\n"
                .
                "326012000,PL60/50NHK,\"trubkový motor 50 Nm s NHK - ? 45 mm, mechanické koncové spínače, komplet s unašečem a boční konzolou do hřídele RT60\",ks,3699",
                array(
                    "326012000", "PL60/50NHK",
                    "trubkový motor 50 Nm s NHK - ? 45 mm, mechanické koncové spínače, komplet s unašečem a boční konzolou do hřídele RT60",
                    "ks", "3699",
                ),
            ),
            array(
                "kod,nazev,popis,mj,cena\n"
                . "303006119,ESR/77-119,\"zámkový profil zlatý dub pro pancíř \"\"za\"\"\",bm,248.52",
                array("303006119", "ESR/77-119", "zámkový profil zlatý dub pro pancíř \"za\"", "bm", "248.52"),
            ),
        );
    }


    public function getDataTestParseFile() {
        return array(
            array('input/test1.csv', 138, 5, ';', '"', '\\', "\n", 'cp1250', 'utf8'),
            array('input/test2.csv', 224, 5, ';', '"', '\\', "\n", 'cp1250', 'utf8'),
            array('input/test3.csv', 6, 5, ';', '"', '\\', "\n", 'cp1250', 'utf8'),
            array('input/test4.csv', 1744, 4, ';', '"', '\\', "\n", 'utf8', 'utf8'),
        );
    }

    /**
     * Data pro testCount()
     * @return array
     */
    public function getDataTestCount() {
        return array(
            array(
                "a,b,c,d,e\n"
                . "aaa,\"bbb\",\"c c c\",\"d,d,d\",\"e\ne\ne\"", 1,
            )       // csv string (s hlavickou), pocet radku
        );
    }

    /**
     * Data pro testOffsetSet(), testOffsetGet(), testOffsetExists(), testOffsetUnset()
     * @return array
     */
    public function getDataTestOffset() {
        return array(
            array(
                "a,b,c,d,e\n"
                . "aaa,\"bbb\",\"c c c\",\"d,d,d\",\"e\ne\ne\"", 0,
            )       // csv string (s hlavickou), cislo radku
        );
    }

    /**
     * Data pro testReplaceEscapeEnclosure()
     * @return array
     */
    public function getDataTestReplaceEscapeEnclosure() {
        return array(
            array(
                '"aa\"aa\"aa","b\"b\"b"',
                '"aa$ESCAPE$$ENCLOSURE$aa$ESCAPE$$ENCLOSURE$aa","b$ESCAPE$$ENCLOSURE$b$ESCAPE$$ENCLOSURE$b"',
            )   // csv string, ocekavana hodnota
        );
    }

    /**
     * Data pro testReplaceDelimiterEnclosured()
     * @return array
     */
    public function getDataTestReplaceDelimiterEnclosured() {
        return array(
            array("\"aa,aa,aa\",\"b,b,b\"", '"aa$DELIMITER$aa$DELIMITER$aa","b$DELIMITER$b$DELIMITER$b"')
            // csv string, ocekavana hodnota
        );
    }

    /**
     * Data pro testReplaceLineDelimiterEnclosured()
     * @return array
     */
    public function getDataTestReplaceLineDelimiterEnclosured() {
        return array(
            array(
                "\"aa\naa\naa\",\"b\nb\nb\"",
                '"aa$LINE_DELIMITER$aa$LINE_DELIMITER$aa","b$LINE_DELIMITER$b$LINE_DELIMITER$b"',
            )   // csv string, ocekavana hodnota
        );
    }

}

$testCase = new CsvTest;
$testCase->run();
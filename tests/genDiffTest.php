<?php

/**
 * Tests for program which compare two files
 *
 * PHP version 7.3
 *
 * @category PHP
 * @package  Php-project-lvl2
 * @author   Rinat Salimyanov <rinatsin@gmail.com>
 * @license  https://github.com/Rinatsin/php-project-lvl2 MIT
 * @link     https://github.com/Rinatsin/php-project-lvl2
 */

namespace Differ\Tests;

use PHPUnit\Framework\TestCase;

use function Differ\Formatters\getJsonFormatOutput;
use function Differ\Formatters\getPlainFormatOutput;
use function Differ\Formatters\getPrettyFormatOutput;
use function Differ\Formatters\renderTreeToJson;
use function Differ\genDiff;
use function Differ\getAst;
use function Differ\Parsers\getParsedData;

/**
 * Class includes tests for program gendiff
 *
 * @category PHP
 * @package  Php-project-lvl2
 * @author   Rinat Salimyanov <rinatsin@gmail.com>
 * @license  https://github.com/Rinatsin/php-project-lvl2 MIT
 * @link     https://github.com/Rinatsin/php-project-lvl2
 */
class DifferTest extends TestCase
{
    /**
     * Method test function genDiff with Json files
     *
     * @return string
     */
    public function testDiffForSimpleJsonFiles()
    {
        $pathToFile1 = __DIR__ . '/fixtures/before.json';
        $pathToFile2 = __DIR__ . '/fixtures/after.json';
        $format = 'pretty';

        $expected = genDiff($pathToFile1, $pathToFile2, $format);
        $actual = file_get_contents(__DIR__ . '/fixtures/pretty_simple_result');

        $this->assertEquals($expected, $actual);
    }

    /**
     * Method test function genDiff with Yaml files
     *
     * @return string
     */
    public function testDiffForSimpleYmlFiles()
    {
        $pathToFile1 = __DIR__ . '/fixtures/before.yml';
        $pathToFile2 = __DIR__ . '/fixtures/after.yml';
        $format = 'pretty';

        $expected = genDiff($pathToFile1, $pathToFile2, $format);
        $actual = file_get_contents(__DIR__ . '/fixtures/pretty_simple_result');

        $this->assertEquals($expected, $actual);
    }

    /**
     * Method test function genDiff with Yaml files
     *
     * @return string
     */
    public function testInnerView()
    {
        $pathToFile1 = __DIR__ . '/fixtures/beforeTree.json';
        $pathToFile2 = __DIR__ . '/fixtures/afterTree.json';
        $parsedData1 = getParsedData($pathToFile1);
        $parsedData2 = getParsedData($pathToFile2);

        $expected = getAst($parsedData1, $parsedData2);
        $actual = [
            [
                "name" => "common",
                "state" => "changed",
                "type" => "node",
                'value' => '',
                "children" => [
                    [
                        "name" => "setting1",
                        "state" => "  ",
                        "type" => "leaf",
                        "value" => "Value 1",
                        "children" => []
                    ],
                    [
                        "name" => "setting2",
                        "state" => "- ",
                        "type" => "leaf",
                        "value" => "200",
                        "children" => []
                    ],
                    [
                        "name" => "setting3",
                        "state" => "  ",
                        "type" => "leaf",
                        "value" => true,
                        "children" => []
                    ],
                    [
                        "name" => "setting6",
                        "state" => "- ",
                        "type" => "node",
                        "value" => [
                            "key" => "value"
                        ],
                        "children" => []
                    ],
                    [
                        "name" => "setting4",
                        "state" => "+ ",
                        "type" => "leaf",
                        "value" => "blah blah",
                        "children" => []
                    ],
                    [
                        "name" => "setting5",
                        "state" => "+ ",
                        "type" => "node",
                        "value" => [
                            "key5" => "value5"
                        ],
                        "children" => []
                    ]
                ]
            ],
            [
                "name" => "group1",
                "state" => "changed",
                "type" => "node",
                'value' => '',
                "children" => [
                    [
                        "name" => "baz",
                        "state" => "+ ",
                        "type" => "leaf",
                        "value" => "bars",
                        "children" => []
                    ],
                    [
                        "name" => "baz",
                        "state" => "- ",
                        "type" => "leaf",
                        "value" => "bas",
                        "children" => []
                    ],
                    [
                        "name" => "foo",
                        "state" => "  ",
                        "type" => "leaf",
                        "value" => "bar",
                        "children" => []
                    ]
                ]
                ],
                [
                    "name" => "group2",
                    "state" => "- ",
                    "type" => "node",
                    "value" => [
                        "abc" => "12345"
                    ],
                    "children" => []
                ],
                [
                    "name" => "group3",
                    "state" => "+ ",
                    "type" => "node",
                    "value" => [
                        "fee" => "100500"
                    ],
                    "children" => []
                ]
        ];

        $this->assertEquals($expected, $actual);
    }

    /**
     * Method test function renderAst
     *
     * @return void
     */
    public function testPrettyFormatRender()
    {
        $pathToFile1 = __DIR__ . '/fixtures/beforeTree.json';
        $pathToFile2 = __DIR__ . '/fixtures/afterTree.json';
        $format = 'pretty';

        $actual = file_get_contents(__DIR__ . '/fixtures/pretty_nested_result');
        $expected = genDiff($pathToFile1, $pathToFile2, $format);
        
        $this->assertEquals($expected, $actual);
    }

    /**
     * Method test function renderAst
     *
     * @return void
     */
    public function testPlainFormatterWithNestedStructure()
    {
        $pathToFile1 = __DIR__ . '/fixtures/beforeTree.json';
        $pathToFile2 = __DIR__ . '/fixtures/afterTree.json';
        $format = 'plain';

        $actual = file_get_contents(__DIR__ . '/fixtures/plain_nested_result');
        $expected = genDiff($pathToFile1, $pathToFile2, $format);

        $this->assertEquals($expected, $actual);
    }

    /**
     * Method test simple files for plain format output
     *
     * @return void
     */
    public function testPlainFormatterWithSimpleFiles()
    {
        $pathToFile1 = __DIR__ . '/fixtures/before.yml';
        $pathToFile2 = __DIR__ . '/fixtures/after.yml';
        $format = 'plain';

        $actual = file_get_contents(__DIR__ . '/fixtures/plain_simple_result');
        $expected = genDiff($pathToFile1, $pathToFile2, $format);

        $this->assertEquals($expected, $actual);
    }

    /**
     * Method test diff output between two files in json format
     *
     * @return void
     */
    public function testJsonFormatOutput()
    {
        $pathToFile1 = __DIR__ . '/fixtures/beforeTree.json';
        $pathToFile2 = __DIR__ . '/fixtures/afterTree.json';
        $parsedData1 = getParsedData($pathToFile1);
        $parsedData2 = getParsedData($pathToFile2);

        $ast = getAst($parsedData1, $parsedData2);
        $expected = getJsonFormatOutput($ast);
        $actual = file_get_contents(__DIR__ . '/fixtures/result_tree.json');

        $this->assertEquals($expected, $actual);
    }
}

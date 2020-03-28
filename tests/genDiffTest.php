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

use ErrorException;
use PHPUnit\Framework\TestCase;

use function Differ\buildAst;
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
     * Method test function which build AST
     *
     * @return array
     */
    public function testInnerView()
    {
        $pathToFile1 = __DIR__ . '/fixtures/beforeTree.json';
        $pathToFile2 = __DIR__ . '/fixtures/afterTree.json';
        $pathParts1 = pathinfo($pathToFile1);
        $pathParts2 = pathinfo($pathToFile2);
        $dataFromFile1 = file_get_contents($pathToFile1);
        $dataFromFile2 = file_get_contents($pathToFile2);
        $parsedData1 = getParsedData($dataFromFile1, $pathParts1['extension']);
        $parsedData2 = getParsedData($dataFromFile2, $pathParts2['extension']);


        $expected = buildAst($parsedData1, $parsedData2);
        $actual = [
            [
                "name" => "common",
                "state" => "changed",
                "type" => "node",
                'value' => '',
                "children" => [
                    [
                        "name" => "setting1",
                        "state" => "not_change",
                        "type" => "leaf",
                        "value" => "Value 1",
                        "children" => []
                    ],
                    [
                        "name" => "setting2",
                        "state" => "deleted",
                        "type" => "leaf",
                        "value" => "200",
                        "children" => []
                    ],
                    [
                        "name" => "setting3",
                        "state" => "not_change",
                        "type" => "leaf",
                        "value" => true,
                        "children" => []
                    ],
                    [
                        "name" => "setting6",
                        "state" => "deleted",
                        "type" => "node",
                        "value" => [
                            "key" => "value"
                        ],
                        "children" => []
                    ],
                    [
                        "name" => "setting4",
                        "state" => "added",
                        "type" => "leaf",
                        "value" => "blah blah",
                        "children" => []
                    ],
                    [
                        "name" => "setting5",
                        "state" => "added",
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
                        "state" => "added",
                        "type" => "leaf",
                        "value" => "bars",
                        "children" => []
                    ],
                    [
                        "name" => "baz",
                        "state" => "deleted",
                        "type" => "leaf",
                        "value" => "bas",
                        "children" => []
                    ],
                    [
                        "name" => "foo",
                        "state" => "not_change",
                        "type" => "leaf",
                        "value" => "bar",
                        "children" => []
                    ]
                ]
                ],
                [
                    "name" => "group2",
                    "state" => "deleted",
                    "type" => "node",
                    "value" => [
                        "abc" => "12345"
                    ],
                    "children" => []
                ],
                [
                    "name" => "group3",
                    "state" => "added",
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
     * @dataProvider additionProvider
     */
    public function testFormatters($actual, $pathToFile1, $pathToFile2, $format)
    {
        $this->assertEquals($actual, genDiff($pathToFile1, $pathToFile2, $format));
    }

    /**
     * Provide data to test
     * 
     * @return void
     */
    public function additionProvider()
    {
        $ActualPretty = file_get_contents(__DIR__ . '/fixtures/pretty_nested_result');
        $ActualPlain = file_get_contents(__DIR__ . '/fixtures/plain_nested_result');
        $ActualJson = file_get_contents(__DIR__ . '/fixtures/result_tree.json');
        $pathToFile1 = __DIR__ . '/fixtures/beforeTree.json';
        $pathToFile2 = __DIR__ . '/fixtures/afterTree.json';
        return [
            [$ActualPretty, $pathToFile1, $pathToFile2, 'pretty'],
            [$ActualPlain, $pathToFile1, $pathToFile2, 'plain'],
            [$ActualJson, $pathToFile1, $pathToFile2, 'json'],
        ];
    }

    /**
     * Function test wrong output format
     *
     * @return void
     */
    public function testWrongOutputFormat()
    {
        $pathToFile1 = __DIR__ . '/fixtures/beforeTree.json';
        $pathToFile2 = __DIR__ . '/fixtures/afterTree.json';
        $format = 'txt';

        $this->expectExceptionMessage("Unknown output format: {$format}");
        genDiff($pathToFile1, $pathToFile2, $format);
    }

    /**
     * Function test wrong extension
     *
     * @return void
     */
    public function testWrongDataType()
    {
        $pathToFile1 = __DIR__ . '/fixtures/wrong_extension.txt';
        $pathToFile2 = __DIR__ . '/fixtures/afterTree.json';
        $format = 'plain';

        $this->expectException(ErrorException::class);
        genDiff($pathToFile1, $pathToFile2, $format);
    }
}

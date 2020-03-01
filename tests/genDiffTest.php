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

use function Differ\genDiff;
use function Differ\getAst;
use function Differ\getRenderingData;
use function Differ\Parsers\parse;

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
    public function testGenDiff()
    {
        $pathToFile1 = __DIR__ . '/fixtures/before.json';
        $pathToFile2 = __DIR__ . '/fixtures/after.json';

        $expected = genDiff($pathToFile1, $pathToFile2);
        $actual = file_get_contents(__DIR__ . '/fixtures/plain_result.txt');

        $this->assertEquals($expected, $actual);
    }

    /**
     * Method test function genDiff with Yaml files
     *
     * @return string
     */
    public function testGenDiffYaml()
    {
        $pathToFile1 = __DIR__ . '/fixtures/before.yml';
        $pathToFile2 = __DIR__ . '/fixtures/after.yml';

        $expected = genDiff($pathToFile1, $pathToFile2);
        $actual = file_get_contents(__DIR__ . '/fixtures/plain_result.txt');

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
        $parsedData1 = parse($pathToFile1);
        $parsedData2 = parse($pathToFile2);

        $expected = getAst($parsedData1, $parsedData2);
        $actual = [
            [
                "name" => "common",
                "state" => "changed",
                "type" => "node",
                "children" => [
                    [
                        "name" => "setting1",
                        "state" => "  ",
                        "type" => "leaf",
                        "value" => "Value 1"
                    ],
                    [
                        "name" => "setting2",
                        "state" => "- ",
                        "type" => "leaf",
                        "value" => "200"
                    ],
                    [
                        "name" => "setting3",
                        "state" => "  ",
                        "type" => "leaf",
                        "value" => true
                    ],
                    [
                        "name" => "setting6",
                        "state" => "- ",
                        "type" => "node",
                        "value" => [
                            "key" => "value"
                        ]
                    ],
                    [
                        "name" => "setting4",
                        "state" => "+ ",
                        "type" => "leaf",
                        "value" => "blah blah"
                    ],
                    [
                        "name" => "setting5",
                        "state" => "+ ",
                        "type" => "node",
                        "value" => [
                            "key5" => "value5"
                        ]
                    ]
                ]
            ],
            [
                "name" => "group1",
                "state" => "changed",
                "type" => "node",
                "children" => [
                    [
                        "name" => "baz",
                        "state" => "+ ",
                        "type" => "leaf",
                        "value" => "bars"
                    ],
                    [
                        "name" => "baz",
                        "state" => "- ",
                        "type" => "leaf",
                        "value" => "bas"
                    ],
                    [
                        "name" => "foo",
                        "state" => "  ",
                        "type" => "leaf",
                        "value" => "bar"
                    ]
                ]
                ],
                [
                    "name" => "group2",
                    "state" => "- ",
                    "type" => "node",
                    "value" => [
                        "abc" => "12345"
                    ]
                ],
                [
                    "name" => "group3",
                    "state" => "+ ",
                    "type" => "node",
                    "value" => [
                        "fee" => "100500"
                    ]
                ]
        ];

        $this->assertEquals($expected, $actual);
    }

    /**
     * Method test function renderAst 
     *
     * @return void
     */
    public function testRender()
    {
        $pathToFile1 = __DIR__ . '/fixtures/beforeTree.json';
        $pathToFile2 = __DIR__ . '/fixtures/afterTree.json';
        $parsedData1 = parse($pathToFile1);
        $parsedData2 = parse($pathToFile2);

        $tree = getAst($parsedData1, $parsedData2);
        $actual = file_get_contents(__DIR__ . '/fixtures/treeResult.txt');
        $expected = getRenderingData($tree);
        
        $this->assertEquals($expected, $actual);
    }
}

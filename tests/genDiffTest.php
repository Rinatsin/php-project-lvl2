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
        $format = 'pretty';

        $expected = genDiff($pathToFile1, $pathToFile2, $format);
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
        $format = 'pretty';

        $expected = genDiff($pathToFile1, $pathToFile2, $format);
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
        $parsedData1 = getParsedData($pathToFile1);
        $parsedData2 = getParsedData($pathToFile2);

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
        $parsedData1 = getParsedData($pathToFile1);
        $parsedData2 = getParsedData($pathToFile2);

        $tree = getAst($parsedData1, $parsedData2);
        $actual = file_get_contents(__DIR__ . '/fixtures/treeResult.txt');
        $expected = getPrettyFormatOutput($tree);
        
        $this->assertEquals($expected, $actual);
    }

    /**
     * Method test function renderAst
     *
     * @return void
     */
    public function testPlainFormatter()
    {
        $pathToFile1 = __DIR__ . '/fixtures/beforeTree.json';
        $pathToFile2 = __DIR__ . '/fixtures/afterTree.json';
        $parsedData1 = getParsedData($pathToFile1);
        $parsedData2 = getParsedData($pathToFile2);

        $tree = getAst($parsedData1, $parsedData2);

        $actual = "Property 'common.setting2' was removed
Property 'common.setting6' was removed
Property 'common.setting4' was added with value: 'blah blah'
Property 'common.setting5' was added with value: 'complex value'
Property 'group1.baz' was changed. From 'bas' to 'bars'
Property 'group2' was removed
Property 'group3' was added with value: 'complex value'
";

        $expected = getPlainFormatOutput($tree);

        $this->assertEquals($expected, $actual);
    }

    /**
     * Method test yml files
     *
     * @return void
     */
    public function testPlainFormatterWithSimpleYmlFiles()
    {
        $pathToFile1 = __DIR__ . '/fixtures/before.yml';
        $pathToFile2 = __DIR__ . '/fixtures/after.yml';
        $parsedData1 = getParsedData($pathToFile1);
        $parsedData2 = getParsedData($pathToFile2);

        $tree = getAst($parsedData1, $parsedData2);
        $actual = "Property 'timeout' was changed. From '50' to '20'
Property 'proxy' was removed
Property 'verbose' was added with value: 'true'
";

        $expected = getPlainFormatOutput($tree);
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

    /**
     * Function test rendered ast for json format output
     *
     * @return void
     */
    public function testRenderedTreeForJsonFormatter()
    {
        $pathToFile1 = __DIR__ . '/fixtures/beforeTree.json';
        $pathToFile2 = __DIR__ . '/fixtures/afterTree.json';
        $parsedData1 = getParsedData($pathToFile1);
        $parsedData2 = getParsedData($pathToFile2);

        $ast = getAst($parsedData1, $parsedData2);
        $expected = renderTreeToJson($ast);
        $actual = [
                ["common" => [
                    ["setting1" => "Value 1", "state" => "no changed"],
                    ["setting2" => "200", "state" => "deleted"],
                    ["setting3" => true, "state" => "no changed"],
                    ["setting6" => ["key" => "value"], "state" => "deleted"],
                    ["setting4" => "blah blah", "state" => "added"],
                    ["setting5" => ["key5" => "value5"], "state" => "added"],
                ]],
                ["group1" => [
                    ["baz" => "bars", "state" => "added"],
                    ["baz" => "bas", "state" => "deleted"],
                    ["foo" => "bar", "state" => "no changed"],
                ]],
                ["group2" => ["abc" => "12345"],
                "state" => "deleted"],
                ["group3" => ["fee" => "100500"],
                "state" => "added"]
        ];
        $this->assertEquals($expected, $actual);
    }
}

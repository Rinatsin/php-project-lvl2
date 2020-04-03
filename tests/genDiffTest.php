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
}

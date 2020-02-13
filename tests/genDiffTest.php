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
}

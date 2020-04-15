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

use function Differ\buildAstTree;
use function Differ\genDiff;
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
     * Function build path to file
     *
     * @param string $filename name of file
     *
     * @return string path to file
     */
    public function getFixturePath($filename)
    {
        $path = __DIR__ . '/fixtures/' . $filename;
        return $path;
    }
    
    /**
     * Function read file
     *
     * @param string $filename name of file
     *
     * @return string data from file
     */
    public function readFile($filename)
    {
        $data = file_get_contents($this->getFixturePath($filename));
        return $data;
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
        $ActualPretty = $this->readFile('pretty_nested_result');
        $ActualPlain = $this->readFile('plain_nested_result');
        $ActualJson = $this->readFile('json_nested_result.json');
        $pathToFile1 = __DIR__ . '/fixtures/beforeTree.json';
        $pathToFile2 = __DIR__ . '/fixtures/afterTree.json';
        return [
            [$ActualPretty, $pathToFile1, $pathToFile2, 'pretty'],
            [$ActualPlain, $pathToFile1, $pathToFile2, 'plain'],
            [$ActualJson, $pathToFile1, $pathToFile2, 'json'],
        ];
    }
}

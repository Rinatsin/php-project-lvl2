<?php

/**
 * Program compare two files and return their difference
 *
 * PHP version 7.3
 *
 * @category PHP
 * @package  Php-project-lvl2
 * @author   Rinat Salimyanov <rinatsin@gmail.com>
 * @license  https://github.com/Rinatsin/php-project-lvl2 MIT
 * @link     https://github.com/Rinatsin/php-project-lvl2
 */

namespace Differ;

use Error;
use ErrorException;

use function Differ\Formatters\getJsonFormatOutput;
use function Differ\Formatters\getPlainFormatOutput;
use function Differ\Formatters\getPrettyFormatOutput;
use function Differ\Parsers\getParsedData;

/**
 * Function compare two files and return their difference
 *
 * @param string $pathToFile1 file to compare one
 * @param string $pathToFile2 file to compare two
 * @param string $format      file format
 *
 * @return string
 */
function genDiff($pathToFile1, $pathToFile2, $format)
{
    //Получаем данные из файлов$
    $pathParts1 = pathinfo($pathToFile1);
    $pathParts2 = pathinfo($pathToFile2);
    $dataFromFile1 = file_get_contents($pathToFile1);
    $dataFromFile2 = file_get_contents($pathToFile2);
    $parsedData1 = getParsedData($dataFromFile1, $pathParts1['extension']);
    $parsedData2 = getParsedData($dataFromFile2, $pathParts2['extension']);
    $ast = getAst($parsedData1, $parsedData2);
    $result = '';

    switch ($format) {
        case 'pretty':
            $result = getPrettyFormatOutput($ast);
            break;
        case 'plain':
            $result = getPlainFormatOutput($ast);
            break;
        case 'json':
            $result = getJsonFormatOutput($ast);
            break;
        default:
            throw new ErrorException("Unknown output format: {$format}");
    }
    
    return $result;
}

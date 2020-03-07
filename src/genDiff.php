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

use function Differ\Formatters\getPlainFormatOutput;
use function Differ\Formatters\getPrettyFormatOutput;
use function Differ\Formatters\getTextFormatOutput;
use function Differ\Parsers\parse;

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
    //Получаем данные из файлов
    $dataFromFile1 = parse($pathToFile1);
    $dataFromFile2 = parse($pathToFile2);
    $ast = getAst($dataFromFile1, $dataFromFile2);

    switch ($format){
    case 'pretty':
            $result = getPrettyFormatOutput($ast);
        break;
    case 'plain':
            $result = getPlainFormatOutput($ast);
        break;
    }
    
    return $result;
    /*
    //Формируем массив с новыми данными, согласно заданию
    $newData = [];
    foreach ($dataFromFile1 as $key => $value) {
        if (array_key_exists($key, $dataFromFile2)) {
            if ($value === $dataFromFile2[$key]) {
                $newData[$key] = $value;
            } else {
                $newData['- ' . $key] = $value;
                $newData['+ ' . $key] = $dataFromFile2[$key];
            }
        } else {
            $newData['- ' . $key] = $value;
        }
    }
    foreach ($dataFromFile2 as $key => $value) {
        if (!array_key_exists($key, $dataFromFile1)) {
            $newData['+ ' . $key] = $value;
        }
    }
    //Преобразуем массив в строку и возвращаем ее
    $strData = '';
    foreach ($newData as $key => $value) {
        $strData .= "\n{$key}: {$value}";
    }

    return "{ {$strData}\n}\n";
    */

}

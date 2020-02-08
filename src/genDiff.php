<?php

/**
 * Command Line function change
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

use function Funct\Strings\left;

/**
 * Function compare two files and return their difference
 *
 * @param string $pathToFile1 file to compare one
 * @param string $pathToFile2 file to compare two
 *
 * @return string
 */
function genDiff($pathToFile1 = null, $pathToFile2 = null)
{
    if (left($pathToFile1, 1) === '/') {
        $fileData1 = json_decode(file_get_contents($pathToFile1), true);
        $fileData2 = json_decode(file_get_contents($pathToFile2), true);
    } else {
        $fileData1 = json_decode(file_get_contents(__DIR__ . $pathToFile1), true);
        $fileData2 = json_decode(file_get_contents(__DIR__ . $pathToFile2), true);
    }
    //Формируем массив с новыми данными, согласно заданию
    $newData = [];
    foreach ($fileData1 as $key => $value) {
        if (array_key_exists($key, $fileData2)) {
            if ($value === $fileData2[$key]) {
                $newData[$key] = $value;
            } else {
                $newData['- ' . $key] = $value;
                $newData['+ ' . $key] = $fileData2[$key];
            }
        } else {
            $newData['- ' . $key] = $value;
        }
    }
    foreach ($fileData2 as $key => $value) {
        if (!array_key_exists($key, $fileData1)) {
            $newData['+ ' . $key] = $value;
        }
    }
    //Преобразуем массив в строку и возвращаем ее
    $strData = '';
    foreach ($newData as $key => $value) {
        $strData .= "\n{$key}: {$value}";
    }

    return "{ {$strData}\n}\n";

}
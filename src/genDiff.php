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


use function Differ\getData;

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
    //Получаем данные из файлов
    $dataFromFile1 = getData($pathToFile1);
    $dataFromFile2 = getData($pathToFile2);

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

}
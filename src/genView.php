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

use function Differ\Parsers\parse;
use function Funct\Collection\flatten;

/**
 * Function compare two files and return their difference
 *
 * @param string $data1 file to compare one
 * @param string $data2 file to compare two
 *
 * @return string
 */
function genView($data1, $data2)
{
    //Получаем данные из файлов
    $dataFromFile1 = parse($data1);
    $dataFromFile2 = parse($data2);
    reduce($dataFromFile1, $dataFromFile2, []);
}

/**
 * Function compare two files with tree structure and return their difference
 *
 * @param array $tree1 file to compare one
 * @param array $tree2 file to compare two
 * @param array $acc   file to compare two
 *
 * @return array
 */
function reduce($tree1, $tree2, $acc)
{
    $keys1 = array_keys($tree1);
    $keys2 = array_keys($tree2);
    $reduce = function ($node1, $node2, $acc, $keys1, $keys2) use (&$reduce) {

        foreach ($keys1 as $key) {
            if (is_array($node1)) {
                if (isset($node2[$key]) && isset($node1[$key])) {
                    $acc[] = [
                      'name' => $key,
                      'state' => 'changed',
                      'type' => 'node',
                      'children' => $node1[$key]//$updatedChildren
                    ];
                } elseif (isset($node1[$key]) && !isset($node2[$key])) {
                    //ищем удаленные узлы элементы
                    $acc[] = [
                      'name' => $key,
                      'state' => 'deleted',
                      'type' => 'node',
                      'value' => $node1[$key]
                    ];
                }
            } elseif (isset($node1[$key]) && !isset($node2[$key])) {
                //ищем удаленные листовые элементы
                $acc[] = [
                  'name' => $key,
                  'state' => 'deleted',
                  'type' => 'leaf',
                  'value' => $node1[$key]
                ];
            }
        }
        foreach ($keys2 as $key2) {
            if (is_array($node1)) {
                if (!isset($node1[$key2])) {
                    $acc[] = [
                      'name' => $key2,
                      'state' => 'added',
                      'type' => 'node',
                      'value' => $node2[$key2]
                    ];
                }
            } elseif (!isset($node1[$key2])) {
                    $acc[] = [
                      'name' => $key2,
                      'state' => 'added',
                      'type' => 'leaf',
                      'value' => $node2[$key2]
                    ];
            }
        }
        return $acc;
    };

    return $reduce($tree1, $tree2, $acc, $keys1, $keys2);
}
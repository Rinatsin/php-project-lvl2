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

/**
 * Function compare two files and return their difference
 *
 * @param string $data1 file to compare one
 * @param string $data2 file to compare two
 *
 * @return array
 */
function getAst($data1, $data2)
{
    //Получаем данные из файлов
    //$dataFromFile1 = parse($data1);
    //$dataFromFile2 = parse($data2);
    $result = reduce($data1, $data2, []);
    return $result;
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
            if (is_array($node1[$key])) {
                if (isset($node2[$key]) && isset($node1[$key])) {
                    $childrenKeys1 = array_keys($node1[$key]);
                    $childrenKeys2 = array_keys($node2[$key]);
                    $updatedNode1 = $node1[$key];
                    $updatedNode2 = $node2[$key];

                    $updatedChildren = $reduce($updatedNode1, $updatedNode2, [], $childrenKeys1, $childrenKeys2);
                    $acc[] = [
                      'name' => $key,
                      'state' => 'changed',
                      'type' => 'node',
                      'children' => $updatedChildren
                    ];
                } elseif (isset($node1[$key]) && !isset($node2[$key])) {//ищем удаленные узлы элементы
                    $acc[] = [
                      'name' => $key,
                      'state' => '- ',
                      'type' => 'node',
                      'value' => $node1[$key]
                    ];
                }
            } else {
                if (isset($node1[$key]) && !isset($node2[$key])) {
                    //ищем удаленные листовые элементы
                    $acc[] = [
                      'name' => $key,
                      'state' => '- ',
                      'type' => 'leaf',
                      'value' => $node1[$key]
                    ];
                }
                if (isset($node1[$key]) && isset($node2[$key]) && $node1[$key] === $node2[$key]) {
                    $acc[] = [
                      'name' => $key,
                      'state' => '  ',
                      'type' => 'leaf',
                      'value' => $node1[$key]
                    ];
                }
                if (isset($node1[$key]) && isset($node2[$key]) && $node1[$key] !== $node2[$key]) {
                    $acc[] = [
                    'name' => $key,
                    'state' => '+ ',
                    'type' => 'leaf',
                    'value' => $node2[$key]
                    ];
                    $acc[] = [
                      'name' => $key,
                      'state' => '- ',
                      'type' => 'leaf',
                      'value' => $node1[$key]
                    ];
                }
            }
        }
        foreach ($keys2 as $key2) {
            if (is_array($node2[$key2])) {
                if (!isset($node1[$key2])) {
                    $acc[] = [
                      'name' => $key2,
                      'state' => '+ ',
                      'type' => 'node',
                      'value' => $node2[$key2]
                    ];
                }
            } elseif (!isset($node1[$key2])) {
                $acc[] = [
                  'name' => $key2,
                  'state' => '+ ',
                  'type' => 'leaf',
                  'value' => $node2[$key2]
                ];
            }
        }
        return $acc;
      };

      return $reduce($tree1, $tree2, $acc, $keys1, $keys2);
}

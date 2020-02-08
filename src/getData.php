<?Php

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


/**
 * Function load data from two files and return it
 * 
 * @param string $pathToFile file to compare one
 *
 * @return array
 */
function getData($pathToFile)
{
    $fileData = json_decode(file_get_contents($pathToFile), true);
    return $fileData;
}
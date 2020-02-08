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

use function Funct\Strings\left;

/**
 * Function load data from two files and return it
 * 
 * @param string $pathToFile1 file to compare one
 * @param string $pathToFile2 file to compare two
 *
 * @return string
 */
function getData($pathToFile1, $pathToFile2)
{
    if (!left($pathToFile1, 1) === '/') {
        
    }
    //Получаем данные из файлов
    $fileData1 = json_decode(file_get_contents($pathToFile1), true);
    $fileData2 = json_decode(file_get_contents($pathToFile2), true);

}
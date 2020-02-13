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

 namespace Differ\Parsers;

use Symfony\Component\Yaml\Yaml;

use function Funct\Strings\endsWith;

/**
 * Function parse data
 *
 * @param string $pathToFile file to compare
 *
 * @return array
 */
function parse($pathToFile)
{
    $fileData = [];

    if (endsWith($pathToFile, 'yml')) {
        $fileData = Yaml::parseFile($pathToFile);
        return $fileData;
    } elseif (endsWith($pathToFile, 'json')) {
        $fileData = json_decode(file_get_contents($pathToFile), true);
        return $fileData;
    }
}

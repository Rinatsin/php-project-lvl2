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

use ErrorException;
use Symfony\Component\Yaml\Yaml;

/**
 * Function parse data
 *
 * @param string $data      data to compare
 * @param string $extension file extension for change parser
 *
 * @return array
 */
function getParsedData($data, $extension)
{
    switch ($extension) {
        case 'yml':
            return Yaml::parse($data);
            break;
        case 'json':
            return json_decode($data, true);
            break;
        default:
            throw new ErrorException("Wrong Data Type: {$extension}");
    }
}

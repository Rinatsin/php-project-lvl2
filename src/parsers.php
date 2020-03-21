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

use Error;
use ErrorException;
use Symfony\Component\Yaml\Yaml;

use function Funct\Strings\endsWith;

/**
 * Function parse data
 *
 * @param string $pathToFile file to compare
 *
 * @return array
 */
function getParsedData($pathToFile)
{
    $result = [];
    $data = file_get_contents($pathToFile);
    $pathParts = pathinfo($pathToFile);

    switch ($pathParts['extension']) {
        case 'yml':
            $result = parseYaml($data);
            break;
        case 'json':
            $result = parseJson($data);
            break;
        default:
            throw new ErrorException("Unknown file extension: {$pathParts['extension']}");
    }

    return $result;
}

/**
 * Function parse yaml data
 *
 * @param string $data data from file
 *
 * @return array return array
 */
function parseYaml($data)
{
    $result = Yaml::parse($data);
    return $result;
}

/**
 * Function parse json data
 *
 * @param string $data data from file
 *
 * @return array return array
 */
function parseJson($data)
{
    $result = json_decode($data, true);
    return $result;
}

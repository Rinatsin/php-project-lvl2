# php-project-lvl2

[![Build Status](https://travis-ci.com/Rinatsin/php-project-lvl2.svg?branch=master)](https://travis-ci.com/Rinatsin/php-project-lvl2)
[![Maintainability](https://api.codeclimate.com/v1/badges/41efcf3e315d5abdd2a8/maintainability)](https://codeclimate.com/github/Rinatsin/php-project-lvl2/maintainability)
[![Test Coverage](https://api.codeclimate.com/v1/badges/41efcf3e315d5abdd2a8/test_coverage)](https://codeclimate.com/github/Rinatsin/php-project-lvl2/test_coverage)

Этот репозиторий содержит реализацию второго проекта обучающего портала Hexlet, Generate Difference!

Утилита служит для поиска отличий в конфигурационных файлах.

### Возможности утилиты:

* Поддержка разных форматов
* Генерация отчета в виде plain text, pretty и json

### Установка утилиты

Для установки утилиты при посощи composer используйте команду:

* composer global require rinatsin/php-project-lvl2

[![asciicast](https://asciinema.org/a/hIRg22BVHVXyZUQ62bHVKsbdW.svg)](https://asciinema.org/a/hIRg22BVHVXyZUQ62bHVKsbdW)

### Примеры использования:

#### 1. Поиск отличий  в двух конфигурационных файлах before.json и after.json, отчет выводится в формате pretty:

[![asciicast](https://asciinema.org/a/Sc17mxWwkkEsxsbmkaWcjcAb9.svg)](https://asciinema.org/a/Sc17mxWwkkEsxsbmkaWcjcAb9)

#### 2. Поиск отличий  в двух конфигурационных файлах before.yml и after.yml, отчет выводится в формате pretty:

[![asciicast](https://asciinema.org/a/eFQf7yPp91PJFo2DElLyaVE5w.svg)](https://asciinema.org/a/eFQf7yPp91PJFo2DElLyaVE5w)

#### 3. Поиск отличий  в двух конфигурационных файлах beforeTree.json и afterTree.json, которые имеют древовидную структутру, отчет выводится в формате pretty:

[![asciicast](https://asciinema.org/a/XrTH1MvR9kLtw8zeN9QgJXtpI.svg)](https://asciinema.org/a/XrTH1MvR9kLtw8zeN9QgJXtpI)

#### 4. Возможность выбора формата вывода отчета используя флаг '--format'. Два типа форматов вывода, это 'pretty' и 'plain'.

[![asciicast](https://asciinema.org/a/bnTuBEwCfdWmlXDaOl1FeOohk.svg)](https://asciinema.org/a/bnTuBEwCfdWmlXDaOl1FeOohk)
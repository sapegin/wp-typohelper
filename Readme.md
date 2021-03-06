# Typohelper for WordPress

[![No Maintenance Intended](http://unmaintained.tech/badge.svg)](http://unmaintained.tech/)

Плагин для обогащения русской типографики. В основном расставляет неразрывные пробелы. Предполагается, что правильные кавычки, тире и прочие знаки в вашем тексте уже стоят. Это удобнее всего делать с помощью типографской клавиатуры.

В комментариях расставляются кавычки, тире, многоточия и т. п., но без неразрывных пробелов. Используется простейшая логика — примерно как в Ворде.


## Установка

1. Скопируйте папку `typohelper` в папку плагинов Вордпресса.

2. Активируйте плагин через админку.


## Что делает типографер

* Ставит неразрывный пробел после знаков №, §, перед тире, внутри сокращений «и т. д.», «и т. п.»

* Ставит неразрывный пробел между числом и валютой ($, «р», «руб»).

* Ставит неразрывный пробел между числом и назавнием месяца.

* Приклеивает инициалы к фамилии.

* Склеивает слова через дефис.

* Приклеивает частицы «бы», «же», «ли» и другие к предшествующему слову.

* Приклеивает предлоги и союзы к слову после них.


## Что делает упрощённый типографер (для комментариев)

* Кавычки-ёлочки, тире.

* Апострофы, многоточие, знак копирайта.


## Настройка

Если лень ставить кавычки руками, можно включить упрощённый типографер везде. Для этого в файл `wp-config.php` нужно добавить:

```php
define('WP_TYPOHELPER_DUMMY', true);
```

При этом в постах по-прежнему будет запускаться обогащение типографики.


---

## License

The MIT License, see the included `License.md` file.

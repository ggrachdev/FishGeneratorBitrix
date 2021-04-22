Библиотека для генерации тестовых элементов для 1С Битрикс на основе Faker, которая позволит сэкономить время контент-менеджера и программиста для наполнения проекта данными и тестирования.

**Библиотека находится в разработке (в будущем возможно изменение методов, параметров и т.п)**

**Если при генерации код долго грузится - значит не отвечает сайт http://lorempixel.com/ в будущих версиях данное неудобство будет исправлено**

В будущем появится генератор не только элементов, но и разделов, элементов Highload блоков, генерация разных цен для разных валют, торговых предложений  

Пример использования:

```php
<?php
use GGrach\FishGenerator\Generators\FishGenerator;


\Bitrix\Main\Loader::includeModule('ggrachdev.fish_generator');

/** 
* В конструктор передаем IBLOCK ID в который нужно сгенерировать тестовый элемент
* При setDebug = true в результирующий массив записываются данные для генерации
* При setStrictMode = true выбрасываются Exception'ы если что-то идет не так
* Вторым параметром в конструктор можно передать локализацию faker, по умолчанию ru_RU
* По умолчанию автоматически генерируются: имя, детальное фото, фото анонса, детальный текст + текст анонса, символьный код
* Системными полями считаются: 'NAME', 'ACTIVE', 'CODE', 'IBLOCK_SECTION_ID', 'DETAIL_TEXT', 'PREVIEW_TEXT', 'SORT'
* Если поле является системным, то нужно установки правил генерации ставить * перед ним, если же свойство является дополнительно созданным и к 
* нему нужно обращаться через PROPERTY_... то ничего в качестве префикса ставить не нужно
*/

$result = (new FishGenerator(6))->setDebug(true)->setStrictMode(true)
->setCategoryPhoto(['technics', 'business', 'city'])
->setGenerationRules([
       /*  
       * Если свойство является системым, то ставим в начале *, если свойство является дополнительным у инфоблока (Т.е PROPERTY_NAME), то не ставим  
       * Если  нужно задать строгое значение свойства при добавлении элементов, то ставим =, можно группировать: *=, =, *, при этом в $ 
       * будет подставлен номер генерируемого     элемента
       */  
       '*=NAME' => 'Тестовый элемент $',
       
       /*  
       * Если свойство является множественным, то передаем массив - 1 элемент массива задаем генератор (так же поддерживаются *, =), 2 элемент массива - кол-во элементов для генерации  
       * Если нужно сгененировать одиночное свойство, то передаем просто строку (в качестве значения)  
       */  
       'PRODUCTION_PHOTOS' => [
           'image', 7
       ],
       'IMPLEMENTED_PROCESSES_POINTS' => [
           'randomElement(Тестовый пункт, Еще один пункт, Пункт производства, Новый пункт, Пункт элемента, Тестовый процесс, Процесс производства, Новый процесс производства)', 5
       ],
       'IMPLEMENTED_PROCESSES_VALUES' => [
           'realText(100)', 5
       ],
       '*IBLOCK_SECTION_ID' => 'randomSection'
   ])->generate(1);
   
echo '<pre>';  
print_r($result);  
echo '</pre>';  
?>
```

Доступные способы для генерации:  
- inn // ИНН  
- name // Имя  
- kpp  
- address  
- realText(100)  
- word  
- city  
- country  
- phoneNumber  
- company  
- email  
- streetAddress  
- date  
- time  
- year  
- jobTitle  
- numberBetween(0)(1000)  
- randomElement(1,2,3,4)  
- lastName  
- firstName  
- latitude  
- longitude  
- hexcolor  
- image  
- image(1000, 500) // Ширина, высота  
- randomSection // Привязать элемент к случайной секции инфоблока
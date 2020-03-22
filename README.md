# Миграция базы данных
Реквизиты соединения с БД задаются прямо в `bin/migrations`

Накатить:
```
php ./bin/migrations migrations:execute --up 20200322113511 
 --configuration=./config/migrations.yml
```
Откатить:
```
php ./bin/migrations migrations:execute --down 20200322113511
 --configuration=./config/migrations.yml
```
Описание проекта
========
Данный проект реализован в виде CLI приложения для обработки некой 
выгрузки данных. Предполагается, что на входе предоставляется 
файл с json данными. Этот файл необходимо прочитать, разобрать 
и сохранить их куда-либо (в файлы/базу) данные.

Для того, чтобы сгенерировать файл с тестовыми данными можно 
воспользоваться командой `./bin/console file:generator` 
соответственно, для запуска парсинга подготовлена 
команда `./bin/console file:parser`

На данный момент приложение полностью рабочее и выполняет 
все изначально поставленные задачи. Но, хотелось бы довести код 
до идеала насколько это возможно, попутно решив несколько 
сопутствующих задач.

Минимально необходимые требования
=========
* Доработать код таким образом, чтобы можно было сохранять данные 
при парсинге сразу в 2 места (файлы и базу)
* Понять схему БД на основании кода и подготовить миграцию 
(компонент для миграций нужно будет выбрать самостоятельно)
* После загрузки данных в БД из файла необходимо расcчитать сколько 
человек в каждом городе и вывести эту информацию так же в консоли

Дополнительные задачи
========
* Привести код в читабельный вид в соответствии со стандартами Symfony
* Исправить возможные проблемы в коде

Будет плюсом
=======
* Добавить логирование возможных ошибок
* Написать тест(ы) на PHPUnit

Дополнительная информация
========
* Решая данное задание вы можете выбрать любой удобный для вас 
подход - выкинуть весь код и переписать его так, чтобы полностью 
сохранить поведение приложения и исправить возможные проблемы. 
Либо же, установить/заменить пакеты на те, которые посчитаете 
нужными или с которыми привыкли работать.
* Если какой-то участок кода не поддается рефакторингу, но вы видите 
в нем проблему - можете обозначить это комментарием в коде.

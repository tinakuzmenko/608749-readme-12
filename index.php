<?php

// импорты
require_once 'user-functions.php';
require_once 'helpers.php';
require_once 'models.php';
require_once 'config/db.php';

// получение mysqli объекта для работы с базой данных
$link = init($db);

// объявление переменных
$is_auth = rand(0, 1);
$user_name = 'Тина Кузьменко';
$title = 'readme: популярное';

// получаем параметры из строки запроса
$params = [
    'filter' => filter_input(INPUT_GET, 'filter', FILTER_SANITIZE_NUMBER_INT) ?? 0,
    'sort' => filter_input(INPUT_GET, 'sort', FILTER_SANITIZE_STRING) ?? 'views',
];

// получаем данные
$result_content_types = get_content_types($link);
$result_popular_posts = get_popular_posts($link, $params['filter']);

// проверка данных
$mysql_error = catch_mysql_error($result_content_types, $result_popular_posts);

// получаем шаблон для main
$main = $mysql_error
    ? include_template('db-error.php', ['error' => $mysql_error])
    : include_template('main.php', [
        'posts' => $result_popular_posts,
        'content_types' => $result_content_types,
        'active_filter' => $params['filter'],
    ]);

// составление layout страницы
$layout = include_template('layout.php', [
    'main' => $main,
    'title' => $title,
    'is_auth' => $is_auth,
    'user_name' => $user_name,
]);

// отрисовка
print($layout);

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

$params = [
    'id' => filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT) ?? 0,
];

$result_post = get_post_details($link, $params['id']);
$result_hashtags = get_post_hashtags($link, $params['id']);
$result_comments = get_post_comments($link, $params['id']);

// проверка данных
$mysql_error = catch_mysql_error($result_post, $result_hashtags, $result_comments);

// запрос данных о пользователе
$user_id = $result_post[0]['user_id'];
$result_user = get_user($link, $user_id);
$user_subscribers = get_subscribers_amount($link, $user_id);
$user_posts = get_posts_amount($link, $user_id);

// получаем шаблон для main
if (empty($result_post)) {
    $main = include_template('errors/not-found.php', [ 'error' => $mysql_error, ]);
} else {
    $likes = get_likes($link, $result_post[0]['id']);
    $post = array_merge(...$result_post, ...$likes);

    $main = $mysql_error
        ? include_template('errors/db-error.php', ['error' => $mysql_error])
        : include_template('post-details.php', [
            'post' => $post,
            'hashtags' => $result_hashtags,
            'comments' => $result_comments,
            'user' => $result_user[0],
            'user_posts' => $user_posts[0]['posts'],
            'user_subscribers' => $user_subscribers[0]['subscribers'],
    ]);
}

// составление layout страницы
$layout = include_template('layout.php', [
    'main' => $main,
    'title' => $title,
    'is_auth' => $is_auth,
    'user_name' => $user_name,
]);

// отрисовка
print($layout);
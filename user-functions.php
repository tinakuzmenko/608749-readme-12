<?php

// Устанавливаем временную зону по умолчанию
date_default_timezone_set('Europe/Amsterdam');

/**
 * Функция для инициализации базы данных в приложении
 * @param   array $db       Массив с настройками для базы данных
 *
 * @return  mysqli|string   Объект mysqli для запросов в базу данных или строка с ошибкой
 */
function init(array $db) {
    $link = mysqli_connect($db['host'], $db['user'], $db['password'], $db['database']);

    if (!$link) {
        return mysqli_connect_error();
    }

    mysqli_set_charset($link, 'utf8');

    return $link;
}

/**
 * Вспомогательная функция для проверки полученных данных на наличие ошибок
 * @param   mixed ...$params  параметры, которые нужно проверить на наличие ошибки
 *
 * @return  string            строка с ошибкой или пустая строка, если ошибок нет
 */
function catch_mysql_error(...$params): string {
    foreach ($params as $param) {
        if (gettype($param) === 'string') {
            return $param;
        }
    }

    return '';
}

/**
 * Функция, которая возвращает результат: оригинальный текст, если его длина меньше
 * заданного числа символов. В противном случае это урезанный текст с прибавленным к нему троеточием.
 * @param string $string    строка, которую требуется обрезать
 * @param int $max_length   максимально допустимый размер строки
 *
 * @return string           обрезанная строка
 */
function cut_string(string $string, int $max_length = 300): string {

    if (mb_strlen($string) <= $max_length) {
        return $string;
    }

    $words = explode(' ', $string);
    $result_string = '';

    foreach ($words as $word) {
        if (mb_strlen($result_string . ' ' . $word) > $max_length) {
            break;
        }

        $result_string .= !$result_string ? $word :  ' ' . $word;
    }

    return $result_string . '...';
}

/**
 * Функция, которая генерирует разметку текстового поста. И, в зависимости от его длины,
 * обрезает текст и добавляет к нему ссылку на полный пост.
 * @param string $content   контент, который вставляется в разметку
 * @param string $link      ссылка на полную версию поста
 * @param int $max_length   максимально допустимый размер текста
 *
 * @return string           сгенерированная разметка для шаблона
 */
function create_text_post(string $content, string $link = '#', int $max_length = 300): string {

    $result = '<p>' . cut_string($content, $max_length) . '</p>';

    if (mb_strlen($content) > $max_length) {
        $result .= '<a class="post-text__more-link" href="' . $link . '">Читать далее</a>';
    }

    return $result;
}

/**
 * Функция, которая форматирует дату в относительный ("человеческий") формат в виде прошедших с данного момента
 * минут, часов, дней, недель или месяцев.
 * @param string $date      дата, которую нужно отформатировать
 * @param bool $is_full     полный или не полный вариант фразы
 *
 * @return string         дата в "человеческом" формате в виде строки
 */
function humanize_date(string $date, bool $is_full = true): string {

    $current = date_create();
    $post_date = date_create($date);
    $diff = date_diff($post_date, $current);

    $minutes = ceil($diff->i);
    $hours = ceil($diff->h);
    $days = ceil($diff->d);
    $weeks = ceil($days / 7);
    $months = ceil($diff->m);

    switch (true) {
        case ($months):
            return "$months " . get_noun_plural_form($months, 'месяц', 'месяца', 'месяцев') . ($is_full ? ' назад' : '');
        case ($days >= 7):
            return "$weeks " . get_noun_plural_form($weeks, 'неделя', 'недели', 'недель') . ($is_full ? ' назад' : '');
        case ($days):
            return "$days " . get_noun_plural_form($days, 'день', 'дня', 'дней') . ($is_full ? ' назад' : '');
        case ($hours):
            return "$hours " . get_noun_plural_form($hours, 'час', 'часа', 'часов') . ($is_full ? ' назад' : '');
        case ($minutes):
            return "$minutes " . get_noun_plural_form($minutes, 'минута', 'минуты', 'минут') . ($is_full ? ' назад' : '');
        default:
            return 'недавно';
    }
}

/**
 * Вспомогательная функция для генерации линка сортировки
 * @param string $active_filter   активный фильтр
 * @param string $active_sort     активная сортировка
 * @param string $sort_direction  направление сортировки
 * @param string $current_sort    тип текущей сортировки
 *
 * @return string   сгенерированная ссылка для сортировки
 */
function get_sorting_link (string $active_filter, string $active_sort, string $sort_direction, string $current_sort): string {
    if (!$active_filter) {
        return "/?" . http_build_query([
                'sort' => $current_sort,
                'direction' => $active_sort === $current_sort && $sort_direction !== 'asc' ? 'asc' : 'desc',
            ]);
    } else {
        return "/?" . http_build_query([
            'filter' => $active_filter,
            'sort' => $current_sort,
            'direction' => $active_sort === $current_sort && $sort_direction !== 'asc' ? 'asc' : 'desc',
        ]);
    }
}

/**
 * Вспомогательная функция для генерации нужного темплейта поста
 * @param array $post  ассоциативный массив для деталей поста
 *
 * @return string
 */
function create_post_template (array $post): string {
    switch ($post['icon']) {
        case 'text':
            return include_template('post-details/text.php', [
                'text' => $post['content'],
            ]);
        case 'quote':
            return include_template('post-details/quote.php', [
                'text' => $post['content'],
                'author' => $post['cite_author']
            ]);
        case 'video':
            return include_template('post-details/video.php', [
                'youtube_url' => $post['content'],
            ]);
        case 'photo':
            return include_template('post-details/photo.php', [
                'img_url' => $post['content'],
            ]);
        case 'link':
            return include_template('post-details/link.php', [
                'url' => $post['content'],
                'title' => $post['title'],
            ]);
        default:
            return 'Недопустимый формат поста.';
    }
}

/**
 * Вспомогательная функция для генерации нужной формы под конкретный тип поста
 * @param string $active_type   тип поста для подбора необходимой формы
 * @param array $errors         массив с ошибками валидации
 * @param array $values         массив с сохраненными ранее данными формы
 *
 * @return string
 */
function add_form_fields(string $active_type = 'text', array $errors = [], array $values = []): string {
    $params = [
        'errors' => $errors,
        'values' => $values,
    ];

    switch ($active_type) {
        case 'quote':
            return include_template('add-post/quote.php', $params);
        case 'video':
            return include_template('add-post/video.php', $params);
        case 'photo':
            return include_template('add-post/photo.php', $params);
        case 'link':
            return include_template('add-post/link.php', $params);
        case 'text':
        default:
            return include_template('add-post/text.php', $params);
    }
}

/**
 * Функция для проверки наличия ошибок у поля и отрисовки блока с ошибками,
 * если они есть
 * @param array $field_errors     массив с ошибками
 *
 * @return string      разметка показа ошибки
 */
function show_field_errors(array $field_errors): string {
    $errors = '';

    if (empty($field_errors)) {
        return $errors;
    }

    foreach ($field_errors as $error) {
        $errors = $errors . '<h3 class="form__error-title">' . $error['title'] . '</h3>
                <p class="form__error-desc">' . $error['description'] . '</p>';
    }

    return '<button class="form__error-button button" type="button">!<span class="visually-hidden">Информация об ошибке</span></button>
        <div class="form__error-text">' . $errors . '</div>';
}

/**
 * Вспомогательная функция для сохранения фото в папку uploads со стороннего ресурса
 * @param string $photo_link   Ссылка на фотографию на стороннем ресурсе
 *
 * @return  string      Ссылка на фото в папке uploads
 */
function save_photo_from_url (string $photo_link): string {
    $image = file_get_contents($photo_link);

    $file_name = basename($photo_link);
    $file_path = '/uploads/' . $file_name;

    file_put_contents($file_path, $image);

    // @todo понять почему картинки не грузятся куда надо и не генерируется нормальный url
    return $file_path;
}

/**
 * Вспомогательная функция для сохранения фото в папку uploads
 * @param array $photo   ассоциативный массив с фото
 *
 * @return  string      Ссылка на фото в папке uploads
 */
function save_photo (array $photo): string {
    $tmp_name = $photo['tmp_name'];
    $file_name = basename($tmp_name);
    $file_path = __DIR__ . '/uploads/';

    move_uploaded_file($tmp_name, $file_path . $file_name);

    // @todo понять почему картинки не грузятся куда надо и не генерируется нормальный url
    return $file_path . $file_name;
}

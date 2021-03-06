use readme;

# список типов контента для поста;
INSERT INTO content_type (type, icon)
VALUES ('Текст', 'text'),
       ('Цитата', 'quote'),
       ('Видео', 'video'),
       ('Картинка', 'photo'),
       ('Ссылка', 'link');

# список пользователей;
INSERT INTO user (email, login, password, avatar)
VALUES ('phizntrg@msn.com', 'Владик', 'abc12345', '/img/userpic-big.jpg'),
       ('wiseb@msn.com', 'Лариса', 'qwwerty', '/img/userpic-larisa.jpg'),
       ('yumpy@msn.com', 'Виктор', 'asdfg123', '/img/userpic-mark.jpg'),
       ('sthomas@comcast.net', 'SThomas', 'password', 'https://placekitten.com/640/640'),
       ('elflord@yahoo.com', 'ElfLord', '12345zxc', 'https://placekitten.com/640/640'),
       ('hahsler@icloud.com', 'HahSler', '098aaa111', 'https://placekitten.com/640/640'),
       ('gslondon@gmail.com', 'gsLondon', 'lkjh567', 'https://placekitten.com/640/640'),
       ('druschel@outlook.com', 'DrUschel', 'bcye12', 'https://placekitten.com/640/640'),
       ('leocharre@att.net', 'LeoCharre', 'foasfa', 'https://placekitten.com/640/640'),
       ('mhanoh@me.com', 'Mhanoh', 'asdasgqwetq', 'https://placekitten.com/640/640'),
       ('bjornk@outlook.com', 'Bjorn_K', '4124wegaerg', 'https://placekitten.com/640/640'),
       ('hillct@icloud.com', 'Hill_Catana', 'q34bli', 'https://placekitten.com/640/640');

# список комментариев к разным постам;
INSERT INTO comment (content, user_id, post_id)
VALUES ('Гармоническое микророндо представляет собой модальный рок-н-ролл 50-х, о чем подробно говорится в книге М.Друскина "Ганс Эйслер и рабочее музыкальное движение в Германии".',
        2, 1),
       ('Аккорд, как бы это ни казалось парадоксальным, выстраивает нонаккорд, благодаря быстрой смене тембров (каждый инструмент играет минимум звуков).',
        6, 2),
       ('Крещендирующее хождение mezzo forte диссонирует флюгель-горн.',
        1, 3),
       ('Глиссандо, согласно традиционным представлениям, полифигурно варьирует сонорный райдер.',
        7, 4),
       ('Мономерная остинатная педаль образует диссонансный септаккорд. ',
        3, 5),
       ('Развивая эту тему, мономерная остинатная педаль дает open-air, потому что современная музыка не запоминается.',
        10, 6),
       ('Ретро ненаблюдаемо. Легато музыкально. Арпеджио просветляет полиряд.',
        4, 2),
       ('Как отмечает Теодор Адорно, пуанта многопланово заканчивает изоритмический ревер, и здесь в качестве модуса конструктивных элементов используется ряд каких-либо единых длительностей.',
        8, 1);

# список постов;
INSERT INTO post (title, content, cite_author, views, user_id, content_type_id)
VALUES ('Цитата', 'Мы в жизни любим только раз, а после ищем лишь похожих', 'Джейсон Стетхем', 150, 2, 2),
       ('Полезный пост про Байкал', 'Озеро Байкал – огромное древнее озеро в горах Сибири к северу от монгольской границы. Байкал считается самым глубоким озером в мире. Он окружен сетью пешеходных маршрутов, называемых Большой байкальской тропой. Деревня Листвянка, расположенная на западном берегу озера, – популярная отправная точка для летних экскурсий. Зимой здесь можно кататься на коньках и собачьих упряжках.', '', 28, 2, 1),
       ('Игра престолов', 'Не могу дождаться начала финального сезона своего любимого сериала!', '', 3, 1, 1),
       ('Клип, от которого мурашки', 'https://youtube.com/watch?v=dQw4w9WgXcQ', '', 80000, 1, 3),
       ('Наконец, обработал фотки!', '/img/rock-default.jpg', '', 1794, 3, 4),
       ('Моя мечта', '/img/coast-medium.jpg', '', 70, 2, 4),
       ('Лучшие курсы', 'https://htmlacademy.ru/', '', 10500, 1, 5);

# получить список постов с сортировкой по популярности и вместе с именами авторов и типом контента
SELECT p.id,
       date,
       title,
       content,
       cite_author,
       views,
       login,
       type
FROM post p
         JOIN user u ON p.user_id = u.id
         JOIN content_type ct ON p.content_type_id = ct.id
ORDER BY views DESC;

# получить список постов для конкретного пользователя;
SELECT *
FROM post
WHERE user_id = 2;

# получить список комментариев для одного поста, в комментариях должен быть логин пользователя;
SELECT c.id, c.date, c.content, u.login, p.content
FROM comment c
         JOIN user u ON c.user_id = u.id
         JOIN post p ON c.post_id = p.id
WHERE post_id = 4;

# добавить лайк к посту;
INSERT INTO post_like (user_id, post_id)
VALUES (2, 6),
       (2, 1),
       (1, 4),
       (2, 4),
       (3, 4),
       (4, 4),
       (5, 4),
       (6, 4),
       (7, 4),
       (8, 4),
       (9, 4);

# подписаться на пользователя.
INSERT INTO subscription (user_id, recipient_id)
VALUES (2, 1);

# добавить хэштеги
INSERT INTO hashtag (hashtag)
VALUES ('байкал'),
       ('путешествия'),
       ('музыка'),
       ('сериал'),
       ('полезное'),
       ('природа'),
       ('фото'),
       ('цитата'),
       ('мудрость'),
       ('мечта'),
       ('beautiful'),
       ('видео'),
       ('лол'),
       ('music'),
       ('драйв'),
       ('щикарныйвид'),
       ('курсы'),
       ('академия'),
       ('htmlacademy');

# добавить хэштеги к постам
INSERT INTO post_hashtag (post_id, hashtag_id)
VALUES (1, 9),
       (1, 8),
       (2, 2),
       (2, 5),
       (2, 6),
       (2, 11),
       (3, 4),
       (4, 3),
       (4, 12),
       (4, 14),
       (4, 13),
       (4, 15),
       (5, 2),
       (5, 1),
       (5, 7),
       (5, 6),
       (6, 10),
       (7, 5),
       (7, 17),
       (7, 18),
       (7, 19);

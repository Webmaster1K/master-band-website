-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1:3306
-- Время создания: Ноя 24 2025 г., 20:45
-- Версия сервера: 8.0.30
-- Версия PHP: 8.1.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `master_band`
--

-- --------------------------------------------------------

--
-- Структура таблицы `albums`
--

CREATE TABLE `albums` (
  `id` int NOT NULL,
  `title` varchar(255) NOT NULL,
  `header` varchar(255) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `description` text,
  `release_year` year DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `albums`
--

INSERT INTO `albums` (`id`, `title`, `header`, `image_path`, `description`, `release_year`) VALUES
(1, 'М А С Т Е Р', 'Альбом 1 - МАСТЕР', '../images/1.webp', '«Мастер» — первый студийный альбом рок-группы «Мастер». Записанный в 1987 году, альбом был выпущен официально в 1988 году фирмой «Мелодия». По официальным данным, тираж пластинок превысил 1 млн экземпляров, что в США соответствует платиновому статусу. В 1995 году альбом был переиздан на компакт-диске «Студией Союз», а в 2007 — студией CD-Maximum.', 1988),
(2, 'С ПЕТЛЁЙ НА ШЕЕ', 'Альбом 2 - МАСТЕР', '../images/2.jpg', '«С петлёй на шее» — второй студийный альбом группы «Мастер». Запись была произведена летом 1989 года и в том же году альбом попал к массовому потребителю.', 1989),
(3, 'ПЕСНИ МЕРТВЫХ', 'Альбом 3 - МАСТЕР', '../images/3.webp', 'Третий студийный альбом группы «Мастер», выпущенный в 1996 году. Альбом продолжает традиции трэш-метала группы.', 1996),
(4, 'ЛАБИРИНТ', 'Альбом 4 - МАСТЕР', '../images/4.webp', 'Четвертый студийный альбом с новым вокалистом Алексеем Кравченко (Lexx), выпущенный в 2000 году. Знаменует новый этап в творчестве группы.', 2000),
(5, 'TALK OF THE DEVIL', 'Альбом 5 - МАСТЕР', '../images/5.webp', 'Англоязычный альбом группы, записанный в 1991-1995 годах. Демонстрирует международные амбиции группы.', 1995),
(6, '33 ЖИЗНИ', 'Альбом 6 - МАСТЕР', '../images/6.webp', 'Один из поздних альбомов группы, выпущенный в 2016 году. Показывает эволюцию звучания группы при сохранении фирменного стиля.', 2016);

-- --------------------------------------------------------

--
-- Структура таблицы `band_history`
--

CREATE TABLE `band_history` (
  `id` int NOT NULL,
  `year` varchar(10) NOT NULL,
  `event` text NOT NULL,
  `importance` enum('high','medium','low') DEFAULT 'medium'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `band_history`
--

INSERT INTO `band_history` (`id`, `year`, `event`, `importance`) VALUES
(1, '1986', 'Основание группы Аликом Грановским и Андреем Большаковым после ухода из группы «Ария»', 'high'),
(2, '1987', 'Запись первого альбома «Мастер»', 'high'),
(3, '1988', 'Официальный выпуск первого альбома тиражом более 1 млн экземпляров', 'high'),
(4, '1989', 'Релиз альбома «С петлёй на шее» (тираж вдвое больше первого альбома)', 'high'),
(5, '1991-1995', 'Период работы над англоязычными альбомами, включая «Talk of the Devil»', 'medium'),
(6, '1996', 'Выход альбома «Песни мёртвых»', 'medium'),
(7, '1999', 'Приход нового вокалиста Алексея Кравченко (Lexx)', 'high'),
(8, '2000', 'Релиз альбома «Лабиринт» с новым вокалистом', 'high'),
(9, '2008', 'Возвращение гитариста Леонида Фомина в группу', 'medium'),
(10, '2016', 'Выход альбома «33 жизни»', 'medium');

-- --------------------------------------------------------

--
-- Структура таблицы `band_members`
--

CREATE TABLE `band_members` (
  `id` int NOT NULL,
  `name` varchar(100) NOT NULL,
  `role` varchar(100) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `years_active` varchar(100) NOT NULL,
  `description` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `band_members`
--

INSERT INTO `band_members` (`id`, `name`, `role`, `image_path`, `years_active`, `description`) VALUES
(1, 'Алексей «Lexx» Кравченко', 'вокал', '../images/01.jpg', '1999—наши дни', 'Основной вокалист группы с 1999 года'),
(2, 'Леонид Фомин', 'гитара', '../images/02.jpg', '1998—2000, 2008—наши дни', 'Гитарист, композитор, участник с перерывами'),
(3, 'Алик Грановский', 'бас-гитара, акустическая гитара, клавишные', '../images/03.webp', '1986—наши дни', 'Основатель группы, лидер, бессменный участник'),
(4, 'Александр «Гипс» Бычков', 'ударные', '../images/05.jpg', '2008, 2011—наши дни', 'Барабанщик, участник с 2008 года'),
(5, 'Алексей Баев', 'ударные', '../images/04.jpg', '2013—2014, 2021—наши дни', 'Барабанщик, текущий участник');

-- --------------------------------------------------------

--
-- Структура таблицы `concerts`
--

CREATE TABLE `concerts` (
  `id` int NOT NULL,
  `city` varchar(100) NOT NULL,
  `date` date NOT NULL,
  `venue` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `concerts`
--

INSERT INTO `concerts` (`id`, `city`, `date`, `venue`, `price`) VALUES
(1, 'Москва', '2024-10-01', 'Клуб \"Рок\"', '1500.00'),
(2, 'Санкт-Петербург', '2024-10-05', 'ДК им. Горького', '1800.00'),
(3, 'Казань', '2024-10-10', 'Центр культуры \"Казань\"', '1200.00'),
(4, 'Екатеринбург', '2024-10-15', 'Клуб \"Олимп\"', '1400.00'),
(5, 'Нижний Новгород', '2024-10-20', 'Дворец спорта', '1600.00'),
(6, 'Ростов-на-Дону', '2024-10-25', 'Концертный зал \"Ростов\"', '1300.00'),
(7, 'Новосибирск', '2024-10-30', 'Клуб \"Красный\"', '1700.00'),
(8, 'Владивосток', '2024-11-05', 'Театр им. Горького', '2000.00');

-- --------------------------------------------------------

--
-- Структура таблицы `gallery`
--

CREATE TABLE `gallery` (
  `id` int NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `category` varchar(100) NOT NULL,
  `description` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `gallery`
--

INSERT INTO `gallery` (`id`, `image_path`, `category`, `description`) VALUES
(1, '../images/master1.jpeg', 'life', 'Участник группы МАСТЕР в студии звукозаписи'),
(2, '../images/master2.jpeg', 'life', 'Репетиция группы перед концертом'),
(3, '../images/master3.jpeg', 'life', 'Участники группы за кулисами'),
(4, '../images/master4.jpeg', 'life', 'Встреча группы с фанатами'),
(5, '../images/concert1.jpg', 'concerts', 'Концерт группы МАСТЕР в Москве'),
(6, '../images/concert2.jpg', 'concerts', 'Выступление на рок-фестивале'),
(7, '../images/concert3.jpg', 'concerts', 'Живое выступление группы'),
(8, '../images/concert4.jpg', 'concerts', 'Соло гитариста на концерте');

-- --------------------------------------------------------

--
-- Структура таблицы `news`
--

CREATE TABLE `news` (
  `id` int NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `publish_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `image_path` varchar(255) DEFAULT NULL,
  `is_published` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `news`
--

INSERT INTO `news` (`id`, `title`, `content`, `publish_date`, `image_path`, `is_published`) VALUES
(1, 'Новый концертный тур', 'Группа МАСТЕР объявляет о старте нового концертного тура по городам России. Билеты уже в продаже!', '2025-11-24 15:16:44', '../images/concert1.jpg', 1),
(2, 'Выход мерча', 'В продажу поступила новая коллекция мерча группы МАСТЕР. Футболки, худи и аксессуары с уникальными дизайнами.', '2025-11-24 15:16:44', '../images/0001.jfif', 1),
(3, 'Юбилейный концерт', 'Группа МАСТЕР готовит специальный юбилейный концерт, посвященный 35-летию творческой деятельности.', '2025-11-24 15:16:44', '../images/master1.jpeg', 1);

-- --------------------------------------------------------

--
-- Структура таблицы `orders`
--

CREATE TABLE `orders` (
  `id` int NOT NULL,
  `customer_name` varchar(100) DEFAULT NULL,
  `customer_email` varchar(100) DEFAULT NULL,
  `customer_phone` varchar(20) DEFAULT NULL,
  `total_amount` decimal(10,2) DEFAULT NULL,
  `order_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `status` enum('pending','confirmed','shipped','delivered') DEFAULT 'pending',
  `customer_address` text NOT NULL,
  `customer_comment` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `orders`
--

INSERT INTO `orders` (`id`, `customer_name`, `customer_email`, `customer_phone`, `total_amount`, `order_date`, `status`, `customer_address`, `customer_comment`) VALUES
(1, NULL, NULL, NULL, '1500.00', '2025-11-24 15:57:07', 'pending', '', NULL),
(2, 'Ксения', 'volos@gmail.com', '894343122549', '2700.00', '2025-11-24 17:41:27', 'pending', 'Пера 3', 'Отлично да');

-- --------------------------------------------------------

--
-- Структура таблицы `order_items`
--

CREATE TABLE `order_items` (
  `id` int NOT NULL,
  `order_id` int DEFAULT NULL,
  `product_id` int DEFAULT NULL,
  `quantity` int DEFAULT NULL,
  `size` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `size`) VALUES
(1, 1, 2, 1, 'L'),
(2, 2, 3, 1, 'M'),
(3, 2, 1, 1, 'S');

-- --------------------------------------------------------

--
-- Структура таблицы `products`
--

CREATE TABLE `products` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `sizes` varchar(100) NOT NULL,
  `description` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `products`
--

INSERT INTO `products` (`id`, `name`, `price`, `image_path`, `sizes`, `description`) VALUES
(1, 'Master', '1200.00', '../images/0001.jfif', 'S, M, L', 'Классическая футболка с логотипом группы'),
(2, 'Kills', '1500.00', '../images/shirt-17-min.png', 'L, XL, 2XL', 'Футболка с агрессивным дизайном и надписью Kills'),
(3, 'Original Metal', '1500.00', '../images/shirt-33-min.png', 'M, L, XL, 2XL', 'Футболка в стиле оригинального метала с классическим принтом'),
(4, 'Alive In Athens', '2000.00', '../images/shirt-32-min.png', 'M, L, XL, 2XL', 'Футболка с концертным дизайном Alive In Athens'),
(5, 'Fuckin Metal', '1700.00', '../images/shirt-25-min.png', 'XL, 2XL', 'Футболка с провокационным дизайном Fuckin Metal'),
(6, 'Reaper', '1800.00', '../images/shirt-08-min.png', 'XL', 'Футболка с изображением жнеца (Reaper)'),
(7, 'Artwork', '2400.00', '../images/other-29-min.png', 'XL, XXL', 'Стильные шорты с художественным принтом'),
(8, 'Guillotine', '4000.00', '../images/other-34-min.png', 'XL, 2XL', 'Теплое худи с дизайном Guillotine для концертов');

-- --------------------------------------------------------

--
-- Структура таблицы `tracks`
--

CREATE TABLE `tracks` (
  `id` int NOT NULL,
  `album_id` int DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `duration` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `tracks`
--

INSERT INTO `tracks` (`id`, `album_id`, `title`, `file_path`, `duration`) VALUES
(30, 1, 'МАСТЕР', '../audio/master-master.mp3', NULL),
(31, 1, 'БЕРЕГИСЬ', '../audio/master-beregis.mp3', NULL),
(32, 1, 'РУКИ ПРОЧЬ', '../audio/master-ruki-proch.mp3', NULL),
(33, 1, 'ЩИТ И МЕЧ', '../audio/master-shhit-i-mech.mp3', NULL),
(34, 1, 'ЕЩЁ РАЗ НОЧЬ', '../audio/master-eshhe-raz-noch.mp3', NULL),
(35, 1, 'КТО КОГО?', '../audio/Мастер - Кто Кого.mp3', NULL),
(36, 1, 'ВОЛЯ И РАЗУМ', '../audio/master-volja-i-razum.mp3', NULL),
(37, 1, 'ВСТАНЬ, СТРАХ ПРЕОДОЛЕЙ', '../audio/master-vstan-strakh-preodolejj.mp3', NULL),
(38, 2, 'Не Хотим', '../audio/master-ne-khotim.mp3', NULL),
(39, 2, 'Палачи', '../audio/master-palachi.mp3', NULL),
(40, 2, 'Мы Не Рабы', '../audio/master-my-ne-raby.mp3', NULL),
(41, 2, 'Когда Я Умру', '../audio/master-kogda-ja-umru.mp3', NULL),
(42, 2, 'Боже Храни', '../audio/master-bozhe-khrani-nashu-zlost.mp3', NULL),
(43, 2, 'Наплевать', '../audio/master-naplevat.mp3', NULL),
(44, 2, 'Амстердам', '../audio/master-amsterdam.mp3', NULL),
(45, 2, '2000 Лет', '../audio/master-2000-let.mp3', NULL),
(46, 2, 'Война', '../audio/master-vojjna.mp3', NULL),
(47, 2, '7 Кругов Ада', '../audio/master-sem-krugov-ada.mp3', NULL),
(48, 2, 'С Петлёй На Шее', '../audio/master-master.mp3', NULL),
(49, 3, 'ПЕСНИ МЕРТВЫХ', '../audio/master-pesni-mertvykh.mp3', NULL),
(50, 3, 'ДИКИЕ ГУСИ', '../audio/master-dikie-gusi.mp3', NULL),
(51, 3, 'ДАЙТЕ СВЕТ', '../audio/master-dajjte-svet.mp3', NULL),
(52, 3, 'ПЕПЕЛ НА ВЕТРУ', '../audio/master-pepel-na-vetru.mp3', NULL),
(53, 3, 'ТОЛЬКО ТЫ САМ', '../audio/master-tolko-ty-sam.mp3', NULL),
(54, 3, 'Я НЕ ХОЧУ ВОЙНЫ', '../audio/master-ja-ne-khochu-vojjny.mp3', NULL),
(55, 3, 'ТАТУ', '../audio/master-tatu.mp3', NULL),
(56, 3, 'НОЧЬ', '../audio/master-noch.mp3', NULL),
(57, 3, 'КОРАБЛЬ ДУРАКОВ', '../audio/master-korabl-durakov.mp3', NULL),
(58, 4, 'МЕСТА ХВАТИТ ВСЕМ', '../audio/master-mesta-khvatit-vsem.mp3', NULL),
(59, 4, 'ЛАБИРИНТ', '../audio/master-labirint.mp3', NULL),
(60, 4, 'ВИСОКОСНЫЙ ВЕК', '../audio/master-visokosnyjj-vek.mp3', NULL),
(61, 4, 'КРЕСТЫ', '../audio/master-kresty.mp3', NULL),
(62, 4, 'СОН', '../audio/master-son.mp3', NULL),
(63, 4, 'МЕТАЛЛ ДОКТОР', '../audio/master-metall-doktor.mp3', NULL),
(64, 4, 'ОХОТНИКИ ЗА СЧАСТЬЕМ', '../audio/master-okhotniki-za-schastem.mp3', NULL),
(65, 4, 'НИКТО НЕ ЗАБЫТ, НИЧТО НЕ ЗАБЫТО', '../audio/master-nikto-ne-zabyt-nichto-ne-zabyto.mp3', NULL),
(66, 4, 'ТАРАН', '../audio/master-taran.mp3', NULL),
(67, 5, 'INTRO GOLGOTHA', '../audio/master-intro-golgotha.mp3', NULL),
(68, 5, 'TALK OF THE DEVIL', '../audio/master-talk-of-the-devil.mp3', NULL),
(69, 5, 'DANGER', '../audio/master-danger.mp3', NULL),
(70, 5, 'FALLEN ANGEL', '../audio/master-fallen-angel.mp3', NULL),
(71, 5, 'LIVE TO DIE', '../audio/master-live-to-die.mp3', NULL),
(72, 5, 'TSAR', '../audio/master-tsar.mp3', NULL),
(73, 5, 'HEROES', '../audio/master-heroes.mp3', NULL),
(74, 5, 'ROMANCE', '../audio/master-romance.mp3', NULL),
(75, 6, 'ИГРА', '../audio/master-igra.mp3', NULL),
(76, 6, 'МАСТЕР СКОРБНЫХ ДЕЛ', '../audio/master-master-skorbnykh-del.mp3', NULL),
(77, 6, 'ВЕРА ГОРИТ НА КОСТРАХ', '../audio/master-vera-gorit-na-kostrakh.mp3', NULL),
(78, 6, '33 ЖИЗНИ', '../audio/master-33-zhizni.mp3', NULL),
(79, 6, 'ЭКСПРЕСС', '../audio/master-jekspress.mp3', NULL),
(80, 6, 'ГЛОТОК ОГНЯ', '../audio/master-glotok-ognja.mp3', NULL),
(81, 6, 'ВОЙНА МИРОВ', '../audio/master-vojna-mirov.mp3', NULL),
(82, 6, 'ХЕВИ ЛАМБАДА', '../audio/master-khevi-lambada.mp3', NULL),
(83, 6, 'СНЕЖНЫЙ ОХОТНИК', '../audio/master-snezhnyjj-okhotnik.mp3', NULL),
(84, 6, 'СТИХИЯ', '../audio/master-stikhija.mp3', NULL),
(85, 6, 'ДЕТИ ПОДЗЕМЕЛЬЯ', '../audio/master-deti-podzemelja.mp3', NULL),
(86, 6, 'ВОЙНА МИРОВ', '../audio/master-vojjna.mp3', NULL);

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `albums`
--
ALTER TABLE `albums`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `band_history`
--
ALTER TABLE `band_history`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `band_members`
--
ALTER TABLE `band_members`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `concerts`
--
ALTER TABLE `concerts`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `gallery`
--
ALTER TABLE `gallery`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `news`
--
ALTER TABLE `news`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Индексы таблицы `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `tracks`
--
ALTER TABLE `tracks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `album_id` (`album_id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `albums`
--
ALTER TABLE `albums`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT для таблицы `band_history`
--
ALTER TABLE `band_history`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT для таблицы `band_members`
--
ALTER TABLE `band_members`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT для таблицы `concerts`
--
ALTER TABLE `concerts`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT для таблицы `gallery`
--
ALTER TABLE `gallery`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT для таблицы `news`
--
ALTER TABLE `news`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT для таблицы `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT для таблицы `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT для таблицы `products`
--
ALTER TABLE `products`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT для таблицы `tracks`
--
ALTER TABLE `tracks`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=87;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Ограничения внешнего ключа таблицы `tracks`
--
ALTER TABLE `tracks`
  ADD CONSTRAINT `tracks_ibfk_1` FOREIGN KEY (`album_id`) REFERENCES `albums` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

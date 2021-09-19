--
-- Table structure for table `tasks`
--

CREATE TABLE `tasks` (
  `id` int(8) NOT NULL AUTO_INCREMENT,
  `poster_id` int(11) NOT NULL DEFAULT '0',
  `user_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `task` LONGTEXT NOT NULL,
  `created` int(11) NOT NULL DEFAULT '0',
  `updated` int(11) NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `user_hash` varchar(255) NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `users`
--

INSERT INTO `tasks` (`id`, `poster_id`, `user_name`, `email`, `task`, `created`, `updated`, `status`, `user_hash`) VALUES
(1, 0, 'Иван Петров', 'ivan@hot.net', 'Имеется два одинаковых граненых стакана емкостью 150 мл. каждый. Один стакан полностью залит водой , а другой ровно на половину 40-ка градусной водкой. 
Как сделать в одном из стаканов 15-ти процентный раствор спирта и сколько мл. получится?', 1582194600, 0, 1, null),
(2, 0, 'Павел Дуров', 'pavel@hot.net', 'Unix-время — система описания моментов во времени, принятая в Unix и других POSIX-совместимых операционных системах. Определяется как количество секунд, прошедших с полуночи 1 января 1970 года; этот момент называют «эпохой Unix»', 1582204600, 0, 1, null),
(3, 0, 'John Doo', 'john@hot.net', 'The unix time stamp is a way to track time as a running total of seconds. This count starts at the Unix Epoch on January 1st, 1970 at UTC. Therefore, the unix time stamp is merely the number of seconds between a particular date and the Unix Epoch. It should also be pointed out (thanks to the comments from visitors to this site) that this point in time technically does not change no matter where you are located on the globe. This is very useful to computer systems for tracking and sorting dated information in dynamic and distributed applications both online and client side.', 1582394600, 0, 0, null),
(4, 0, 'Nike Name', 'nike@hot.net', 'В момент времени 00:00:00 UTC 1 января 1970 года (четверг) Unix-время равно нулю. Начиная с этого времени, число возрастает на определённое количество в день. Таким образом, к примеру, 16 сентября 2004 года в 00:00:00, спустя 12677 дней после начала отсчета Unix-времени, время будет представлено числом 12 677 × 86 400 = 1 095 292 800, или в случае с 17 декабря 2003 года в 00:00:00, через 12403 дня после начала отсчёта время будет являться числом 12403 × 86 400 = 1 071 619 200. Расчеты могут быть также произведены в обратном направлении используя отрицательные числа. К примеру, дата 4 октября 1957 года 00:00:00, а это 4472 дня до начала отсчета, представлена в Unix-времени числом −4472 × 86 400 = −386 380 800', 1582494600, 0, 0, null);

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(8) NOT NULL AUTO_INCREMENT,
  `group_id` int(11) NOT NULL DEFAULT '0',
  `user_name` varchar(100) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `group_id`, `user_name`, `full_name`, `email`, `password`) VALUES
(1, 1, 'admin', 'Administrator', 'admin@hot.net', '$2y$10$jGOahrIevD3GVsofPyY.CuSomDlGpC9T3kohTG4WRInhrErnaEqk6');

--
-- Table structure for table `autologin`
--

CREATE TABLE `autologin` (
  `id` int(8) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `key_id` varchar(40) DEFAULT NULL,
  `user_agent` varchar(150) DEFAULT NULL,
  `user_ip` varchar(40) DEFAULT NULL,
  `last_login` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
COMMIT;

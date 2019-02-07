DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` mediumint(8) unsigned NOT NULL auto_increment,
  `first_name` varchar(100) collate latin1_general_ci NOT NULL,
  `surname` varchar(100) collate latin1_general_ci NOT NULL,
  `email` varchar(100) collate latin1_general_ci NOT NULL,
  `username` varchar(100) collate latin1_general_ci NOT NULL,
  `password` varchar(100) collate latin1_general_ci NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=6 ;

INSERT INTO `users` (`id`, `first_name`, `surname`, `email`, `username`, `password`) VALUES
(1, 'Joe', 'Soap', 'joe@example.com', 'joe', 'joespass'),
(2, 'Bob', 'Jones', 'bob@example.com', 'bob', 'bobspass'),
(3, 'Bill', 'Adams', 'bill@example.com', 'bill', 'billspass'),
(4, 'Jenny', 'Watson', 'jenny@example.com', 'jenny', 'jennyspass'),
(5, 'Angela', 'Bell', 'angela@example.com', 'angela', 'angelaspass');

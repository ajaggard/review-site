DROP TABLE IF EXISTS `comments`;
DROP TABLE IF EXISTS `articles`;
DROP TABLE IF EXISTS `media_types`;

-- Table for the media types a review can be for
CREATE TABLE `media_types` (
  `id`          int(11)         NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `name`        varchar(250)    DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Initial media types
INSERT INTO `media_types` (`id`, `name`) VALUES
(2, 'Film'),
(3, 'TV'),
(4, 'Novel'),
(5, 'Comic'),
(6, 'Game');

-- Table for all articles (reviews)
CREATE TABLE `articles` (
  `id`          int(11)         NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `type_id`     int(11)         NOT NULL,
  `featured`    tinyint(1)      DEFAULT 0,
  `image_link`  varchar(500)    DEFAULT NULL,
  `title`       varchar(500)    NOT NULL,
  `author`      varchar(250)    NOT NULL,
  `content`     text            DEFAULT NULL,
  `date`        timestamp       NOT NULL DEFAULT current_timestamp(),
  
  FOREIGN KEY (`type_id`) REFERENCES `media_types` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table for comments on articles
CREATE TABLE `comments` (
  `id`          int(11)         NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `article_id`  int(11)         NOT NULL,
  `reply_to_id` int(11)         DEFAULT NULL                            COMMENT '(Optional) The id of the comment this is a reply to.',
  `author`      varchar(250)    NOT NULL,
  `email`       varchar(250)    NOT NULL,
  `content`     text            DEFAULT NULL,
  `date`        timestamp       NOT NULL DEFAULT current_timestamp(),
  
  FOREIGN KEY (`article_id`) REFERENCES `articles` (`id`),
  FOREIGN KEY (`reply_to_id`) REFERENCES `comments` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
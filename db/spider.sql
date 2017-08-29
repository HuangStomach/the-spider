CREATE TABLE `record` (
    `id`        int(10)     unsigned NOT NULL AUTO_INCREMENT,
    `fqdn`      varchar(100)         NOT NULL,
    `name`      varchar(250)         NOT NULL,
    `state`     varchar(100)         NOT NULL,
    `output`    varchar(500)                 ,
    `type`      varchar(100)                 ,
    `time`      datetime                     ,
    PRIMARY KEY (`id`)
) ENGINE=INNODB  DEFAULT CHARSET=utf8;

CREATE TABLE `server` (
    `id`        int(10)     unsigned NOT NULL AUTO_INCREMENT,
    `name`      varchar(250)         NOT NULL,
    `fqdn`      varchar(100)         NOT NULL,
    `ip`        varchar(500)         NOT NULL,
    `status`    varchar(100)                 ,
    `free`      int(10)                      ,
    `top`       int(10)                      ,
    `report`    int(10)                      ,
    `update`    datetime                     ,
    PRIMARY KEY (`id`)                       ,
    UNIQUE (`fqdn`)
) ENGINE=INNODB  DEFAULT CHARSET=utf8;
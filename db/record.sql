CREATE TABLE `record` (
    `id`        int(10)     unsigned NOT NULL AUTO_INCREMENT,
    `name`      varchar(250)         NOT NULL,
    `state`     varchar(100)         NOT NULL,
    `output`    varchar(500)                 ,
    `type`      varchar(100)                 ,
    `time`      datetime             NOT NULL,
    PRIMARY KEY (`id`)
);
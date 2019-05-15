<?php namespace Eckinox\User\Migrate;

use Eckinox\User\Model\User;

$this->model( new User() )->create("2018-05-26", function($table) {
    
    return [
        "CREATE TABLE IF NOT EXISTS `$table` (
            `id`            int(11) unsigned NOT NULL AUTO_INCREMENT,
            `first_name`    varchar(35) DEFAULT NULL,
            `last_name`     varchar(35) DEFAULT NULL,
            `email`         varchar(150) DEFAULT NULL,
            `address`       varchar(115) DEFAULT NULL,            
            `zip_code`      varchar(15) DEFAULT NULL,
            `province`      varchar(45) DEFAULT NULL,
            `country`       varchar(3) DEFAULT NULL,
            `phone`         varchar(15) DEFAULT NULL,
            `mobile`        varchar(15) DEFAULT NULL,
            `ext`           varchar(15) DEFAULT NULL,
            `password`      varchar(255) DEFAULT NULL,
            `username`      varchar(35) DEFAULT NULL,
            `group_list`    varchar(255) DEFAULT NULL,
            `date_created`  timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `last_updated`  timestamp NULL ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8"
    ];

});
<?php namespace Eckinox\User\Migrate;

use Eckinox\User\Model\Group;

$this->model( new Group() )->create("2018-05-26", function($table) {
    
    return [
        "CREATE TABLE IF NOT EXISTS `$table` (
            `id`            int(11) unsigned NOT NULL AUTO_INCREMENT,
            `name`          varchar(255) NOT NULL,
            `privileges`    text NULL,
            `date_created`  timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `last_updated`  timestamp NULL ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8"
    ];

});
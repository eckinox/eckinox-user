<?php namespace Eckinox\User\Migrate;

use Eckinox\User\Model\User;

$this->model( new User() )->update("2018-12-14", function($table) {

    return [
        "ALTER TABLE `$table`
            CHANGE `date_created` `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            CHANGE `last_updated` `updated_at` TIMESTAMP on update CURRENT_TIMESTAMP NULL DEFAULT NULL"
    ];

});

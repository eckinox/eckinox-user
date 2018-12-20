<?php
namespace Eckinox\User;

use Eckinox\{
    Event,
    Component,
    singleton
};

class User extends Component {
    use singleton;

    public function initialize() {
        parent::initialize();
        
        Event::make()->on('eckinox.migration.migrate', function($e) {
            Migrate\Migrate::instance()->autoload()->migrate();
        });
    }
}
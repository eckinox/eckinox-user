<?php namespace Eckinox\User;

use Eckinox\{
    Annotation,
    Event
};

class Privileges {
    public function from_route($route, $controller) {
        return Annotation::instance()->annotations()[ get_class($controller) ]['methods'][$route]['privilege'] ?? false;
    }
}

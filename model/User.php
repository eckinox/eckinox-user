<?php namespace Eckinox\User\Model;

use Eckinox\{
    Event
};

use Eckinox\Nex\{
    cookie,
    sessions,
    cookies
};

use Eckinox\Nex_model_tools\{
    validation
};

/**
 *  @table "name" : "user_users"
 *
 *  @field {
 *      "id" : "id",
 *      "first_name" : { "type" : "string", "null" : true, "size" : 35 },
 *      "last_name" : { "type" : "string", "null" : true, "size" : 35 },
 *      "email" : { "type" : "string", "null" : true, "size" : 150 },
 *      "address" : { "type" : "string", "null" : true, "size" : 115 },
 *      "zip_code" : { "type" : "string", "null" : true, "size" : 15 },
 *      "city" : { "type" : "string", "null" : true, "size" : 255 },
 *      "province" : { "type" : "string", "null" : true, "size" : 45 },
 *      "country" : { "type" : "string", "null" : true, "size" : 3 },
 *      "phone" : { "type" : "string", "null" : true, "size" : 15 },
 *      "ext" : { "type" : "string", "null" : true, "size" : 15 },
 *      "mobile" : { "type" : "string", "null" : true, "size" : 15 },
 *      "password" : { "type" : "string", "null" : true },
 *      "username" : { "type" : "string", "null" : true, "size" : 35},
 *      "guid" : { "type" : "string", "null" : true, "size" : 16 },
 *      "group_list" : { "type" : "string", "null" : true },
 *      "privileges" : { "type" : "text", "null" : true },
 *      "created_at" : { "type" : "created at" },
 *      "updated_at" : { "type" : "updated at" }
 *  }
 *
 *  @validation {
 *      "id" : "readonly",
 *      "first_name" : "alpha",
 *      "last_name" : "alpha",
 *      "email" : {
 *          "type" : "email",
 *          "unique" : true
 *      },
 *      "zip_code" : {
 *          "type" : "zipcode",
 *          "country" : "country"
 *      },
 *      "password" : {
 *          "type" : "password",
 *          "confirm" : "confirm_password"
 *      },
 *      "username" : {
 *          "type" : "username",
 *          "unique" : true
 *      },
 *      "guid" : "readonly",
 *      "created_at" : "readonly",
 *      "updated_at" : "readonly"
 *  }
 */
class User extends \Eckinox\Nex\Model {
    use cookies, sessions, validation;

    public $tablename = 'user_users' ;

    # Set from config
    protected $use_session = true;
    protected $use_cookie  = true;
    protected $logged      = false;

    /**
     * Unique field used to identify an account
     */
    public $connection_fields = 'email';

    public function __construct() {
        parent::__construct();

        $this->event = Event::instance();
    }

    public function connect() {
        $this->logged();
        return $this;
    }

    /**
     *
     * @return type
     */
    public function logged() {
        return $this->logged ?: $this->from_session() || $this->from_cookie() || $this->system();
    }

    public function loading_done() {
        return $this->each(function() {
            $this['privileges'] = json_decode($this['privileges'] ?: '[]', true);
        });
    }

    public function save($reload = false, $mode = null) {
        $this['privileges'] = json_encode($tmp = $this['privileges']);
        parent::save($reload, $mode);

        $this['privileges'] = $tmp;
        return $this;
    }

    public function set_password($password) {
        $this['password'] = $password;
        return $this->hash_password();
    }

    public function hash_password() {
        $this['password'] = password_hash($this['password'], PASSWORD_DEFAULT);
        return $this;
    }

    public function authenticate($field, $password, $force = false) {
        if ( ! $this->logged() ) {
            $this->where($this->connection_fields, $field)->load_all(1);

            if ( $this->loaded() ) {
                if ( $force || password_verify($password, $this['password'] )) {
                    $this->event->trigger('User.authenticated');

                    $this->use_session && $this->session("User.user.id", $this['id']);
                    $this->use_cookie  && $this->cookie("User.user.id", $this['id']);

                    return $this->logged = true;
                }
                else {
                    $this->event->trigger('User.incorrect_password');
                }
            }
            else {
                $this->event->trigger('User.not_found');
            }

            $this->container = [];
            $this->logged = false;
        }

        return $this->logged;
    }

    /**
     * Authenticate using session data
     *
     * @return boolean
     */
    public function from_session() {
        if ( ! $this->use_session ) {
            return false;
        }

        if ( $id = $this->session('User.user.id') ) {
            $this->load($id);

            return $this->logged = true;
        }

        return $this->logged = false;
    }

    /**
     * Authenticate using cookie data
     *
     * @return boolean
     */
    public function from_cookie() {
        if ( ! $this->use_cookie ){
            return false;
        }

        if ( $id = $this->cookie("User.user.id") ) {
            $this->load($id);
            return $this->logged = true;
        }

        return $this->logged = false;
    }

    public function system() {
        return false; # todo? Server::make()->lan();
    }

    /**
     * Force user disconnection and handle memory trashing
     */
    public function logout() {
        $this->logged = false;
        cookie::delete('User.user.id');
        $this->session('User.user.id', null);

        return $this;
    }

    public function fullname() {
        return trim("{$this['first_name']} {$this['last_name']}");
    }

    public function privilege($key) {
        return in_array($key, $this['privileges'] ?? []) || in_array("*", $this['privileges']);
    }
}

<?php

namespace Admin\Models;
use Core\Models\Generator\Url;

/**
 * Class Login
 * @package Admin\Models
 */
class Login {

    /**
     * @param string $action
     */
    public function __construct($action = "") {
        if (method_exists($this, $action))
            call_user_func([$this, $action]);       
    }

    /**
     *
     */
    public function login() {
        if (isset($_POST['email'])) {
            if ($_SESSION['user']->email == $_POST['email'] && $_SESSION['user']->password == sha1($_POST['password'] . $_SESSION['user']->salt)) {
                $_SESSION['admin_loggedIn'] = true;
                header("Location:" . Url::convertUrl("index.php?m=admin"));
                exit();
            } else {
                $view = new \Admin\View\Login();
                $view->assign("message", "The entered data is not correct!");
                $view->show();
            }
        } else {
            $view = new \Admin\View\Login();
            $view->assign("message", "");
            $view->show();
        }
    }

    /**
     * @return bool
     */
    public static function loggedIn() {
        return (isset($_SESSION['admin_loggedIn']) && $_SESSION['admin_loggedIn'] === true && isset($_SESSION['user']));
    }

    /**
     *
     */
    public static function loginRequired() {
        if (!self::loggedIn()) {
            header("Location:" . Url::convertUrl("index.php?m=admin&action=login"));
            exit();
        } else
            return;
    }

}

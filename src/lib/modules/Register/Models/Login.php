<?php

namespace Register\Models;

use Core\Models\Db\Row\User;
use Core\Models\Db\Table\Users;
use Core\Models\Generator\Url;
use Core\Models\Request;
use Core\View\Ajax\View;

/**
 * Class Login
 * @package Register\Models
 */
class Login {

    /**
     * @param bool $autologin
     * @return bool|null|\Zend_Db_Table_Row_Abstract
     */
    public static function checkAutoLogin($autologin = true) {
        if (!isset($_COOKIE['cunity-login']) || !isset($_COOKIE['cunity-login-token']))
            return false;
        $users = new Users();
        $user = $users->search("username", base64_decode($_COOKIE['cunity-login']));
        if (md5($user->salt . "-" . $user->registered . "-" . $user->userhash) == $_COOKIE['cunity-login-token']) {
            if ($autologin) {
                $user->setLogin(true);
                header("Location:" . Url::convertUrl("index.php?m=profile"));
                exit();
            } else
                return $user;
        }
        return false;
    }

    /**
     * @return bool
     */
    public static function loggedIn() {
        return (isset($_SESSION['loggedIn']) && $_SESSION['loggedIn'] === true && isset($_SESSION['user']) && $_SESSION['user'] instanceof User);
    }

    /**
     *
     */
    public static function loginRequired() {
        if (!self::loggedIn()) {
            $res = self::checkAutoLogin(false);
            if ($res !== false && $res instanceof User) {
                $res->setLogin(true);
                header("Location:" . Url::convertUrl("index.php?m=profile"));
            } else if (!isset($_GET['m']) || $_GET['m'] != "start") {
                if (!Request::isAjaxRequest()) {
                    header("Location:" . Url::convertUrl("index.php?m=start"));
                } else {
                    $view = new View(false);
                    $view->addData(["session" => 0]);
                    $view->sendResponse();
                }
            }
        } else
            return;
        exit();
    }

}

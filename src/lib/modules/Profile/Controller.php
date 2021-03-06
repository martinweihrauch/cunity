<?php

namespace Profile;

use Core\ModuleController;
use Register\Models\Login;

/**
 * Class Controller
 * @package Profile
 */
class Controller implements ModuleController
{

    /**
     *
     */
    public function __construct()
    {
        Login::loginRequired();
        $this->handleRequest();
    }

    /**
     *
     */
    private function handleRequest()
    {
        if (isset($_GET['action']) && ($_GET['action'] == "edit" ||
                $_GET['action'] == "cropImage"))
            new Models\ProfileEdit();
        else
            new Models\Profile();
    }

    /**
     * @param $user
     * @return mixed|void
     */
    public static function onRegister($user)
    {

    }

    /**
     * @param $user
     * @return mixed|void
     */
    public static function onUnregister($user)
    {

    }

}

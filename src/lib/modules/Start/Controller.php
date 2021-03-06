<?php

namespace Start;

use Core\ModuleController;
use Register\Models\Login;

/**
 * Class Controller
 * @package Start
 */
class Controller implements ModuleController
{

    /**
     *
     */
    public function __construct()
    {
        Login::checkAutoLogin(true);
        new View\Startpage();
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

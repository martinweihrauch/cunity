<?php

namespace Gallery;

use Core\ModuleController;
use Register\Models\Login;

/**
 * Class Controller
 * @package Gallery
 */
class Controller implements ModuleController
{

    /**
     * @var array
     */
    private $_allowedActions = [
        "loadImages",
        "overview",
        "loadImage",
        "edit",
        "deleteImage",
        "create",
        "upload",
        "deleteAlbum"
    ];

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
        if (!isset($_GET['action']) || empty($_GET['action']))
            new View\Albums();
        elseif (
            isset(
                $_GET['action']
            ) &&
            !empty(
            $_GET['action']
            ) &&
            in_array(
                $_GET['action'],
                $this->_allowedActions
            )
        )
            new Models\Process($_GET['action']);
        elseif (
            isset(
                $_GET['action']
            ) &&
            !empty(
            $_GET['action']
            )
        )
            new Models\Process("loadAlbum");
    }

    /**
     * @param $user
     * @return mixed|void
     */
    public static function onRegister($user)
    {
        $albums = new Models\Db\Table\Gallery_Albums();
        $albums->newProfileAlbums($user->userid);
    }

    /**
     * @param $user
     * @return mixed|void
     */
    public static function onUnregister($user)
    {
        $albums = new Models\Db\Table\Gallery_Albums();
        $albums->deleteAlbumsByUser($user->userid);
    }

}

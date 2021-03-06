<?php

namespace Admin\View\Abstractables;

/**
 * Class View
 * @package Admin\View\Abstractables
 */
class View extends \Core\View\View
{

    /**
     * @throws \Exception
     */
    public function __construct()
    {
        parent::__construct();
        $this->_templateDir = "Admin";
    }

    /**
     * @param $adminModule
     * @param $file
     * @throws \Core\Exception
     */
    public function registerCss($adminModule, $file)
    {
        parent::registerCss("admin", "../" . $adminModule . "/css/" . $file);
    }

}

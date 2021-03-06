<?php

namespace Admin\View;

use Admin\View\Abstractables\View;

/**
 * Class Statistics
 * @package Admin\View
 */
class Statistics extends View
{

    /**
     * @var bool
     */
    protected $_useWrapper = false;

    /**
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->_templateFile = "statistics/" . $_GET['x'] . ".tpl";
        $this->registerCss("statistics", $_GET['x']);
        $this->show();
    }

}

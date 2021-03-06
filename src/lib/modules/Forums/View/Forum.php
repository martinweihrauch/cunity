<?php
namespace Forums\View;

use Core\View\View;

/**
 * Class Forum
 * @package Forums\View
 */
class Forum extends View
{

    /**
     * @var string
     */
    protected $_templateDir = "forums";
    /**
     * @var string
     */
    protected $_templateFile = "forum.tpl";
    /**
     * @var array
     */
    protected $_metadata = ["title" => "Forum"];

    /**
     * @throws \Core\Exception
     * @throws \Exception
     */
    public function __construct()
    {
        parent::__construct();
        $this->registerCss("forums", "forum");
        $this->registerScript("forums", "forum");
        $this->registerScript("forums", "category-cloud");
    }
}
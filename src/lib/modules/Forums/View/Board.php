<?php

namespace Forums\View;

use Core\View\View;

/**
 * Class Board
 * @package Forums\View
 */
class Board extends View
{

    /**
     * @var string
     */
    protected $_templateDir = "forums";
    /**
     * @var string
     */
    protected $_templateFile = "board.tpl";
    /**
     * @var array
     */
    protected $_metadata = ["title" => "Board"];

    /**
     * @throws \Core\Exception
     * @throws \Exception
     */
    public function __construct()
    {
        parent::__construct();
        $this->registerCss("forums", "board");
        $this->registerScript("forums", "board");
        $this->registerCunityPlugin(
            "summernote",
            [
            "css/summernote.css",
            "js/summernote.min.js"
            ]
        );
        $this->registerScript("forums", "category-cloud");
    }
}

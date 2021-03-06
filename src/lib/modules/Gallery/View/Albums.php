<?php
namespace Gallery\View;

use Core\View\View;

/**
 * Class Albums
 * @package Gallery\View
 */
class Albums extends View
{

    /**
     * @var string
     */
    protected $_templateDir = "gallery";
    /**
     * @var string
     */
    protected $_templateFile = "albums.tpl";
    /**
     * @var array
     */
    protected $_metadata = ["title" => "Albums"];

    /**
     * @throws \Core\Exception
     * @throws \Exception
     */
    public function __construct()
    {
        parent::__construct();
        $this->registerCss("gallery", "albums");
        $this->registerScript("gallery", "albums");
        $this->show();
    }
}
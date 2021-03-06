<?php

namespace Friends\View;

use Core\View\View;

/**
 * Class FriendList
 * @package Friends\View
 */
class FriendList extends View
{

    /**
     * @var string
     */
    protected $_templateDir = "friends";
    /**
     * @var string
     */
    protected $_templateFile = "friendslist.tpl";
    /**
     * @var string
     */
    protected $_languageFolder = "Friends/languages";
    /**
     * @var array
     */
    protected $_metadata = ["title" => "My friends"];

    /**
     * @throws \Core\Exception
     * @throws \Exception
     */
    public function __construct()
    {
        parent::__construct();
        $this->registerScript("friends", "friendslist");
        $this->registerCss("friends", "friends");
        $this->show();
    }
}



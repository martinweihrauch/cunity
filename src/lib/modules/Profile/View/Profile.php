<?php

namespace Profile\View;

use Core\View\View;

/**
 * Class Profile
 * @package Profile\View
 */
class Profile extends View
{

    /**
     * @var string
     */
    protected $_templateDir = "profile";
    /**
     * @var string
     */
    protected $_templateFile = "profile.tpl";
    /**
     * @var array
     */
    protected $_metadata = ["title" => "Profile"];

    /**
     * @throws \Core\Exception
     * @throws \Exception
     */
    public function __construct()
    {
        parent::__construct();
        $this->registerScript("profile", "profile");
        $this->registerScript("gallery", "albums");
        $this->registerScript("newsfeed", "newsfeed");
        $this->registerCss("profile", "profile");
        $this->registerCss("newsfeed", "newsfeed");
        $this->registerCss("gallery", "albums");
        $this->registerCss("friends", "friends");
        $this->registerCss("gallery", "lightbox");
        $this->registerScript("gallery", "jquery.blueimp-gallery");
        $this->registerScript("gallery", "lightbox");
    }

    /**
     *
     */
    public function render()
    {
        $this->show();
    }

}

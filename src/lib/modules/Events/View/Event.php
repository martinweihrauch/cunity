<?php

namespace Events\View;

use Core\View\View;

/**
 * Class Event
 * @package Events\View
 */
class Event extends View
{

    /**
     * @var string
     */
    protected $_templateDir = "events";
    /**
     * @var string
     */
    protected $_templateFile = "event.tpl";
    /**
     * @var array
     */
    protected $_metadata = ["title" => "Event"];

    /**
     * @throws \Core\Exception
     * @throws \Exception
     */
    public function __construct()
    {
        parent::__construct();
        $this->registerCss("events", "event");
        $this->registerCss("newsfeed", "newsfeed");
        $this->registerScript("newsfeed", "newsfeed");
        $this->registerScript("events", "event");

        $this->registerCss("gallery", "lightbox");
        $this->registerScript("gallery", "jquery.blueimp-gallery");
        $this->registerScript("gallery", "lightbox");
    }

}

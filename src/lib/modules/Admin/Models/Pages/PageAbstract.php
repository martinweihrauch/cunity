<?php
namespace Admin\Models\Pages;

/**
 * Class PageAbstract
 * @package Admin\Models\Pages
 */
abstract class PageAbstract {

    /**
     * @var array
     */
    protected $assignments = [];

    /**
     * @param $class
     */
    public function render($class){
        $class = "\\Admin\\View\\".ucfirst($class);
        $view = new $class;
        $view->assign($this->assignments);
        $view->show();
    }
}

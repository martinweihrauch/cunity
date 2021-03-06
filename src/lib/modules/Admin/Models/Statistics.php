<?php

namespace Admin\Models;
use Comments\Models\Db\Table\Comments;
use Gallery\Models\Db\Table\Gallery_Images;
use Likes\Models\Db\Table\Likes;
use Messages\Models\Db\Table\Messages;
use Newsfeed\Models\Db\Table\Posts;

/**
 * Class Statistics
 * @package Admin\Models
 */
class Statistics {

    /**
     * @var array
     */
    private $tables = [];

    /**
     *
     */
    public function __construct() {
        $this->tables['messages'] = new Messages();
        $this->tables['comments'] = new Comments();
        $this->tables['likes'] = new Likes();
        $this->tables['post'] = new Posts();
        $this->tables['images'] = new Gallery_Images();
    }

    /**
     * @param $name
     * @return mixed
     */
    private function __get($name) {
        $returnValue = NULL;
        if (array_key_exists($name, $this->tables))
        {
            $returnValue = $this->tables[$name];
        }

        return $returnValue;
    }

}

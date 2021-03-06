<?php

namespace Core\View\Ajax;

/**
 * Class View
 * @package Core\View\Ajax
 */
class View extends \Core\View\View
{

    /**
     * @var bool
     */
    private $_status = true;
    /**
     * @var array
     */
    private $_values = [];

    /**
     * @param bool $status
     */
    public function __construct($status = false)
    {
        $this->setStatus($status);
    }


    /**
     * @param $status
     */
    public function setStatus($status)
    {
        $this->_status = ($status === true);
    }

    /**
     * @param array $values
     */
    public function addData(array $values)
    {
        $this->_values = $values;
    }

    /**
     *
     */
    public function sendResponse()
    {
        header('Content-type: application/json');
        header("Cache-Control: no-cache, must-revalidate"); // Disable Cache
        header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
        exit(
        json_encode(
            array_merge(
                ["status" => $this->_status],
                $this->_values
            )
        )
        );
    }

}



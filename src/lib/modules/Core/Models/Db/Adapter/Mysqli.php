<?php

namespace Core\Models\Db\Adapter;

/**
 * Class Mysqli
 * @package Core\Models\Db\Adapter 
 */
class Mysqli extends \Zend_Db_Adapter_Mysqli {

    /**
     *
     * @var String
     */
    protected $_dbprefix = "cunity";

    /**
     * @param \Zend_Config_Abstract $config
     */
    public function __construct(\Zend_Config_Xml $config) {
        parent::__construct($config->db->params);
        $this->_dbprefix = $config->db->params->table_prefix;
    }

    /**
     * 
     * @return String
     */
    public function get_dbprefix() {
        return $this->_dbprefix . '_';
    }

}

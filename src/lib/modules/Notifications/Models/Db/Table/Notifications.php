<?php

namespace Notifications\Models\Db\Table;

use Core\Models\Db\Abstractables\Table;
use Notifications\Models\Notifier;

/**
 * Class Notifications
 * @package Notifications\Models\Db\Table
 */
class Notifications extends Table {

    /**
     * @var string
     */
    protected $_name = 'notifications';
    /**
     * @var string
     */
    protected $_primary = 'id';

    /**
     *
     */ 
    public function __construct() {
        parent::__construct();
    }

    /**
     * @param array $data
     * @return bool
     */
    public function insertNotification(array $data) {
        return (1 == $this->insert($data));
    }

    /**
     * @return array
     */
    public function getNotifications() {
        $result = [];
        $query = $this->getAdapter()->select()->from(["n" => $this->_name])
                ->joinLeft(["u" => $this->_dbprefix . "users"], "n.ref_userid=u.userid", ["name", "username"])
                ->joinLeft(["pi" => $this->_dbprefix . "gallery_images"], "pi.id = u.profileImage", ['filename AS pimg', 'albumid AS palbumid'])
                ->where("n.userid=?", $_SESSION['user']->userid)
                ->order("n.unread DESC")
                ->limit(5);
        $res = $this->getAdapter()->fetchAll($query);
        for ($i = 0; $i < count($res); $i++) {
            $d = Notifier::getNotificationData($res[$i]["type"]);
            $res[$i]["message"] = \sprintf($d, $res[$i]["name"]);
            $res[$i]["target"] = \Core\Models\Generator\Url::convertUrl($res[$i]["target"]);
            if ($res[$i]["unread"] == 1)
                $result["new"] ++;
        }
        $result["all"] = $res;
        return $result;
    }

    /**
     * @param $id
     * @return bool
     */
    public function read($id) {
        return ($this->update(["unread" => 0], $this->getAdapter()->quoteInto("id=?", $id)) !== false);
    }

}

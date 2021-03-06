<?php

namespace Profile\Models\Db\Table;

use Core\Models\Db\Abstractables\Table;

/**
 * Class Privacy
 * @package Profile\Models\Db\Table
 */
class Privacy extends Table {

    /**
     * @var string
     */
    protected $_name = 'privacy';
    /**
     * @var array
     */
    private static $privacies = ["message" => 3, "visit" => 3, "posts" => 3, "search" => 3];

    /**
     *
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * @param $type
     * @param $userid
     * @return bool
     */
    public function checkPrivacy($type, $userid) {
        if($userid == $_SESSION['user']->userid)
            return true;        
        $pri = $this->getPrivacy($type, $userid);        
        if ($pri == 3)
            return true;
        else if ($pri == 1 && $_SESSION['user']->isFriend($userid))
            return true;
        return false;
    }

    /**
     * @param bool $type
     * @param int $userid
     * @return array
     */
    public function getPrivacy($type = false, $userid = 0) {
        if ($userid == 0)
            $userid = $_SESSION['user']->userid;
        if ($type == false) {
            $res = $this->fetchAll($this->select()->where("userid=?", $userid));
            $result = [];
            foreach ($res AS $p)
                $result[$p->type] = $p->value;
            return $result;
        } else {
            $res = $this->fetchAll($this->select()->where("userid=?", $userid)->where("type=?", $type));
            if ($res == NULL || $res == false)
                return $this::$privacies[$type];
            return $res->value;
        }
    }

    /**
     * @param $userid
     * @param $privacyName
     * @param int $val
     * @return mixed
     */
    public function updatePrivacy($userid, $privacyName, $val = 0) {
        if (is_array($privacyName)) {
            $res = [];
            foreach ($privacyName AS $type => $val)
                $res[] = $this->updatePrivacy($userid, $type, $val);
            return !in_array(false, $res);
        }
        $res = $this->fetchRow($this->select()->where("userid=?", $userid)->where("type=?", $privacyName));
        if ($res !== NULL) {
            $res->value = $val;
            return $res->save();
        } else {
            return $this->insert(["userid" => $userid, "type" => $privacyName, "value" => $val]);
        }
    }

}

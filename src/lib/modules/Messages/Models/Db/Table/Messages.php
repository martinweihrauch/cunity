<?php

namespace Messages\Models\Db\Table;

use Core\Models\Db\Abstractables\Table;

/**
 * Class Messages
 * @package Messages\Models\Db\Table
 */
class Messages extends Table {

    /**
     * @var string
     */
    protected $_name = 'messages';
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
     * @param $userid
     * @param $cid
     * @return bool
     */
    public function deleteByUser($userid, $cid) {
        return (0 < $this->delete([$this->getAdapter()->quoteInto("sender=?", $userid), $this->getAdapter()->quoteInto("conversation=?", $cid)]));
    }

    /**
     * @param $conversation_id
     * @param int $offset
     * @param int $refresh
     * @return array
     */
    public function loadByConversation($conversation_id, $offset = 0, $refresh = 0) {
        $query = $this->getAdapter()->select()
                ->from($this->_dbprefix . "messages AS m")
                ->where("conversation = ?", $conversation_id)
                ->join($this->_dbprefix . "users AS us", "m.sender = us.userid", ["us.username", "us.name"])
                ->joinLeft($this->_dbprefix . "gallery_images AS img", "img.id = us.profileImage", ["filename AS pimg"])
                ->order("time DESC");

        if ($refresh > 0)
            $query->where("m.id > ?", $refresh);
        else
            $query->limit(20, $offset);
        return $this->getAdapter()->fetchAll($query);
    }

    /**
     * @param array $data
     * @return mixed
     */
    public function insert(array $data) {
        $conversation = new Conversations();
        $conversation->markAsUnRead($data['conversation']);
        return parent::insert($data);
    }

}

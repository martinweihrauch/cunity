<?php

namespace Events\Models\Db\Table;

use Core\Models\Db\Abstractables\Table;
use Notifications\Models\Notifier;

/**
 * Class Guests
 * @package Events\Models\Db\Table
 */
class Guests extends Table {

    /**
     * @var string
     */
    protected $_name = 'events_guests';

    /**
     *
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * @param $status
     * @param $userid
     * @param $eventid
     * @return bool
     */
    public function changeStatus($status, $userid, $eventid) {
        if ($status < 0)
            return (0 < $this->delete([$this->getAdapter()->quoteInto("userid=?", $userid), $this->getAdapter()->quoteInto("eventid=?", $eventid)]));
        if ($this->getAdapter()->fetchOne("SELECT COUNT(1) FROM " . $this->_name . " WHERE userid= " . $userid . " AND eventid = " . $eventid))
            return (0 < $this->update(["status" => $status], [$this->getAdapter()->quoteInto("userid=?", $userid), $this->getAdapter()->quoteInto("eventid=?", $eventid)]));
        return (false !== $this->insert(["userid" => $userid, "eventid" => $eventid, "status" => $status]));
    }

    /**
     * @param $eventid
     * @param $users
     * @param bool $invitation
     * @return bool
     */
    public function addGuests($eventid, $users, $status = 0, $invitation = false) {
        if (is_array($users) && !empty($users)) {
            foreach ($users AS $user) {
                $this->insert(["userid" => intval($user), "eventid" => intval($eventid), "status" => $status]);
                Notifier::notify($user, $_SESSION['user']->userid, "eventInvitation", "index.php?m=events&action=" . $eventid);
            }
        } else {
            $this->insert(["userid" => intval($users), "eventid" => intval($eventid), "status" => $status]);
            if ($invitation)
                Notifier::notify($users, $_SESSION['user']->userid, "eventInvitation", "index.php?m=events&action=" . $eventid);
        }
        return true;
    }

    /**
     * @param $eventid
     * @param bool $sort
     * @param int $limit
     * @return array|bool
     */
    public function getGuests($eventid, $sort = true, $limit = 4) {
        $guests = [];
        $res = $this->getAdapter()->fetchAll(
                $this->getAdapter()->select()->from(["g" => $this->_name])
                        ->joinLeft(["u" => $this->_dbprefix . "users"], "g.userid=u.userid", ["username", "name"])
                        ->joinLeft(["i" => $this->_dbprefix . "gallery_images"], "i.id=u.profileImage", "filename")
                        ->where("g.eventid=?", $eventid));
        if ($res !== NULL) {
            if ($sort) {
                foreach ($res AS $guest) {
                    if ($guest['status'] == 0 && (($limit > 0 && count($guests['invited']) < $limit) || $limit == 0))
                        $guests['invited'][] = $guest;
                    else if ($guest['status'] == 1 && (($limit > 0 && count($guests['maybe']) < $limit) || $limit == 0))
                        $guests['maybe'][] = $guest;
                    else if ($guest['status'] == 2 && (($limit > 0 && count($guests['attending']) < $limit) || $limit == 0))
                        $guests['attending'][] = $guest;
                }
                return $guests;
            }
            return $res;
        }
        return false;
    }

}

<?php

namespace Likes\Models\Db\Table;

use Core\Models\Db\Abstractables\Table;

/**
 * Class Likes
 * @package Likes\Models\Db\Table
 */
class Likes extends Table {

    /**
     * @var string
     */
    protected $_name = 'likes';
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
     * @param $referenceId
     * @param $referenceName
     * @return null|\Zend_Db_Table_Row_Abstract
     */
    public function getLike($referenceId, $referenceName) {
        return $this->fetchRow($this->select()->from($this, ["id", "dislike"])->where("ref_id=?", $referenceId)->where("ref_name=?", $referenceName)->where("userid=?", $_SESSION['user']->userid));
    }

    /**
     * @param $referenceId
     * @param $referenceName
     * @return array
     */
    public function countLikes($referenceId, $referenceName) {
        $likes = $this->fetchRow($this->select()->from($this, new \Zend_Db_Expr("COUNT(*) AS c"))->where("ref_name=?", $referenceName)->where("ref_id=?", $referenceId)->where("dislike=0"));
        $dislikes = $this->fetchRow($this->select()->from($this, new \Zend_Db_Expr("COUNT(*) AS c"))->where("ref_name=?", $referenceName)->where("ref_id=?", $referenceId)->where("dislike=1"));
        return ["dislikes" => $dislikes['c'], "likes" => $likes['c']];
    }

    /**
     * @param $referenceId
     * @param $referenceName
     * @param int $dislike
     * @return array
     */
    public function getLikes($referenceId, $referenceName, $dislike = 0) {
        return $this->getAdapter()->fetchAll($this->getAdapter()->select()->from(["l" => $this->_dbprefix . "likes"])->joinLeft(["u" => $this->_dbprefix . "users"], "u.userid=l.userid", ["username", "name"])->joinLeft(["i" => $this->_dbprefix . "gallery_images"], "i.id=u.profileImage", "filename")->where("ref_name=?", $referenceName)->where("ref_id=?", $referenceId)->where("dislike=?", $dislike));
    }

    /**
     * @param $referenceId
     * @param $referenceName
     * @return array|bool
     */
    public function like($referenceId, $referenceName) {
        $res = $this->getLike($referenceId, $referenceName);
        if ($res != NULL && $res->dislike == 1) {
            $res->dislike = 0;
            if ($res->save())
                return $this->countLikes($referenceId, $referenceName);
        }else if ($this->insert(["ref_id" => $referenceId, "ref_name" => $referenceName, "userid" => $_SESSION['user']->userid]) !== NULL)
            return $this->countLikes($referenceId, $referenceName);
        return false;
    }

    /**
     * @param $referenceId
     * @param $referenceName
     * @return array|bool
     */
    public function dislike($referenceId, $referenceName) {
        $res = $this->getLike($referenceId, $referenceName);
        if ($res != NULL && $res->dislike == 0) {
            $res->dislike = 1;
            if ($res->save())
                return $this->countLikes($referenceId, $referenceName);
        }else if ($this->insert(["ref_id" => $referenceId, "ref_name" => $referenceName, "dislike" => 1, "userid" => $_SESSION['user']->userid]) !== NULL)
            return $this->countLikes($referenceId, $referenceName);
        return false;
    }

    /**
     * @param $referenceId
     * @param $referenceName
     * @return array|bool
     * @throws \Zend_Db_Table_Row_Exception
     */
    public function unlike($referenceId, $referenceName) {
        $res = $this->getLike($referenceId, $referenceName);
        if ($res->delete())
            return $this->countLikes($referenceId, $referenceName);
        return false;
    }

}

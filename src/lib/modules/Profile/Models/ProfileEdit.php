<?php

namespace Profile\Models;

use Core\Cunity;
use Core\Models\Generator\Url;
use Core\Models\Validation\Email;
use Core\Models\Validation\Username;
use Core\View\Ajax\View;
use Core\View\Message;
use Core\View\PageNotFound;
use Gallery\Models\Db\Table\Gallery_Images;
use Notifications\Models\Db\Table\Notification_Settings;
use Notifications\Models\Db\Table\Notifications;
use Profile\View\ProfileCrop;

/**
 * Class ProfileEdit
 * @package Profile\Models
 */
class ProfileEdit {

    /**
     * @var null
     */
    private $user = null;

    /**
     *
     */
    public function __construct() {
        $this->user = $_SESSION['user'];
        $this->handleRequest();
    }

    /**
     *
     */
    private function handleRequest() {
        if ($_GET['action'] == "cropImage")
            $this->cropImage();
        else if (isset($_POST['edit']) && !empty($_POST['edit'])) {
            if (method_exists($this, $_POST['edit']))
                call_user_func([$this, $_POST['edit']]);
        } else {
            $view = new \Profile\View\ProfileEdit();
            $user = $this->user->getTable()->get($_SESSION['user']->userid);
            $profile = $user->toArray(["userid", "username", "email", "firstname", "lastname", "registered", "sex", "pimg", "timg", "palbumid", "talbumid"]);
            $table = new Db\Table\Privacy();
            $privacy = $table->getPrivacy();
            $table = new Notification_Settings();
            $notificationSettings = $table->getSettings();
            $view->assign("profile", array_merge($profile, ["privacy" => $privacy, 'notificationSettings' => $notificationSettings]));
            $view->render();
        }
    }

    /**
     * @throws \Zend_Db_Table_Exception
     */
    private function loadPinData() {
        $pinid = $_POST['id'];
        $pins = new Db\Table\ProfilePins();
        $data = $pins->find($pinid)->current()->toArray();
        $data['content'] = htmlspecialchars_decode($data['content']);
        $view = new View($data !== NULL);
        $view->addData($data);
        $view->sendResponse();
    }

    /**
     *
     */
    private function deletePin() {
        if (isset($_POST['id'])) {
            $pins = new Db\Table\ProfilePins();
            $result = $pins->delete($pins->getAdapter()->quoteInto("id=?", $_POST['id']));
            $view = new View(($result > 0));
            $view->addData(["id" => $_POST['id']]);
            $view->sendResponse();
        }
    }

    /**
     *
     */
    private function pin() {
        if (isset($_POST['title']) && isset($_POST['type']) && isset($_POST['content'])) {
            $pins = new Db\Table\ProfilePins();
            if (isset($_POST['editPin'])) {
                $pins->update(["title" => $_POST['title'], "content" => $_POST['content'], "type" => $_POST['type'], "iconclass" => $_POST['iconClass']], $pins->getAdapter()->quoteInto("id=?", $_POST['editPin']));
                $res = $_POST['editPin'];
            } else {
                $res = $pins->insert(["userid" => $this->user->userid, "title" => $_POST['title'], "content" => $_POST['content'], "type" => $_POST['type'], "iconclass" => $_POST['iconClass']]);
            }
            $view = new View(true);
            $view->addData(["title" => $_POST['title'], "type" => $_POST['type'], "content" => htmlspecialchars_decode($_POST['content']), "iconclass" => $_POST['iconClass'], "id" => $res, "updated" => isset($_POST['editPin'])]);
            $view->sendResponse();
        }
    }

    /**
     *
     */
    private function notifications() {
        if (!empty($_POST['types'])) {
            $result = [];
            foreach ($_POST['types'] AS $key => $v) {
                if (isset($_POST['alert'][$key]) && isset($_POST['mail'][$key]))
                    $result[$key] = 3;
                else if (isset($_POST['alert'][$key]) && !isset($_POST['mail'][$key]))
                    $result[$key] = 1;
                else if (!isset($_POST['alert'][$key]) && isset($_POST['mail'][$key]))
                    $result[$key] = 2;
                else
                    $result[$key] = 0;
            }
            $settings = new Notification_Settings();
            $res = $settings->updateSettings($result);
            $view = new View($res);

            if ($res) {
                $message = $view->translate("Notification settings changed successfully!");
            } else {
                $message = $view->translate("Sorry, something went wrong, try again");
            }

            $view->addData(['msg' => $message]);
            $view->sendResponse();
        }
    }

    /**
     *
     */
    private function pinPositions() {
        $pins = new Db\Table\ProfilePins();
        if (isset($_POST['pins'])) {
            foreach ($_POST['pins'] AS $i => $pin) {
                $pins->updatePosition($_POST['column'], $i, $pin);
            }
        }
    }

    /**
     *
     */
    private function general() {
        if (isset($_POST['email']) || isset($_POST['username']) || $_POST['sex']) {
            $view = new View();
            $message = [];
            $validateMail = new Email();
            $validateUsername = new Username();

            if ($validateUsername->isValid($_POST['username']))
                $this->user->username = $_POST['username'];
            else
                $message[] = implode(",", $validateUsername->getMessages());
            if ($validateMail->isValid($_POST['email']))
                $this->user->email = $_POST['email'];
            else
                $message[] = implode(",", $validateMail->getMessages());
            $this->user->sex = $_POST['sex'];

            $res = $this->user->save();
            if (!$res)
                $message[] = $view->translate("Something went wrong! Please try again later!");
            $view->setStatus(empty($message));
            if (empty($message))
                $message[] = $view->translate("Your changes were saved successfully!");
            $view->addData(["msg" => implode(',', $message)]);
            $view->sendResponse();
        }
    }

    /**
     *
     */
    private function changePassword() {
        $status = false;
        $view = new View();
        if (sha1($_POST['old-password'] . $this->user->salt) === $this->user->password) {
            if ($_POST['new-password'] === $_POST['new-password-rep']) {
                $this->user->password = sha1($_POST['new-password'] . $this->user->salt);
                $this->user->save();
                $status = true;
                $message = $view->translate("Password changed successfully!");
            } else
                $message = $view->translate("The new passwords do not match!");
        } else
            $message = $view->translate("The current password is wrong");
        $view->setStatus($status);
        $view->addData(["msg" => $message]);
        $view->sendResponse();
    }

    /**
     *
     */
    private function changePrivacy() {
        if (isset($_POST['privacy']) && is_array($_POST['privacy'])) {
            $table = new Db\Table\Privacy();
            $res = $table->updatePrivacy($_SESSION['user']->userid, $_POST['privacy']);
            $view = new View();
            $view->setStatus($res);

            if ($res) {
                $message = $view->translate("Privacy settings changed successfully!");
            } else {
                $message = $view->translate("Sorry, something went wrong, try again");
            }

            $view->addData(['msg' => $message]);
            $view->sendResponse();
        }
    }

    /**
     *
     */
    private function changeimage() {
        $gimg = new Gallery_Images();
        $result = $gimg->uploadProfileImage();
        if ($result !== false) {
            $view = new View(true);
            $view->addData($result);
            $view->sendResponse();
        } else {
            new Message("Sorry!", "Something went wrong on our server!");
        }
    }

    /**
     *
     */
    function deleteImage() {
        if ($_POST['type'] == "profile") {
            $_SESSION['user']->profileImage = 0;
        } else {
            $_SESSION['user']->titleImage = 0;
        }
        if ($_SESSION['user']->save()) {
            $view = new View(true);
            $view->sendResponse();
        }
    }

    /**
     * @throws \Exception
     */
    private function cropImage() {
        if (!isset($_GET['x']) || empty($_GET['x'])) {
            new PageNotFound();
        }
        /** @noinspection PhpUnnecessaryFullyQualifiedNameInspection */
        $images = new \Gallery\Models\Db\Table\Gallery_Images();
        $result = $images->getImageData($_GET['x']);
        $view = new ProfileCrop();
        $user = $_SESSION['user']->getTable()->get($_SESSION['user']->userid); // Get a new user Object with all image-data
        $profileData = $user->toArray(["userid", "username", "name", "timg", "pimg", "talbumid", "palbumid"]);
        $view->assign(["profile" => $profileData, "result" => $result[0], "type" => $_GET['y'], "image" => getimagesize("../data/uploads/" . Cunity::get("settings")->getSetting("core.filesdir") . "/" . $result[0]['filename'])]);
        $view->show();
    }

    /**
     * @throws \Exception
     */
    private function crop() {
        $file = new \Skoch_Filter_File_Crop([
            "x" => $_POST['crop-x'],
            "y" => $_POST['crop-y'],
            "x1" => $_POST['crop-x1'],
            "y1" => $_POST['crop-y1'],
            "thumbwidth" => ($_POST['type'] == "title") ? 970 : 150,
            "directory" => "../data/uploads/" . Cunity::get("settings")->getSetting("core.filesdir"),
            "prefix" => "cr_"
        ]);
        $file->filter($_POST['crop-image']);
        if ($_POST['type'] == "title")
            $_SESSION['user']->titleImage = $_POST['imageid'];
        else
            $_SESSION['user']->profileImage = $_POST['imageid'];
        if ($_SESSION['user']->save())
            header("Location: " . Url::convertUrl("index.php?m=profile"));
    }

}
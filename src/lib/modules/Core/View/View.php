<?php

namespace Core\View;

use Core\Cunity;
use Core\Exception;
use Core\Models\Db\Table\Announcements;
use Core\Models\Db\Table\Menu;
use Core\Models\Generator\Url;
use Register\Models\Login;
use Zend_Log;
use Zend_Log_Writer_Stream;

require_once 'Smarty/Smarty.class.php';

/**
 * Class View
 * @package Core\View
 */
class View extends \Smarty
{

    /**
     * @var
     */
    public static $zt;
    /**
     * @var string
     */
    protected $_templateRoot = "modules/";
    /**
     * @var mixed|string
     */
    protected $_coreRoot = "../style/%design%/";
    /**
     * @var string
     */
    protected $_templateCache = "../data/temp/templates-cache/";
    /**
     * @var string
     */
    protected $_templateCompiled = "../data/temp/templates-compiled/";
    /**
     * @var bool
     */
    protected $_useWrapper = true;
    /**
     * @var string
     */
    protected $_wrapper = "Core/styles/out_wrap.tpl";
    /**
     * @var string
     */
    protected $_templateDir = "";
    /**
     * @var string
     */
    protected $_templateFile = "";
    /**
     * @var string
     */
    protected $_languageFolder = "Core/languages/";
    /**
     * @var array
     */
    protected $_headScripts = [];
    /**
     * @var array
     */
    protected $_headCss = [];
    /**
     * @var array
     */
    protected $_metadata = [
        "title" => "A page",
        "description" => "Cunity - Your private social network"
    ];

    /**
     * @throws \Exception
     * @throws \SmartyException
     */
    public function __construct()
    {
        parent::__construct();
        if (!file_exists("../style/" . $this->getSetting("core.design")))
            throw new \Exception(
                "Cannot find Theme-Folder \""
                . $this->getSetting("core.design")
                . "\""
            );
        $this->_coreRoot = str_replace(
            "%design%",
            $this->getSetting("core.design"),
            $this->_coreRoot
        );
        // $this->use_include_path = true;
        $this->setTemplateDir([$this->_coreRoot, $this->_templateRoot]);
        $this->setCompileDir($this->_templateCompiled);
        $this->setCacheDir($this->_templateCache);
        $this->left_delimiter = "{-";
        $this->debugging = Cunity::get("config")->site->tpl_debug;
        $this->registerPlugin("modifier", "translate", [$this, "translate"]);
        $this->registerPlugin("modifier", "setting", [$this, "getSetting"]);
        $this->registerPlugin("modifier", "config", [$this, "getConfig"]);
        $this->registerPlugin("modifier", "image", [$this, "convertImage"]);
        $this->registerPlugin("modifier", "URL", [$this, 'convertUrl']);
        $this->_templateDir = ucfirst($this->_templateDir);
        //$this->initTranslator();
    }

    /**
     * @param $settingname
     * @return mixed
     * @throws \Exception
     */
    public function getSetting($settingname)
    {
        return Cunity::get("settings")->getSetting($settingname);
    }

    /**
     * @param $urlString
     * @return string
     */
    public static function convertUrl($urlString)
    {
        return Url::convertUrl($urlString);
    }

    /**
     * @param $string
     * @param array $replaces
     * @return string
     */
    public static function translate($string, $replaces = [])
    {
        return vsprintf($string, $replaces);
    }

    /**
     * @param array $meta
     */
    public function setMetaData(array $meta)
    {
        $this->_metadata = $meta;
    }

    /**
     * @param $filename
     * @param $type
     * @param string $prefix
     * @return string
     */
    public function convertImage($filename, $type, $prefix = "")
    {
        if ($filename == NULL || empty($filename))
            return $this->getSetting("core.siteurl")
            . "style/"
            . $this->getSetting("core.design")
            . "/img/placeholders/noimg-"
            . $type . ".png";
        return $this->getSetting("core.siteurl")
        . "data/uploads/"
        . $this->getSetting("core.filesdir")
        . "/"
        . $prefix
        . $filename;
    }

    /**
     * @param $value
     */
    public function useWrapper($value)
    {
        $this->_useWrapper = ($value === true);
    }

    /**
     * @throws \Core\Exception
     * @throws \Exception
     */
    public function show()
    {
        if (Login::loggedIn() && $_GET['m'] !== "admin") {
            $this->registerScript("search", "livesearch");
            $this->registerScript("messages", "message-modal");
            $this->registerScript("notifications", "notifications");
            $this->registerScript("messages", "chat");
            $this->registerCss("messages", "chat");
            $this->registerCss("search", "livesearch");
            $this->registerCss("messages", "message-modal");
            $this->registerCss("notifications", "notifications");
            $this->registerScript("friends", "friends");
        }
        $announcements = new Announcements();
        $this->assign("announcements", $announcements->getAnnouncements());
        if ((Login::loggedIn())) {
            $this->assign(
                'user',
                $_SESSION['user']->getTable()->get($_SESSION['user']->userid)
            );
        } else {
            $this->assign('user', []);
        }
        $this->assign('menu', new Menu());
        $this->registerCunityPlugin(
            "jscrollpane",
            ["css/jquery.jscrollpane.css", "js/jquery.jscrollpane.min.js"]
        );
        $this->registerCunityPlugin(
            "bootstrap-validator",
            [
            "css/bootstrapValidator.min.css",
            "js/bootstrapValidator.min.js"
            ]
        );
        $this->_metadata["module"] = $_GET['m'];
        $this->assign("meta", $this->_metadata);
        $this->assign('script_head', implode("\n", $this->_headScripts));
        $this->assign('css_head', base64_encode(implode(",", $this->_headCss)));
        $this->assign('modrewrite', (boolean)Cunity::get("mod_rewrite"));
        if ($this->_useWrapper) {           
            $this->assign(
                'tpl_name',
                $this->_templateDir . '/styles/' . $this->_templateFile
            );
            $this->display($this->_wrapper);
        } else {
            $this->display(
                $this->_templateDir
                . DIRECTORY_SEPARATOR
                . 'styles'
                . DIRECTORY_SEPARATOR
                . $this->_templateFile
            );
        }
    }

    /**
     * @param $module
     * @param $scriptName
     * @throws \Core\Exception
     */
    protected function registerScript($module, $scriptName)
    {
        if ((!empty($module))) {
            $module = ucfirst($module) . "/styles/javascript/";
        } else {
            $module = "../plugins/javascript/";
        }
        if (file_exists(
            $this->_templateRoot . $module . $scriptName . ".min.js"
        ))
            $this->_headScripts[] = '<script src="'
                . $this->getSetting("core.siteurl")
                . "lib/"
                . $this->_templateRoot
                . $module
                . $scriptName
                . ".min.js"
                . '"></script>';
        elseif (file_exists(
            $this->_templateRoot . $module . $scriptName . ".js"
        ))
            $this->_headScripts[] = '<script src="'
                . $this->getSetting("core.siteurl")
                . "lib/"
                . $this->_templateRoot
                . $module
                . $scriptName
                . ".js"
                . '"></script>';
        else
            throw new Exception(
                "Cannot load javascript-file: '"
                . $this->_templateRoot
                . $module
                . $scriptName
                . ".js"
                . "'"
            );
    }

    /**
     * @param $module
     * @param $fileName
     * @throws \Core\Exception
     */
    protected function registerCss($module, $fileName)
    {
        if ((!empty($module))) {
            $module = ucfirst($module) . "/styles/css/";
        } else {
            $module = "../plugins/css/";
        }
        if (file_exists($this->_templateRoot . $module . $fileName . ".css"))
            $this->_headCss[] = $module . $fileName . ".css";
        else
            throw new Exception(
                "Cannot load CSS-file: '"
                . $this->_templateRoot
                . $module
                . $fileName
                . ".css"
                . "'"
            );
    }

    /**
     * @param $pluginName
     * @param array $files
     */
    protected function registerCunityPlugin($pluginName, array $files)
    {
        if (file_exists("plugins/" . $pluginName)) {
            if (!empty($files)) {
                foreach ($files AS $file) {
                    $finfo = pathinfo($file);
                    if ($finfo['extension'] == "js")
                        $this->_headScripts[] = '<script src="'
                            . $this->getSetting("core.siteurl")
                            . "lib/plugins/" . $pluginName
                            . "/"
                            . $file
                            . '"></script>';
                    else if ($finfo['extension'] == "css")
                        $this->_headCss[] = "../plugins/"
                            . $pluginName
                            . "/"
                            . $file;
                }
            }
        }
    }

    /**
     *
     */
    private function initTranslator()
    {
        $locale = new \Zend_Locale();
        self::$zt = new \Zend_Translate(
            [
            'adapter' => 'gettext',
            'locale' => 'auto',
            'content' => "Core/languages/",
            'scan' => \Zend_Translate::LOCALE_FILENAME
            ]
        );
        self::$zt->setOptions(
            [
            'log' => new Zend_Log(
                new Zend_Log_Writer_Stream('missing-translations.log')
            ),
            'logUntranslated' => true
            ]
        );
        if (!self::$zt->isAvailable($locale->getLanguage()))
            self::$zt->setLocale(self::$defaultLanguage);
        self::$zt->getLocale();
    }

}

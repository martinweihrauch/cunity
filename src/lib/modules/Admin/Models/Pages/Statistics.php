<?php

namespace Admin\Models\Pages;
use Core\Cunity;
use Core\Models\Db\Table\Modules;

/**
 * Class Statistics
 * @package Admin\Models\Pages
 */
class Statistics extends PageAbstract {

    /**
     *
     */
    public function __construct() {
        $this->loadData();
        $this->render("statistics");
    }

    /**
     * @throws \Exception
     */
    private function loadData() {
        $modules = new Modules();
        $installedModules = $modules->getModules()->toArray();
        $config = Cunity::get("config");
        $this->assignments['smtp_check'] = $config->mail->smtp_check;
        $this->assignments['modules'] = $installedModules;
    }

}

<?php

    /**
     * Manage languages in a database
     *
     * $Id: languages.php,v 1.13 2007/08/31 18:30:11 ioguix Exp $
     */

// Include application functions
    require_once '../lib.inc.php';

    $lang_controller = new \PHPPgAdmin\Controller\LangController($container);

    $lang_controller->render();

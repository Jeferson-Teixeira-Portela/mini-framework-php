<?php

require_once('../vendor/autoload.php');
require_once('../app/function/function.php');

use app\controller\TesteController;
(new \app\core\RouterCore());

$controller = new TesteController();

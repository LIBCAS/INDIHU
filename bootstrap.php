<?php

require_once __DIR__ . "/vendor/autoload.php";
require_once __DIR__ . "/app/Extension/Tracy/DbQueries.php";

define('BASE_DIR', __DIR__);
define('TEMP_DIR', ABSPATH . '../temp');
define('LOG_DIR', ABSPATH . '../log');
define('UPLOADS_DIR', ABSPATH . 'wp-content/uploads');
define('UPLOADS_URL', get_site_url() . '/wp-content/uploads');
define('ADMIN_TEMPLATE_DIR', BASE_DIR . '/app/templates/admin');
define('CLIENT_TEMPLATE_DIR', BASE_DIR . '/app/templates/client');

$configurator = new Nette\Configurator;

$configurator->enableDebugger(LOG_DIR);
$configurator->setTempDirectory(TEMP_DIR);
$configurator->setTimeZone('Europe/Prague');

$configurator->createRobotLoader()
    ->addDirectory(BASE_DIR . '/app')
    ->register();

if ($configurator->isDebugMode()) {
    $configurator->addConfig(BASE_DIR . '/app/config/config.neon');
    $configurator->addConfig(BASE_DIR . '/app/config/config.local.neon');
} else {
    $configurator->addConfig(BASE_DIR . '/app/config/config.neon');
}

global $container;
$container = $configurator->createContainer();

use Tracy\Debugger;
Debugger::enable(Debugger::DETECT, LOG_DIR);
Debugger::$showLocation = Tracy\Dumper::LOCATION_SOURCE;
Debugger::$showLocation = Tracy\Dumper::LOCATION_CLASS | Tracy\Dumper::LOCATION_LINK; 
Debugger::$showLocation = TRUE;
Debugger::$maxDepth = 10;
Debugger::$maxLength = 0;
Debugger::getBar()->addPanel(new DbQueries);
error_reporting(E_ALL & ~E_NOTICE);

use WP\Extension\Latte\Macro;
use WP\Extension\Latte\Filters\ActiveContent;
function render($template, $parameters){
    $latte = new Latte\Engine;
    $latte->setTempDirectory(TEMP_DIR . '/latte');
    $latte->addFilter('nl2br', 'nl2br');
    $latte->addFilter('wpContent', new ActiveContent);
    Macro::install($latte->getCompiler());
    $latte->render($template, $parameters);
}

function renderToString($template, $parameters){
    $latte = new Latte\Engine;
    $latte->setTempDirectory(TEMP_DIR . '/latte');
    Macro::install($latte->getCompiler());
    return $latte->renderToString($template, $parameters);
}
?>
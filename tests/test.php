<?php
require_once '../vendor/autoload.php';

require_once 'Demo.php';
$demo = new \carono\codegen\tests\Demo();
$content = $demo->render(['value' => 500]);
echo $demo->output;
file_put_contents($demo->output, $content);
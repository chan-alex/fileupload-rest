<?php

## Convinent script to work around "Routes With Dots" issue when using the php embedded web server.
## http://stackoverflow.com/questions/24336725/slim-framework-cannot-interpret-routes-with-dot

$_SERVER['SCRIPT_NAME'] = 'index.php';
include 'index.php';

?>

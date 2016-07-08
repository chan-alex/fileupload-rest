<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require '../vendor/autoload.php';
require './FileManager.php';

$app = new \Slim\App;

$container = $app->getContainer();

$container['logger'] = function($c) {
    $logger = new \Monolog\Logger('fileupload_logger');
    $file_handler = new \Monolog\Handler\StreamHandler("../../logs/app.log");
    $formatter = new  \Monolog\Formatter\LineFormatter(null, null, false, true);
    $file_handler->setFormatter($formatter);
    $logger->pushHandler($file_handler);
    return $logger;
};


$container['notFoundHandler'] = function ($c) {
    return function ($request, $response) use ($c) {
        return $c['response']
            ->withStatus(404);
            #->withHeader('Content-Type', 'text/html')
            #->write('Page not found lah');
    };
};



$app->post('/1/files/upload/{filename}', function (Request $request, Response $response,$args) {
    #$this->logger->addInfo("upload");  

    $filename = $args['filename'];
    $this->logger->Info("upload operation initiated for filename: $filename " );  

    $file_content = file_get_contents('php://input',NULL,NULL,0,2097152);  # limit to 2MB.
    $file_manager = new FileManager($this->logger);

    $status = $file_manager->save_file($filename, $file_content);
    if ($status['status'] != 'success') {
        $this->logger->Info("upload operation failed for filename: $filename " );  
        return $response->withJson($status,500); 
    }

    $this->logger->Info("upload operation completed for filename: $filename " );  
    return $response->withJson($status);
});



$app->get('/1/files/retrieve/{filename}', function (Request $request, Response $response,$args) {

    $filename = $args['filename'];
    $this->logger->Info("retrieve operation initiated for filename: $filename " );  

    $file_manager = new FileManager;
    
    $file_content = $file_manager->retrieve_file("$filename");
    if ($file_content === false) {
        $this->logger->Info("retrieve operation failed for filename: $filename " );  
        $data = array('status' => 'Error.File could not be found or opened for reading.' );
        return $response->withJson($data,500); 
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $newresponse = $response->withHeader('Content-Type', 'content-type: ' . $finfo->buffer($file_content));
    echo $file_content;

    $this->logger->Info("retrieve operation completed for filename: $filename " );  

    return $newresponse;
});



$app->delete('/1/files/{filename}', function (Request $request, Response $response,$args) {

    $filename = $args['filename'];
    $this->logger->Info("delete operation initiated for filename: $filename " );  

    $file_manager = new FileManager($this->logger);

    $status = $file_manager->delete_file($filename);
    if ($status['status'] != 'success') {
        $this->logger->Info("delete operation failed for filename: $filename " );  
        return $response->withJson($status,500); 
    }

   $this->logger->Info("delete operation competed for filename: $filename " );  
   return $response->withJson($status); 

});


$app->run();

?>

<?php


// The tests in this file tests the standard flow.

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use PHPUnit\Framework\TestCase;

class FileuploaderTest extends TestCase {

    public function test_upload_retrieve_delete() {
        // This test tests uploading, retrieve and then delete a file.

        // Simulates this command:
        //    curl -s localhost:8080/1/files/upload/something --data something
        // this is expected to be a successful upload.
        $client = new Client(); 

        $request = new Request('POST', 'http://localhost:8080/1/files/something', array() , "something");
        $response = $client->send($request, ['timeout' => 2]);

        $this->assertEquals(200, $response->getStatusCode());
        $data = json_decode($response->getBody(true), true);
        $this->assertEquals("success", $data['status']);
        $this->assertEquals("File uploaded successfully.", $data['info']);

        
        // Simulates this command:
        //    curl localhost:8080/1/files/retreieve/something 
        // this is expected to be a successful retrieve.
        $request = new Request('GET', 'http://localhost:8080/1/files/something');
        $response = $client->send($request, ['timeout' => 2]);

        $this->assertEquals(200, $response->getStatusCode());
        $body = $response->getBody();
        $this->assertEquals("something", $body->getContents());


        // Simulates this command:
        //    curl -X DELETE localhost:8080/1/files/something 
        // this is expected to be a successful delete.
        $request = new Request('DELETE', 'http://localhost:8080/1/files/something');
        $response = $client->send($request, ['timeout' => 2]);

        $this->assertEquals(200, $response->getStatusCode());
        $data = json_decode($response->getBody(true), true);
        $this->assertEquals("success", $data['status']);
        $this->assertEquals("file deleted.", $data['info']);
    }
    
}

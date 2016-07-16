<?php

// This file contains tests for testing some error conditions.

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use PHPUnit\Framework\TestCase;

class FileuploaderTest extends TestCase {

    public function test_unsuccessful_delete() {
        // this test is to simulate a unsuccessful delete - deleting a file that does not exist.

        // Simulates this command:
        //    curl -X DELETE localhost:8080/1/files/upload/something-else
        $client = new Client(); 
        $request = new Request('DELETE', 'http://localhost:8080/1/files/something-else');

        try {

            $response = $client->send($request, ['timeout' => 2]);
            $response = $client->send($request, ['timeout' => 2]);  # send twice just to be sure.
        
        } catch (GuzzleHttp\Exception\ServerException $e) {

            $response = $e->getResponse();
            $this->assertEquals(500, $response->getStatusCode() );
            $data = json_decode($response->getBody(true), true);
            $this->assertEquals("error", $data['status']);
            $this->assertEquals("file does not exist.", $data['info']);

        }

    }
    
    public function test_unsuccessful_retrieve() {
        // this test is to simulate a unsuccessful retrieve - retrieving  a file that does not exist.

        // Simulates this command:

        $client = new Client(); 
       
        # in case the file exists. delete it. 
        try {

            $request = new Request('DELETE', 'http://localhost:8080/1/files/something-else');
            $response = $client->send($request, ['timeout' => 2]);  

        } catch (GuzzleHttp\Exception\ServerException $e) {

            // no action needed.            

        }
      
        // Now do the GET.
        try { 
        
            $request = new Request('GET', 'http://localhost:8080/1/files/something-else');
            $response = $client->send($request, ['timeout' => 2]);
        
        } catch (GuzzleHttp\Exception\ServerException $e) {
            
            $response = $e->getResponse();
            $this->assertEquals(500, $response->getStatusCode() );
            $data = json_decode($response->getBody(true), true);
            $this->assertEquals("error", $data['status']);
            $this->assertEquals("File could not be found or opened for reading.", $data['info']);

        }

    }
}

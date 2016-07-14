<?php

// This file contains tests for testing some error conditions.

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use PHPUnit\Framework\TestCase;

class FileuploaderTest extends TestCase {

    private $testname;

    function setUp() {

        $this->testname = str_repeat("abcde",20);

    }

    function common_asserts ($response) {

        $this->assertEquals(500, $response->getStatusCode() );
        $data = json_decode($response->getBody(true), true);
        $this->assertEquals("error", $data['status']);
        $this->assertEquals("Invalid filename.", $data['info']);

    }

    public function test_very_long_name_upload() {
        // This test simulate uploading a file with a very long filename. Should fail.

        $client = new Client(); 

        try {

            $request = new Request('POST', "http://localhost:8080/1/files/upload/".$this->testname, array() , "something");
            $response = $client->send($request, ['timeout' => 2]);
        
        } catch (GuzzleHttp\Exception\ServerException $e) {

            $this->common_asserts($e->getResponse());
            return;

        } 

        $this->fail("This test should not reach here");
            
    }
    
    public function test_very_long_name_retrieve() {
        // This test simulates retrieve a file with a very long filename. Should fail.

        $client = new Client(); 

        try {

            $request = new Request('GET', "http://localhost:8080/1/files/retrieve/". $this->testname );
            $response = $client->send($request, ['timeout' => 2]);
        
        } catch (GuzzleHttp\Exception\ServerException $e) {

            $this->common_asserts($e->getResponse());
            return;

        }

        $this->fail("This test should not reach here");
            
    }
    
    public function test_very_long_name_delete() {
        // This test simulate deleting a file with a very long filename. Should fail.

        $client = new Client(); 

        try {

            $request = new Request('DELETE', "http://localhost:8080/1/files/". $this->testname );
            $response = $client->send($request, ['timeout' => 2]);
        
        } catch (GuzzleHttp\Exception\ServerException $e) {

            $this->common_asserts($e->getResponse());
            return;

        }

        $this->fail("This test should not reach here");
            
    }
}

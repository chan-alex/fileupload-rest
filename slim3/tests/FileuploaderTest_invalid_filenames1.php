<?php

// This file contains tests for testing some error conditions.

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use PHPUnit\Framework\TestCase;

class FileuploaderTest extends TestCase {

    private $testname;
    private $testnames;

    function setUp() {

        $this->testname = str_repeat("abcde",20);
        
        $this->testnames = array ( "somthi^gtxt", 
                                   "ab\$cd",
                                   "a;bcd",
                                   "abc}d",
                                   "a\?bd",
                                   "%2e`",
                                   "ab%Cd" );

    }

    function common_asserts ($response) {

        $data = json_decode($response->getBody(true), true);
        $this->assertEquals("error", $data['status']);
        $this->assertEquals("Invalid filename.", $data['info']);

    }

    public function test_invalid_filename_upload() {
        // This test simulate uploading a file with an invalid filename. Should fail.

        $client = new Client(); 

        for($x = 0; $x < count($this->testnames); $x++) {
            try {
                $request = new Request('POST', "http://localhost:8080/1/files/upload/".$this->testnames[$x], array() , "something");
                $response = $client->send($request, ['timeout' => 2]);
            
            } catch (GuzzleHttp\Exception\ServerException $e) {

                $response = $e->getResponse();
                $this->assertEquals(500, $response->getStatusCode() );
                $this->common_asserts($response);
                continue;
            }

            $this->fail("This test should not reach here. Current test filename: ". $this->testnames[$x] );
        }    
    }
    
    public function test_invalid_filename_retrieve() {
        // This test simulate retrieveing a file with an invalid filename. Should fail.

        $client = new Client(); 

        for($x = 0; $x < count($this->testnames); $x++) {
            try {
                $request = new Request('GET', "http://localhost:8080/1/files/retrieve/".$this->testnames[$x]);
                $response = $client->send($request, ['timeout' => 2]);
            
            } catch (GuzzleHttp\Exception\ServerException  $e) {

                $response = $e->getResponse();
                $this->assertEquals(500, $response->getStatusCode() );
                $this->common_asserts($response);
                continue;
            }

            $this->fail("This test should not reach here. Current test filename: ". $this->testnames[$x] );
        }    
    }
    
    public function test_invalid_filename_delete() {
        // This test simulate retrieveing a file with an invalid filename. Should fail.

        $client = new Client(); 

        for($x = 0; $x < count($this->testnames); $x++) {
            try {
                $request = new Request('DELETE', "http://localhost:8080/1/files/".$this->testnames[$x]);
                $response = $client->send($request, ['timeout' => 2]);
            
            } catch (GuzzleHttp\Exception\ServerException  $e) {

                $response = $e->getResponse();
                $this->assertEquals(500, $response->getStatusCode() );
                $this->common_asserts($response);
                continue;
            }

            $this->fail("This test should not reach here. Current test filename: ". $this->testnames[$x] );
        }    
    }
}

# Simple REST server for file upload

This is a simple PHP program that provides a HTTP REST API to store and retrieve files.
It uses the Slim 3 framework with Monolog for logging.




### REST API 
Note: The examples below assume the PHP program is start on the ip address 127.0.0.1 and listens on port 8080.

######  Uploading a file

Send a POST request to _http://ip-address-and-port/1/files/upload/{name of the file}_

    curl -X POST -T test.png  http://127.0.0.1:8080/1/files/upload/test.png


######  Retrieve a file

Do a GET from _http://ip-address-and-port/1/files/retrieve/{name of the file}_

    curl -X GET  http://127.0.0.1:8080/1/files/retrieve/test.png > test.png


######  DELETE a file

Send a DELETE request to _http://ip-address-and-port/1/files/{name of the file}_

    curl -X DELETE  http://127.0.0.1:8080/1/files/test.png



### Vagrant notes

You can use Vagrant for a quick way to run this code. Just copy over the "Vagrantfile" file in the "vagrant" directory
and then run "vagrant up".


### Notes on the file storage

This program implements a simple scheme of file deduplication. When a file (e.g. named test.png) is first saved , it's size and md5 checksum is 
calculated. Then the file is saved using the name "size-md5". A hard link with the name of the uploaded file (test.png) is 
created to point to this file ("size-md5"). If the user uploads another file (e.g. test2.png) with the same size and md5 checksum, a new
hardlink pointing the previous "size-md5" file is created. 

The uploaded files are saved under the "src/public/uploaded_files" directory.

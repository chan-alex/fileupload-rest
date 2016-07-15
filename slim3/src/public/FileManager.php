<?php

class FileManager {


     private $files_location = "./uploaded_files";
     private $backing_store= "./backing_store";
     private $logger;

     function __construct($logger) {

         $this->logger = $logger;           

     }

     function save_file($filename, $file_content) {

         $file_size = strlen($file_content);
         $file_md5  = md5($file_content);

         $path = "$this->backing_store/$file_size-$file_md5";

         if (file_exists($path) === false) {

         if (file_exists("$this->files_location/$filename")) {
              $this->delete_file($filename);
         }

         $fh = fopen($path,'w');
         if ($fh === false) {
             return array('status' => 'error', 'info'=>'Not able to write to file.' );
         }

         fwrite($fh,$file_content);
         fflush($fh);
         fclose($fh);

         }
         link($path, "$this->files_location/$filename");
         $this->logger->Info("$this->files_location/$filename was saved." );
         return array('status' => 'success', 'info'=>'File uploaded successfully.' );
     }


     function retrieve_file($filename) {

         $path = "$this->files_location/$filename";
         return file_get_contents($path);

     }

     function delete_file($filename) {

         $link_path = "$this->files_location/$filename";

         if (file_exists($link_path) === false) {
             return array('status' => 'error', 'info'=>'file does not exist.' );
         }
     
         $link_stat = stat($link_path);
         $filesize = $link_stat['size'];
         $md5 = md5(file_get_contents($link_path));

         // delete the hard link
         unlink($link_path);

         $content_path = "$this->backing_store/$filesize-$md5";
         $content_stat = stat($content_path);
         $nlink = $content_stat['nlink'];

         // remove the last file, if there are no more hardlinks pointing to it.
         if ($nlink == 1) {
             unlink($content_path);
         }   

         return array('status' => 'success', 'info'=>'file deleted.');
     }

}



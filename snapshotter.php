<?php
/**
 * Thanks to
 * https://stackoverflow.com/a/42605439
 */
global $file_info;
global $blackListedFolders;
global $blackListedFiles;
$file_info = new stdClass();
//=== ADD Black lists like vendor ====
//add all folder names in lowercase - to make it case insensitive we check with lowercase names only
// add assets folder if any
$blackListedFolders = ['.','..','vendor','node-modules'];
//add all files in lowercase
$blackListedFiles = ['readme.md','.gitignore'];

global $folderCount;
$folderCount = 0;
global $fileCount;
$fileCount =0;
global $skippedfileCount;
$skippedfileCount = 0;
global $skippedfolderCount;
$skippedfolderCount = 0;
/**
 * 
 * @function recursive_scan
 * @description Recursively scans a folder and its child folders
 * @param $path :: Path of the folder/file
 * 
 * */
function recursive_scan($path){
    global $file_info;
    global $blackListedFolders;
    global $blackListedFiles;
    global $folderCount;
    global $fileCount;
    global $skippedfileCount;
    global $skippedfolderCount;
    $path = rtrim($path, '/');
    if(!is_dir($path)){ 
        $fileNameArr = explode("/",$path);
        $fileName = $fileNameArr[count($fileNameArr)-1];
        if(!in_array(strtolower($fileName), $blackListedFiles) && $path != './' .basename(__FILE__))  {    
            $key = rtrim(strtr(base64_encode($path), '+/', '-_'), '=');
            $value = new stdClass();
            $value->path = $path;
            $value->hash = hash_file('sha1', $path);
            $value->modTime = date ("F d Y H:i:s.", filemtime($path));
            
            $file_info->$key = $value;
            $fileCount += 1;
        } else {
            // add skipped count
            $skippedfileCount += 1;
        }
    } else {
            $files = scandir($path);
            $folderCount += 1;
            foreach($files as $file){  
             if(!in_array(strtolower($file), $blackListedFolders))  {
                recursive_scan($path . '/' . $file);
             } else {
                if(!is_dir($path . '/' . $file)){
                    $skippedfolderCount += 1;
                }
             }
            }
        }
}

function colorLog($str){
    echo "\033[36m[i] $str \033[0m\n";
}

// Actual function starts here
recursive_scan('./');
$t=time();
$savedAs = 'snapshot_'.$t.'.json';
$fp = fopen($savedAs, 'w');
fwrite($fp, json_encode($file_info));
fclose($fp);
colorLog('Scanned '.$fileCount . ' files in ' . $folderCount . ' folders');
colorLog('Skipped '.$skippedfileCount . ' files');
colorLog('Skipped '.$skippedfolderCount . ' folders');
colorLog('Snapshot saved to '.$savedAs);
?>
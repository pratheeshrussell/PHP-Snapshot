<?php
// Assumption snapshot2 is the latest
$snapshotFile1 = './snapshot_1650013148.json';
$snapshotFile2 = './snapshot_1650013314.json';
$saveTofile = true;//save result to file

$snapshotObj1 = json_decode(file_get_contents($snapshotFile1),true);
$snapshotObj2 = json_decode(file_get_contents($snapshotFile2),true);
$changes = 0;
$comparerResult = new stdClass();


// Support function to print in color
// Thanks to https://stackoverflow.com/a/66075475
function colorLog($str, $type = 'info'){
    switch ($type) {
        case 'deleted':
            echo "\033[31m$str \033[0m\n";
        break;
        case 'created': 
            echo "\033[32m$str \033[0m\n";
        break;
        case 'modified': 
            echo "\033[33m$str \033[0m\n";
        break;  
        case 'info': //info
            echo "\033[36m$str \033[0m\n";
        break;      
        default:
        # code...
        break;
    }
}


//modified or newly created change
foreach($snapshotObj2 as $key => $val) {
    
    $valObj = ($val);
    if (array_key_exists($key,$snapshotObj1)){
        $snapshotObjVal1 = $snapshotObj1[$key];
        if($snapshotObjVal1['hash'] != $valObj['hash']){
            $valObj['change'] = 'modified';
            $comparerResult->$key = $valObj;
            colorLog( "[~] " . $valObj['path'] . " Modified " . PHP_EOL . "    At " . $valObj['modTime'] . PHP_EOL , 'modified');
            $changes += 1;
        } 
    }else {
        $valObj['change'] = 'created';
        $comparerResult->$key = $valObj;
        colorLog( "[+] " . $valObj['path'] . " Created " . PHP_EOL . "    At " . $valObj['modTime'] . PHP_EOL, 'created');
        $changes += 1;
    }
}

// deleted change
foreach($snapshotObj1 as $key => $val) {
    $valObj = $val;
    if (!array_key_exists($key,$snapshotObj2)){
        $valObj['change'] = 'deleted';
        $comparerResult->$key = $valObj;
        colorLog( "[-] " . $valObj['path'] . ' Deleted ' . PHP_EOL, 'deleted');
        $changes += 1;
    }
}

if($changes > 0){
    if($saveTofile){
        $t=time();
        $savedAs = 'comparedResult_'.$t.'.json';
        $fp = fopen($savedAs, 'w');
        fwrite($fp, json_encode($comparerResult));
        fclose($fp);
        colorLog( '[i] ' . $changes . ' Changes detected' , 'info');
        colorLog( '[i] Result saved to '.$savedAs , 'info');
    }
} else {
    colorLog( "[i] No changes detected", 'info');
}

?>
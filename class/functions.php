<?php

function base64_to_jpeg($base64_string, $output_file) {
    // open the output file for writing
    $ifp = fopen( $output_file, 'wb' ); 

    // split the string on commas
    // $data[ 0 ] == "data:image/png;base64"
    // $data[ 1 ] == <actual base64 string>
    $data = explode( ',', $base64_string );

    // we could add validation here with ensuring count( $data ) > 1
    fwrite( $ifp, base64_decode( $data[ 1 ] ) );

    // clean up the file resource
    fclose( $ifp ); 

    return $output_file; 
}

function encodeName($str) {
    return str_replace("/","@",base64_encode($str));
}
function decodeName($str){
    return base64_encode(str_replace("@","/",$str));
}

function append($xmlNode, $tag, $data){
    $node = $xmlNode->addChild($tag);
    foreach($data as $x=>$x_value){
        $node[$x]=$x_value;
    }
    return $node;
}

function write2file($path, $str){
    $file = fopen($path, "w");
    fwrite($file, $str);
    fclose($file);
}

function isFolderExist($folder)
{
    // Get canonicalized absolute pathname
    $path = realpath($folder);
    // If it exist, check if it's a directory
    return ($path !== false AND is_dir($path)) ? $path : false;
}
function createFolder($folder){
    if(!isFolderExist($folder)){
        mkdir($folder, 0777, true);
    }
}
function isFileExist($file){
    return realpath($file)!==false;
}
?>
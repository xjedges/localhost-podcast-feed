<?php
header("Content-Type: application/xml; charset=utf-8"); 

include "class/mp3file.class.php";
include "class/functions.php";

$title = $_POST["title"];
$author = $_POST["author"];
$colorText = $_POST["colorText"];
$colorBG = $_POST["colorBG"];

$folder = "feeds/".$title."/";
$domain = $_POST["domain"]."/";
$coverData = $_POST["cover"];


write2file("rss/".$title.".json", json_encode(array(
    "title"=>$title,
    "titleBase64"=>encodeName($title),
    "author"=>$author,
    "colorText"=>$colorText,
    "colorBG"=>$colorBG,
)));

$path = $domain.$folder;

$coverPath = "rss/".$title.".png";
base64_to_jpeg($coverData, $coverPath);
$coverURL = $domain.$coverPath;


if(isFolderExist($folder)){
    $namespace = "http://www.itunes.com/dtds/podcast-1.0.dtd";
    $xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><rss xmlns:itunes="'.$namespace.'"/>');
    $channel = $xml->addChild('channel');
    $channel->addChild("title",$title);
    $channel->addChild("author",$author,$namespace);
    $image = $channel->addChild("image","",$namespace);
    $image->addAttribute("href", $coverURL);

    
    $scanned_directory = scandir($folder);
    foreach ($scanned_directory as $file) {
        if($file=="." or $file==".." or $file==".DS_Store")continue;
        $type = explode(".", $file);
        $fileFormat = $type[1];
        $fileName = $type[0];
        if($fileFormat=="mp3"|| $fileFormat=="MP3"){

            $pathFolderOutput = "rss/".encodeName($title);
            $fileOutput = encodeName($fileName).".".$fileFormat;
            $pathFileOutput = $pathFolderOutput."/".$fileOutput;
            createFolder($pathFolderOutput);
            if(!isFileExist($pathFileOutput))
                copy($folder.$file, $pathFileOutput);
            //-------------------------
            $item = $channel->addChild('item');
            $item->addChild("title", $type[0]);
            $enclosure = $item->addChild('enclosure');
            $enclosure->addAttribute("url", $domain.$pathFileOutput);
            $mp3file = new MP3File($pathFileOutput);
            $item->addChild("duration", $mp3file->getDurationEstimate(), $namespace);
            $item->addChild("pubDate", "Wed, 24 Jun 2015 16:11:37 GMT");
        }
    }
    
    $str = $xml->asXML();
    print($str);
    write2file("rss/".$title.".xml", $str);

}


?>
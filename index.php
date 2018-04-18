<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Podcast</title>
    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    <script src="js/html2canvas.min.js"></script>
    <script src="js/functions.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/vue/2.3.4/vue.min.js"></script>
    <script src="https://unpkg.com/vue-color/dist/vue-color.min.js"></script>
    <script src="js/main.js"></script>
    <link rel="stylesheet" href="css/style.css"></link>
    <script>document.write('<script src="http://' + (location.host || 'localhost').split(':')[0] + ':35729/livereload.js?snipver=1"></' + 'script>')</script>
</head>
<body>
<script>
var list = [<?php
    include "class/functions.php";
    $scanned_directory = scandir('./feeds/');
    foreach ($scanned_directory as $dir) {
        if($dir=="." or $dir==".." or $dir==".DS_Store")continue;
        $configPath = "rss/".$dir.".json";
        $config;
        if(isFileExist($configPath)){
            $string = file_get_contents($configPath);
            $config = json_decode($string, true);
        }else{
            $config = array(
                "title"=>$dir,
                "author"=>"",
                "colorText"=>"#000",
                "colorBG"=>"#4ee58a",
            );
        }
        echo "{";
        foreach($config as $x=>$x_value)
            echo $x.":'".$x_value."',";
        echo "url:'',";
        echo "isCapturing:false,";
        echo "isCaptured:false,";
        echo "},";
    }
    ?>
];
</script>
<div id="app">
<table id="list">
    <tr class="feed" v-for="item,index in list">
        <td class="coverHtml">
            <div :class="item.isCapturing?'capture':''" :style="{background:item.colorBG}">
                <span :style="{color:item.colorText}">
                    {{item.title}}
                </span>
            </div>
        </td>
        <td>
            <table>
                <tr>
                    <td>title</td>
                    <td class="feedName">{{item.title}}</td>
                </tr>
                <tr>
                    <td>author</td>
                    <td><input v-model="item.author"/></td>
                </tr>
                <tr>
                    <td>colorText</td>
                    <td><colorpicker :color="item.colorText" v-model="item.colorText" /></td>
                </tr>
                <tr>
                    <td>colorBG</td>
                    <td><colorpicker :color="item.colorBG" v-model="item.colorBG" /></td>
                </tr>
                <tr>
                    <td><button @click="coverCapture(index)">capture</button></td>
                    <td class="coverCanvas"></td>
                </tr>
                <tr>
                    <td><button @click="update(index)">update</button></td>
                    <td><input :value='item.url'></input></td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</div>
</body>
</html>
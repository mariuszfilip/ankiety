<?php

class My_Util
{

    public static function sanitize_file_name($filename) {
        return preg_replace(array('/\s/', '/\.[\.]+/', '/[^\w_\.\-]/'), array('_', '.', ''), $filename);
    }

}
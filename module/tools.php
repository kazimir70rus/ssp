<?php

namespace ssp\module;

Class Tools
{
    static function send_json($json_data) {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: POST, GET, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: X-Requested-With, content-type');
        header('Content-Type: application/json; charset=utf8');

        echo json_encode($json_data);
    }


    static function save_CSV(&$content, $filename)
    {
        header('Content-type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . $filename);
        header('Content-Length: ' . strlen($content));
        echo $content;
    }


    static function save_XML(&$content, $filename)
    {
        header('Content-type: text/xml');
        header('Content-Disposition: attachment; filename=' . $filename);
        header('Content-Length: ' . strlen($content));
        echo $content;
    }


    static function checkSession($lifetime)
    {
        if (isset($_SESSION['time'])) {
            if ((time() - $_SESSION['time']) > $lifetime) {
                return false;
            }
        }

        $_SESSION['time'] = time();

        return true;
    }


    static function filter($str)
    {
        $result = preg_match("/([a-zA-Z0-9]+)/", $str, $match);
        if ($result) {
            return $match[0];
        } else {
            return '';
        }
    }
}

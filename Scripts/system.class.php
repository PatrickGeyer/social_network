<?php

include_once('Includes_PHP/simple_html_dom.php');
include('database.class.php');
include('files.class.php');

class System extends Database {

    private $url;
    protected $files;
    private $meta_tag = array(
        "charset" => "utf-8",
        "description" => "This is a description of my site.",
        "keywords" => "social, connect, school, business, collaborate, file, share,",
        "copyright" => "Copyright 2013 --",
        "revisit-after" => "2 days",
        "web_author" => "Patrick Geyer",
    );

    public function __construct() {
        parent::__construct();
        $this->files = new Files;
    }

    function getSchoolNames() {
        $query = "SELECT name FROM community;";
    }

    function getSchoolIds() {
        
    }

    const CYPHER = MCRYPT_RIJNDAEL_256;
    const MODE = MCRYPT_MODE_CBC;
    const KEY = 'somesecretphrase';

    public function encrypt($plaintext) {
        $td = mcrypt_module_open(self::CYPHER, '', self::MODE, '');
        $iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
        mcrypt_generic_init($td, self::KEY, $iv);
        $crypttext = mcrypt_generic($td, $plaintext);
        mcrypt_generic_deinit($td);
        return base64_encode($iv . $crypttext);
    }

    public function decrypt($crypttext) {
        $crypttext = base64_decode($crypttext);
        $plaintext = '';
        $td = mcrypt_module_open(self::CYPHER, '', self::MODE, '');
        $ivsize = mcrypt_enc_get_iv_size($td);
        $iv = substr($crypttext, 0, $ivsize);
        $crypttext = substr($crypttext, $ivsize);
        if ($iv) {
            mcrypt_generic_init($td, self::KEY, $iv);
            $plaintext = mdecrypt_generic($td, $crypttext);
        }
        return trim($plaintext);
    }

    public function getGlobalMeta() {
        foreach ($this->meta_tag as $meta => $value) {
            echo "<meta name='" . $meta . "' content='" . $value . "' />";
        }
    }

    public function audioPlayer($path = null, $name = null, $close_button = true, $rndm = null) {
        if ($rndm == "blank") {
            $rndm = ":::uid:::";
        }
        else if ($rndm == NULL) {
            $rndm = rand();
        }

        if ($path == null) {
            $path = ":::path:::";
            $name = ":::name:::";
        }

        $string = '<div class="audio_container" id="audio_container_' . $rndm
                . '">' . ($close_button == true ? "<div onclick=\"removeAudio(" . $rndm . ");\" class=\'audio_remove\'>x</div>" : "") . '<audio id="audio_' . $rndm .
                '" style="display:none;" controls="controls" class="player" preload="none"> <source src="' . $path . '" />Your browser doesnt support the audio element, please download to listen instead...</audio>';

        $string .= '<div id="image_' . $rndm . '" onclick="audioPlay(&quot;' . $rndm
                . '&quot;);" class="audio_button"></div>';

        $string .= '<div id="audio_info_' . $rndm . '" class="audio_info"><div class="ellipsis_overflow audio_title">' . $name . '</div><div id="audio_progress_container_' . $rndm
                . '"class="audio_progress_container"><div id="audio_progress_' . $rndm . '" class="audio_progress"></div><div class="audio_buffered" id="audio_buffered_' . $rndm .
                '"></div></div><div class="audio_time" id="audio_time_' . $rndm . '">0:00</div></div></div>';
        return $string;
    }
    
    public function videoPlayer($video_id = NULL, $path = NULL, $classes = NULL, $styles = NULL) {
        $flv_path;
        $mp4_path;
        $ogg_path;
        $original_path;
        $thumbnail;
        
        if($video_id == NULL) {
            $video_id = ":::vid:::";
        }
        if($path == NULL) {
            $flv_path = ":::flv_path:::";
            $mp4_path = ":::mp4_path:::";
            $ogg_path = ":::ogg_path:::";
            $original_path = ":::ori_path:::";
            $path = ":::path:::";
            $thumbnail = ":::thumb:::";
        } else {
            $path = $this->files->stripexts($path);
            $flv_path = $path . ".flv";
            $mp4_path = $path . ".mp4";
            $ogg_path = $path . ".ogg";
            $original_path = $path.".avi";
            $thumbnail = $path . ".jpg";
        }
        return  "<video id='file_video_" . $video_id . "' class='".$classes." video-js vjs-default-skin'"
                . "preload='auto' controls poster='" . $thumbnail . "'"
                . "style='" . $styles . "' "
                . "data-setup='{}'>"
                . "<source src='" . $flv_path . "' type='video/x-flv'>" //should be mp4
                . "<source src='". $ogg_path ."' type='video/ogg'> "
                . "<source src='" . $mp4_path . "' type='video/mp4'>"
                . "<object data='" . $original_path . "' width='320' height='240'>"
                . "<embed src='" . $flv_path . "' width='320' height='240'>"
                . "</object>"
                . "</video>";
    }

    function trimStr($string, $length) {
        $string = (strlen($string) > $length) ? substr($string, 0, $length) . '...' : $string;
        return $string;
    }

    function humanTiming($time) {
        $time = time() - $time;
        $return;
        $tokens = array(
            31536000 => 'year',
            2592000 => 'month',
            604800 => 'week',
            86400 => 'day',
            3600 => 'hour',
            60 => 'minute',
            1 => 'second'
        );

        foreach ($tokens as $unit => $text) {
            if ($time < $unit) {
                continue;
            }
            $numberOfUnits = floor($time / $unit);
            if ($numberOfUnits < 20 && $text == "second") {
                $return = "Just Now";
            }
            else {
                $return = $numberOfUnits . ' ' . $text . (($numberOfUnits > 1) ? 's' : '') . " ago";
            }
            if ($return != "") {
                return $return;
            }
            else {
                return "Just Now";
            }
        }
    }

    public function getPagePreview($url) {
        $this->url = $url;
        $parse = parse_url($this->url);
        $scheme = $parse['scheme'];
        if ($scheme == "") {
            $this->url = "http://" . $this->url;
        }
        $html = new simple_html_dom();
        $html->load($this->get_web_html($this->url), true, false);

        $return_info = array();
        $return_info['favicon'] = $this->getFavicon($html);
        $return_info['title'] = $this->trimStr($this->getTitle($html), 80);
        $return_info['description'] = $this->trimStr($this->getDescription($html), 100);
        return $return_info;
    }

    private function getTitle($html) {
        $title = 'No title';
        foreach ($html->find('title') as $element) {
            $title = $element->innertext;
        }
        return $title;
    }

    private function getFavicon($html) {
        $icon = 'NA';
        foreach ($html->find('link') as $element) {
            if ($element->rel == "shortcut icon" || $element->rel == "icon") {
                $icon = $element->href;
            }
        }
        if ($icon == "NA") {
            foreach ($html->find('meta[itemprop="image"]') as $element) {
                $icon = $element->content;
            }
        }
        $parse_path = parse_url($icon);
        $parse_url = parse_url($this->url);
        if ($parse_path['scheme'] == NULL && substr($icon, 0, 2) != "//") {
            $icon = $parse_url['scheme'] . "://" . $parse_url['host'] . $icon;
        }
        return $icon;
    }

    private function getDescription($html = NULL) {
        $description = 'No Description available';
        
        $tag = $html->find("meta[name='description']", 0)->content;
        if ($tag == "") {
            $tag = $html->find("meta[name='og:description']", 0)->content;
            if ($tag == "") {
                $tag = $html->find("meta[property='description']", 0)->content;
                if ($tag == "") {
                    $tag = $html->find("meta[property='og:description']", 0)->content;
                    if ($tag == "") {
                        $tag = $html->find("h1", 0)->plaintext;
                        if($tag == "") {
                            $tag = $html->find("h2", 0)->plaintext;
                            if($tag == "") {
                                $tag = $html->find("h3", 0)->plaintext;
                                if($tag == "") {
                                    $tag = $html->find("p", 0)->plaintext;
                                    if($tag == "") {
                                        $tag = $html->find("div", 0)->plaintext;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        if ($tag != "") {
            $description = $tag;
        }
        return $description;
    }

    function get_web_html($url) {
        $options = array(
            CURLOPT_RETURNTRANSFER => true, // return web page
            CURLOPT_HEADER => true, // don't return headers
            CURLOPT_FOLLOWLOCATION => true, // follow redirects
            CURLOPT_ENCODING => "", // handle compressed
            CURLOPT_USERAGENT => "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.4 (KHTML, like Gecko) Chrome/22.0.1229.95 Safari/537.4",
            CURLOPT_AUTOREFERER => true, // set referer on redirect
            CURLOPT_CONNECTTIMEOUT => 120,
            CURLOPT_TIMEOUT => 120,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_CAINFO => "../Global_Tools/cacert.pem",
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => FALSE
        );

        $ch = curl_init($url);
        curl_setopt_array($ch, $options);
        $content = curl_exec($ch);
        $err = curl_errno($ch);
        $errmsg = curl_error($ch);
        $header = curl_getinfo($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        $header ['errno'] = $err;
        $header ['errmsg'] = $errmsg;
        $header ['content'] = $content;
        if ($header['http_code'] == 301 || $header['http_code'] == 302) {
            $this->url = $header['url'];
            return $this->get_web_html($header['url']);
        }
        else {
            return $header ['content'];
        }
    }

}

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $system = new System;
    if (isset($_POST['action'])) {
        if ($_POST['action'] == "get_page_preview") {
            die(json_encode($system->getPagePreview($_POST['url'])));
        }
    }
}
?>
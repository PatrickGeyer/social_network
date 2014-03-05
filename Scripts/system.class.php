<?php

include_once('php_includes/simple_html_dom.php');
include_once('database.class.php');
include_once('base.class.php');

class System extends Base {

    private static $system = NULL;
    private $url;
    private $meta_tag = array(
        "charset" => "utf-8",
        "description" => "This is a description of my site.",
        "keywords" => "social, connect, school, business, collaborate, file, share,",
        "copyright" => "Copyright 2013 --",
        "revisit-after" => "2 days",
        "web_author" => "Patrick Geyer",
    );

    public function __construct() {
        
    }

    public static function getInstance() {
        if (self :: $system) {
            return self :: $system;
        }

        self :: $system = new System();
        return self :: $system;
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
        if (!empty($plaintext)) {
            $td = mcrypt_module_open(self::CYPHER, '', self::MODE, '');
            $iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
            mcrypt_generic_init($td, self::KEY, $iv);
            $crypttext = mcrypt_generic($td, $plaintext);
            mcrypt_generic_deinit($td);
            return base64_encode($iv . $crypttext);
        }
    }

    public function decrypt($crypttext) {
        if (!empty($crypttext)) {
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
    }

    function getRandomString($length = 8) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $string = '';

        for ($i = 0; $i < $length; $i++) {
            $string .= $characters[mt_rand(0, strlen($characters) - 1)];
        }

        return $string;
    }

    function linkReplace($string, $classes = 'post_feed_link', $before = '', $after = '') {
        if ($classes == NULL) {
            $classes = 'post_feed_link';
        }
        $string = preg_replace("/(?<!a href=\")(?<!src=\")((http|ftp)+(s)?:\/\/[^<>\s]+)/i", "<a href=\"\\0\" target=\"blank\" class=\"" . $classes . "\">" . $before . "\\0" . $after . "</a>", $string);
//        $string = str_replace($string, ':::BEFORE:::', $before);
//        $string = str_replace($string, ':::AFTER:::', $after);
//        $string = str_replace($string, ':::CLASSES:::', $classes);
        return $string;
    }

    public function getGlobalMeta() {
        foreach ($this->meta_tag as $meta => $value) {
            echo "<meta name='" . $meta . "' content='" . $value . "' />";
        }
        echo "<link rel='stylesheet' href='CSS/style.css' type='text/css'>"; //change to minified version after development
        echo "<link rel='shortcut icon' href='" . self::SHORTCUT_ICON . "'>";
        echo "<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>";
    }

    public function jsVars() {
        echo "<script id='js_vars'>";
        $refl = new ReflectionClass('Base');
        $constant = $refl->getConstants();
        foreach ($constant as $var_name => $value) {
            if($var_name != "COPYRIGHT_ZIP") {
                echo "var " . $var_name . "='" . $value . "';";
            }
        }
        echo "var SCROLL_OPTIONS = {
                scrollButtons:  
                {
                    enable:false
                },
                advanced:
                {
                     updateOnContentResize: true,
                     updateOnBrowserResize:true,
                },
                scrollInertia:200,
                theme:'dark',
                autoHideScrollbar: true,
                mouseWheelPixels: 100 
            };";
        echo "USER_ID=".base64_decode($_COOKIE['id']).";</script>";
    }
    /**
     * AudioPlayer function
     * Prints an HTML5 audio Tag and controls.
     * 1. Path
     * 2. Name
     * 3. ID
     * 4. Control Part (default:"all")
     */
    public function audioPlayer($path = null, $name = null, $rndm = null, $part = 'all', $uniqid = NULL) {
        $string = '';

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

        $string .= '<div data-path="'.$path.'" uid="'.$uniqid.'">';

        if($part == 'all') {
            $string .= '<div class="audio_container" id="audio_container_' . $rndm . '">';

            $string .= $this->audioButton($rndm, $path);
            $string .= $this->audioInfo($rndm, $name, $path);

            $string .= '</div>';
        } else if( $part == "button" ) {
            $string .= $this->audioButton($rndm, $path);
        } else if( $part == "info" ) {
            $string .= $this->audioInfo($rndm, $path);
        } else if( $part == 'timeline' ) {
            $string .= $this->audioTimeline($rndm);
        }
        $string .= "</div>";
        return $string;
    }

    private function audioButton($id, $path) {
        return '<div id="image_' . $id . '" class="audio_button" audio_id="'
            .$id.'" style="background-image:url(' 
                . Base::AUDIO_PLAY_THUMB . ')"><div class="audio_loader"><div class="loader_outside"></div><div class="loader_inside"></div><span class="audio_loader_text loader_text"></span></div></div>';
    }

    private function audioInfo($id, $name, $path) {
        return '<div id="audio_info_' . $id . '" class="audio_info"><div class="ellipsis_overflow audio_title">' 
        . $name . '</div>'.$this->audioTimeline($id).'<div class="audio_time" id="audio_time_' . $id . '">0:00</div></div>';
    }

    private function audioTimeline($id) {
        return '<div id="audio_progress_container_' . $id
                . '"class="audio_progress_container"></div>';
    }

    /**
     * VideoPlayer function
     * Prints an HTML5 video Tag.
     * 1. Video ID
     * 2. Video Path
     * 3. Video Classes
     * 4. Video Styles
     * 5. Video ID Prefix (default:"file_video_")
     * 6. Print Source Tags (bool)
     */
    public function videoPlayer($video_id = NULL, $path = NULL, $classes = NULL, $styles = NULL, $video_id_prefix = "file_video_", $source = FALSE) {
        $flv_path = \NULL;
        $mp4_path = \NULL;
        $ogg_path = \NULL;
        $swf_path = \NULL;
        $webm_path = \NULL;
        $original_path = \NULL;
        $thumbnail = \NULL;
        $return = \NULL;

        if ($video_id == NULL) {
            $video_id = ":::vid:::";
        }
        if ($path == NULL) {
            $webm_path = ":::webm_path:::";
            $flv_path = ":::flv_path:::";
            $mp4_path = ":::mp4_path:::";
            $ogg_path = ":::ogg_path:::";
            $original_path = ":::ori_path:::";
            $path = ":::path:::";
            $thumbnail = ":::thumb:::";
        }
        else {
            $path = $this->stripexts($path);
            $flv_path = $path . ".flv";
            $mp4_path = $path . ".mp4";
            $ogg_path = $path . ".ogg";
            $swf_path = $path . ".swf";
            $webm_path = $path . ".webm";
            $original_path = $path . ".avi";
            $thumbnail = $path . ".jpg";

//            REM mp4  (H.264 / ACC)
//            "c:\program files\ffmpeg\bin\ffmpeg.exe" -i %1 -b 1500k -vcodec libx264 -vpre slow -vpre baseline                                           -g 30 -s 640x360 %1.mp4
//            REM webm (VP8 / Vorbis)
//            "c:\program files\ffmpeg\bin\ffmpeg.exe" -i %1 -b 1500k -vcodec libvpx                              -acodec libvorbis -ab 160000 -f webm    -g 30 -s 640x360 %1.webm
//            REM ogv  (Theora / Vorbis)
//            "c:\program files\ffmpeg\bin\ffmpeg.exe" -i %1 -b 1500k -vcodec libtheora                           -acodec libvorbis -ab 160000            -g 30 -s 640x360 %1.ogv
//            REM jpeg (screenshot at 10 seconds)
//            "c:\program files\ffmpeg\bin\ffmpeg.exe" -i %1 -ss 00:10 -vframes 1 -r 1 -s 640x360 -f image2 %1.jpg
        }
        $return .="<video width='100%' height='100%' id='" . $video_id_prefix . $video_id . "' class='video-js vjs-default-skin " . $classes . "'"
                . "preload='auto' controls poster='" . $thumbnail . "'"
                . "style='" . $styles . "' "
                . ">"; // data-setup={}
        if ($source == TRUE) {
            $return .= "<source src='" . $mp4_path . "' type='video/mp4'></source>"
                    . "<source src='" . $flv_path . "' type='video/x-flv'></source>"
                    . "<source src='" . $webm_path . "' type='video/webm'></source>";
            //. "<source src='" . $original_path . "' type='video/avi'></source>";
        }
        $return .= "<object data='" . $mp4_path . "' width='320' height='240'>"
                . "<embed src='" . $flv_path . "' width='320' height='240'>"
                . "</object>"
                . "</video>";
        return $return;
    }

    function trimStr($string, $length) {
        $string = (strlen($string) > $length) ? substr($string, 0, $length) . '...' : $string;
        return $string;
    }

    public function findexts($filename) {
        $filename = strtolower($filename);
        return pathinfo($filename, PATHINFO_EXTENSION);
    }

    public function stripexts($filename) {
        $exts = preg_replace("/\\.[^.\\s]{3,4}$/", "", $filename);
        return $exts;
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

    function date($time) {
        if (date('Ymd') == date('Ymd', $time)) {
            return date('H:i', $time);
        }
        return date('Y/m/d H:i', $time);
    }

    public function getPagePreview($url) {
        error_reporting(0);
        $this->url = $url;
        $parse = parse_url($this->url);
        $scheme = $parse['scheme'];
        if ($scheme == "") {
            $this->url = "http://" . $this->url;
        }
        $html = new simple_html_dom();
        $html->load($this->get_web_html($this->url), true, false);

        $return_info = array();
        $return_info['favicon'] = $this->getFavicon($this->url);
        $return_info['title'] = $this->trimStr($this->getTitle($html), 80);
        $return_info['description'] = $this->trimStr($this->getDescription($html), 100);
        return $return_info;
    }

    private function getTitle($html) {
        $title = $this->url;
        foreach ($html->find('title') as $element) {
            $title = $element->innertext;
        }
        return $title;
    }

    private function getFavicon($url) {
        return 'http://www.google.com/s2/favicons?domain=' . $url;
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
                        if ($tag == "") {
                            $tag = $html->find("h2", 0)->plaintext;
                            if ($tag == "") {
                                $tag = $html->find("h3", 0)->plaintext;
                                if ($tag == "") {
                                    $tag = $html->find("p", 0)->plaintext;
                                    if ($tag == "") {
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

    function create_zip($destination = '', $files = array(), $overwrite = false) {
        $destination = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $destination);
        if (file_exists($destination) && !$overwrite) {
            return false;
        }
        $valid_files = array();
        if (is_array($files)) {
            foreach ($files as $file) {
                if (file_exists($file)) {
                    $valid_files[] = $file;
                }
            }
        }
        $zip = new ZipArchive();
        if ($zip->open($destination, $overwrite ? ZIPARCHIVE::OVERWRITE : ZIPARCHIVE::CREATE) !== true) {
            //echo "Zip NO";
            return false;
        }
        $zip->addFromString('Copyright.txt', Base::COPYRIGHT_ZIP);
        foreach ($valid_files as $file) {
            $zip->addFile($file, $file);
        }

        $zip->close();

        return file_exists($destination);
    }

    function add_to_zip($destination = '', $files = array()) {
        $zip = new ZipArchive;
        if ($zip->open($destination) === TRUE) {
            foreach($files as $file) {
                $zip->addFile($file['path'], $file['name']);
                $zip->close();
            }
            return true;
        }
        else {
            return false;
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
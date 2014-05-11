<?php

class Files {

    private static $files = NULL;
    public static $PARENT_DIR = NULL;
    public $extension_to_mime = array(
        'acx' => 'application/internet-property-stream',
        'ai' => 'application/postscript',
        'aif' => 'audio/x-aiff',
        'aifc' => 'audio/x-aiff',
        'aiff' => 'audio/x-aiff',
        'asf' => 'video/x-ms-asf',
        'asr' => 'video/x-ms-asf',
        'asx' => 'video/x-ms-asf',
        'au' => 'audio/basic',
        'avi' => 'video/x-msvideo',
        'axs' => 'application/olescript',
        'bas' => 'text/plain',
        'bcpio' => 'application/x-bcpio',
        'bin' => 'application/octet-stream',
        'bmp' => 'image/bmp',
        'c' => 'text/plain',
        'cat' => 'application/vnd.ms-pkiseccat',
        'cdf' => 'application/x-cdf',
        'cdf' => 'application/x-netcdf',
        'cer' => 'application/x-x509-ca-cert',
        'class' => 'application/octet-stream',
        'clp' => 'application/x-msclip',
        'cmx' => 'image/x-cmx',
        'cod' => 'image/cis-cod',
        'cpio' => 'application/x-cpio',
        'crd' => 'application/x-mscardfile',
        'crl' => 'application/pkix-crl',
        'crt' => 'application/x-x509-ca-cert',
        'csh' => 'application/x-csh',
        'css' => 'text/css',
        'dcr' => 'application/x-director',
        'der' => 'application/x-x509-ca-cert',
        'dir' => 'application/x-director',
        'dll' => 'application/x-msdownload',
        'dms' => 'application/octet-stream',
        'doc' => 'application/msword',
        'docx' => 'application/msword',
        'dot' => 'application/msword',
        'dvi' => 'application/x-dvi',
        'dxr' => 'application/x-director',
        'eps' => 'application/postscript',
        'etx' => 'text/x-setext',
        'evy' => 'application/envoy',
        'exe' => 'application/octet-stream',
        'fif' => 'application/fractals',
        'flr' => 'x-world/x-vrml',
        'gif' => 'image/gif',
        'gtar' => 'application/x-gtar',
        'gz' => 'application/x-gzip',
        'h' => 'text/plain',
        'hdf' => 'application/x-hdf',
        'hlp' => 'application/winhlp',
        'hqx' => 'application/mac-binhex40',
        'hta' => 'application/hta',
        'htc' => 'text/x-component',
        'htm' => 'text/html',
        'html' => 'text/html',
        'htt' => 'text/webviewhtml',
        'ico' => 'image/x-icon',
        'ief' => 'image/ief',
        'iii' => 'application/x-iphone',
        'ins' => 'application/x-internet-signup',
        'isp' => 'application/x-internet-signup',
        'jfif' => 'image/pipeg',
        'jpe' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'jpg' => 'image/jpeg',
        'js' => 'application/javascript',
        'latex' => 'application/x-latex',
        'lha' => 'application/octet-stream',
        'lsf' => 'video/x-la-asf',
        'lsx' => 'video/x-la-asf',
        'lzh' => 'application/octet-stream',
        'm13' => 'application/x-msmediaview',
        'm14' => 'application/x-msmediaview',
        'm3u' => 'audio/x-mpegurl',
        'man' => 'application/x-troff-man',
        'mdb' => 'application/x-msaccess',
        'me' => 'application/x-troff-me',
        'mht' => 'message/rfc822',
        'mhtml' => 'message/rfc822',
        'mid' => 'audio/mid',
        'mny' => 'application/x-msmoney',
        'mov' => 'video/quicktime',
        'movie' => 'video/x-sgi-movie',
        'mp2' => 'video/mpeg',
        'mp3' => 'audio/mpeg',
        'mpa' => 'video/mpeg',
        'mpe' => 'video/mpeg',
        'mpeg' => 'video/mpeg',
        'mpg' => 'video/mpeg',
        'mpp' => 'application/vnd.ms-project',
        'mpv2' => 'video/mpeg',
        'ms' => 'application/x-troff-ms',
        'msg' => 'application/vnd.ms-outlook',
        'mvb' => 'application/x-msmediaview',
        'nc' => 'application/x-netcdf',
        'nws' => 'message/rfc822',
        'oda' => 'application/oda',
        'p10' => 'application/pkcs10',
        'p12' => 'application/x-pkcs12',
        'p7b' => 'application/x-pkcs7-certificates',
        'p7c' => 'application/x-pkcs7-mime',
        'p7m' => 'application/x-pkcs7-mime',
        'p7r' => 'application/x-pkcs7-certreqresp',
        'p7s' => 'application/x-pkcs7-signature',
        'pbm' => 'image/x-portable-bitmap',
        'pdf' => 'application/pdf',
        'pfx' => 'application/x-pkcs12',
        'pgm' => 'image/x-portable-graymap',
        'pko' => 'application/ynd.ms-pkipko',
        'pma' => 'application/x-perfmon',
        'pmc' => 'application/x-perfmon',
        'pml' => 'application/x-perfmon',
        'pmr' => 'application/x-perfmon',
        'pmw' => 'application/x-perfmon',
        'pnm' => 'image/x-portable-anymap',
        'png' => 'image/png',
        'pot' => 'application/vnd.ms-powerpoint',
        'ppm' => 'image/x-portable-pixmap',
        'pps' => 'application/vnd.ms-powerpoint',
        'ppt' => 'application/vnd.ms-powerpoint',
        'pptx' => 'application/vnd.ms-powerpoint',
        'prf' => 'application/pics-rules',
        'ps' => 'application/postscript',
        'pub' => 'application/x-mspublisher',
        'qt' => 'video/quicktime',
        'ra' => 'audio/x-pn-realaudio',
        'ram' => 'audio/x-pn-realaudio',
        'ras' => 'image/x-cmu-raster',
        'rgb' => 'image/x-rgb',
        'rmi' => 'audio/mid',
        'roff' => 'application/x-troff',
        'rtf' => 'text/rtf',
        'rtx' => 'text/richtext',
        'scd' => 'application/x-msschedule',
        'sct' => 'text/scriptlet',
        'setpay' => 'application/set-payment-initiation',
        'setreg' => 'application/set-registration-initiation',
        'sh' => 'application/x-sh',
        'shar' => 'application/x-shar',
        'sit' => 'application/x-stuffit',
        'snd' => 'audio/basic',
        'spc' => 'application/x-pkcs7-certificates',
        'spl' => 'application/futuresplash',
        'src' => 'application/x-wais-source',
        'sst' => 'application/vnd.ms-pkicertstore',
        'stl' => 'application/vnd.ms-pkistl',
        'stm' => 'text/html',
        'sv4cpio' => 'application/x-sv4cpio',
        'sv4crc' => 'application/x-sv4crc',
        'svg' => 'image/svg+xml',
        'swf' => 'application/x-shockwave-flash',
        't' => 'application/x-troff',
        'tar' => 'application/x-tar',
        'tcl' => 'application/x-tcl',
        'tex' => 'application/x-tex',
        'texi' => 'application/x-texinfo',
        'texinfo' => 'application/x-texinfo',
        'tgz' => 'application/x-compressed',
        'tif' => 'image/tiff',
        'tiff' => 'image/tiff',
        'tr' => 'application/x-troff',
        'trm' => 'application/x-msterminal',
        'tsv' => 'text/tab-separated-values',
        'txt' => 'text/plain',
        'uls' => 'text/iuls',
        'ustar' => 'application/x-ustar',
        'vcf' => 'text/x-vcard',
        'vrml' => 'x-world/x-vrml',
        'wav' => 'audio/x-wav',
        'wcm' => 'application/vnd.ms-works',
        'wdb' => 'application/vnd.ms-works',
        'wks' => 'application/vnd.ms-works',
        'wmf' => 'application/x-msmetafile',
        'wps' => 'application/vnd.ms-works',
        'wri' => 'application/x-mswrite',
        'wrl' => 'x-world/x-vrml',
        'wrz' => 'x-world/x-vrml',
        'xaf' => 'x-world/x-vrml',
        'xbm' => 'image/x-xbitmap',
        'xla' => 'application/vnd.ms-excel',
        'xlc' => 'application/vnd.ms-excel',
        'xlm' => 'application/vnd.ms-excel',
        'xls' => 'application/vnd.ms-excel',
        'xltv' => 'application/vnd.ms-excel',
        'xlw' => 'application/vnd.ms-excel',
        'xof' => 'x-world/x-vrml',
        'xpm' => 'image/x-xpixmap',
        'xwd' => 'image/x-xwindowdump',
        'z' => 'application/x-compress',
        'zip' => 'application/zip',
    );
    public $mime_to_extension = array();
    public $alias_mime = array(
        'text/javascript' => 'application/javascript',
        'application/x-javascript' => 'application/javascript',
        'rtf' => 'application/rtf',
    );

    public function __construct() {
        foreach ($this->extension_to_mime as $ext => $mimetype) {
            $this->mime_to_extension[$mimetype] = $ext;
        }
    }

    public static function getInstance($args = array()) {
        if (self :: $files) {
            return self :: $files;
        }

        self :: $files = new Files;
        return self :: $files;
    }

    function getList($dir = null, $id = null) {
        $sql = "SELECT * FROM file WHERE user_id = :user_id AND type != 'Webpage' AND visible = 1 AND parent_folder_id = :pf ORDER BY name;";
        $sql = Registry::get('db')->prepare($sql);
        $sql->execute(array(
            ":user_id" => Registry::get('user')->user_id,
            ":pf" => $dir
        ));
        $return_array = $sql->fetchAll(PDO::FETCH_ASSOC);
        foreach ($return_array as $key => $value) {
            $return_array[$key] = $this->format_file($return_array[$key]);
        }
        return $return_array;
    }

    public function getSharedList($viewed_id = NULL, $id = null, $parent_folder = 0) {
        if ($id == null) {
            $id = Registry::get('user')->user_id;
        }
        $sql = "SELECT * FROM file WHERE type != 'Webpage' AND id IN (SELECT file_id FROM file_share WHERE user_id = :viewed_id "
                . "AND (receiver_id = :user_id "
                . "OR group_id IN(SELECT group_id FROM group_member WHERE member_id = :user_id)));";
        $sql = Registry::get('db')->prepare($sql);
        $sql->execute(array(
            ":user_id" => Registry::get('user')->user_id,
            ":viewed_id" => $viewed_id,
        ));
        $return_array = $sql->fetchAll();
        return $return_array;
    }

    public function getActivity($file_id) {
        $sql = "SELECT activity_id FROM activity_media WHERE file_id = :file_id AND activity_id IN"
                . " (SELECT id FROM activity WHERE type = 'File'); ";
        $sql = Registry::get('db')->prepare($sql);
        $sql->execute(array(
            ":file_id" => $file_id
        ));
        return $sql->fetchColumn();
    }

    public function get_folder_file_id($folder_id) {
        $sql = "SELECT id FROM file WHERE user_id = :user_id AND folder_id = :folder_id;";
        $sql = Registry::get('db')->prepare($sql);
        $sql->execute(array(
            ":folder_id" => $folder_id,
            ":user_id" => Registry::get('user')->user_id
        ));
        return $sql->fetchColumn();
    }

    public function getInfo($file_id) {
        $sql = "SELECT * FROM file WHERE id = :file_id;";
        $sql = Registry::get('db')->prepare($sql);
        $sql->execute(array(
            ":file_id" => $file_id));
        return $sql->fetch(PDO::FETCH_ASSOC);
    }

    public function format_file($file) {
        require_once('home.class.php');
        $this->home = Home::getInstance($args = array());
        $activity = array('id' => $this->getActivity($file['id']));
        if ($file['type'] != "Folder") {
            $file['type'] = $this->getType($this->mime_content_type($file['path']));
        }
        if (!isset($file['type_preview'])) {
            $file['type_preview'] = $this->getFileTypeImage($file, 'THUMB');
        }
        if (!isset($file['uid'])) {
            $file['uid'] = str_replace('.', '', uniqid('', true));
        }
        $file['enc_parent_folder_id'] = Registry::get('system')->encrypt($file['parent_folder_id']);
        $file['time'] = Registry::get('system')->format_dates($file['time']);
        if (!isset($file['view']['count'])) {
            $file['view']['count'] = $this->getViewCount($file['id']);
        }
        if (!isset($file['like']['count'])) {
            $file['like']['count'] = $this->home->getLikeNumber($activity['id']);
        }
        if (!isset($file['activity']['id'])) {
            $file['activity']['id'] = $this->getActivity($file['id']);
        }
        $file['activity']['stats'] = $this->home->getStats($activity);
        return $file;
    }

    public function styleRecentlyShared($file) {
        $post_classes = " class='files_feed_item ";
        $post_styles = " style='";
        $container = "<div";
        $post_content = "";

        if ($file['type'] == "Audio") {
            $post_classes .= "files_shared_audio";
            $post_styles .= " height:auto; ";
            $post_content .= $this->audioPlayer($file['thumb_path'], $file['name'], false, false);
        }
        else if ($file['type'] == "Image") {
            //$post_styles .= "background-image:url(\"" . $file['thumb_path'] . "\")' "
            //. "onclick='initiateTheater(\"no_text\", " . $file['id'] . ");";
        }
        else if ($file['type'] == "Video") {
            $post_classes .= "files_shared_video";
            $post_content .= $this->videoPlayer(
                    $file['id'], $file['path'], $classes, "height:100%;", "home_feed_video_", TRUE, "display:none;");
        }
        else if ($file['type'] == "PDF Document") {
            //$post_styles .= " display:none; ";
            //$post_content .= "<embed src='viewer?id=" . $file['id'] . "' width='100%' height='100%'>";    TOO MANY RESOURCES
        }
        else if ($file['type'] == "Webpage") {
            $post_classes .= "post_media_full";
            $post_styles .= "height:auto;";
            $post_content .= "<table style='height:100%;'><tr><td rowspan='3'><div class='post_media_webpage_favicon' style='background-image:url(&quot;" . $file['web_favicon'] . "&quot;);'></div></td>"
                    . "<td><div class='ellipsis_overflow' style='position:relative;margin-right:30px;'>" .
                    "<a class='user_preview_name' target='_blank' href='" . $file['URL'] . "'><span style='font-size:13px;'>" . $file['web_title'] . "</span></a></div></td></tr>" .
                    "<tr><td><span style='font-size:12px;' class='user_preview_community'>" . $file['web_description'] . "</span></td></tr></table>";
        }
        return "<td><div " . $post_classes . "' " . $post_styles . "'>" . $post_content . "</div></td>";
    }

    function get_shared($file_id) {
        $sql = "SELECT group_id, user_id FROM activity_share WHERE activity_id IN "
                . "(SELECT activity_id FROM activity_media WHERE file_id = :file_id);";
        $sql = Registry::get('db')->prepare($sql);
        $sql->execute(array(
            ":file_id" => $file_id
        ));
        $return = $sql->fetchAll(PDO::FETCH_ASSOC);
        return $return;
    }

    public function convert($from, $to, $args = NULL, $before_args = NULL) {
//     	$from = urlencode($from);
//         $to = urlencode($to);
        $report = "-report"; //" -report ";/
        $progress = $to . '.txt';

        chdir('C:/inetpub/wwwroot/Global_Tools/ffmpeg/bin/');
        $cmd = 'ffmpeg ' . $report . $before_args . ' -i "' . $from . '" ' . $args . ' "' . $to . '" -y 1> ' . $progress . ' 2>&1';
        $result = shell_exec($cmd);

        return $to;
    }

    public function getConversionProgress($file) {
        $content = @file_get_contents($file . ".txt");
        if ($content) {
            //get duration of source
            preg_match("/Duration: (.*?), start:/", $content, $matches);

            $rawDuration = $matches[1];

            //rawDuration is in 00:00:00.00 format. This converts it to seconds.
            $ar = array_reverse(explode(":", $rawDuration));
            $duration = floatval($ar[0]);
            if (!empty($ar[1]))
                $duration += intval($ar[1]) * 60;
            if (!empty($ar[2]))
                $duration += intval($ar[2]) * 60 * 60;

            //get the time in the file that is already encoded
            preg_match_all("/time=(.*?) bitrate/", $content, $matches);

            $rawTime = array_pop($matches);

            //this is needed if there is more than one match
            if (is_array($rawTime)) {
                $rawTime = array_pop($rawTime);
            }

            //rawTime is in 00:00:00.00 format. This converts it to seconds.
            $ar = array_reverse(explode(":", $rawTime));
            $time = floatval($ar[0]);
            if (!empty($ar[1]))
                $time += intval($ar[1]) * 60;
            if (!empty($ar[2]))
                $time += intval($ar[2]) * 60 * 60;

            //calculate the progress
            $progress = round(($time / $duration) * 100);

            echo "Duration: " . $duration . "<br>";
            echo "Current Time: " . $time . "<br>";
            echo "Progress: " . $progress . "%";
        }
    }

    function getType($mime_type, $file_id = NULL) {
        $mime_type = $this->alias_mime_type($mime_type);
        $extn = '';
        switch ($mime_type) {

            case "image/png" :
            case "image/jpg" :
            case "image/jpeg" :
            case "image/svg" :
            case "image/gif" :
            case "image/ico" :
                $extn = "Image";
                break;

            case "video/mp4" :
            case "video/mov" :
            case "video/wmv" :
            case "video/avi" :
            case "video/mpg" :
            case "video/mpeg" :
            case "video/m4p" :
            case "video/mkv" :
                $extn = "Video";
                break;

            case "audio/mp3" :
            case "audio/mpeg" :
            case "audio/wav" :
            case "audio/m4a" :
                $extn = "Audio";
                break;

            case "text/plain" :
            case "text/rtf" :
                $extn = "Text File";
                break;
            case "application/pdf" :
                $extn = "PDF Document";
                break;
            case "application/msword" :
                $extn = "WORD Document";
                break;
            case "application/vnd.ms-powerpoint" :
                $extn = "PPT Document";
                break;
            case "application/vnd.ms-excel" :
                $extn = "EXCEL Document";
            case "zip" :
                $extn = "ZIP Archive";
                break;
            case "bak" :
                $extn = "Backup File";
                break;

            //CODE
            case "text/html" :
            case "text/css" :
            case "application/js" :
            case "text/php" :
            case "text/cs":
            case "application/x-msdownload":
                $extn = "Code";

            case "" :
                $extn = "Folder";
                break;

            default :
                $extn = strtoupper($extn) . " File";
                break;
        }
        return $extn;
    }

    function mime_content_type($path) {
        $path = $_SERVER['DOCUMENT_ROOT'] . "/" . $path;
        @$file_info = new finfo(FILEINFO_MIME);  // object oriented approach!
        @$mime_type = $file_info->buffer(file_get_contents($path));  // e.g. gives "image/jpeg"
        @$mime_type = explode(';', $mime_type);
        return $this->alias_mime_type($mime_type[0]);
    }

    function alias_mime_type($mime) {
        if (array_key_exists($mime, $this->alias_mime)) {
            return $this->alias_mime[$mime];
        }
        else {
            return $mime;
        }
    }

    function get_content($parent_folder = 0, $user_id = null) {
        if ($user_id == null) {
            $user_id = Registry::get('user')->user_id;
        }
        $sql = "SELECT * FROM file WHERE type != 'Webpage' AND user_id = :user_id "
                . "AND parent_folder_id = :parent_folder AND visible=1 ORDER BY name;";
        $sql = Registry::get('db')->prepare($sql);
        $sql->execute(array(":user_id" => $user_id, ":parent_folder" => $parent_folder));
        $sql = $sql->fetchAll(PDO::FETCH_ASSOC);
        foreach ($sql as $key => $file) {
            $sql[$key] = $this->format_file($file);
        }
        return $sql;
    }

    public function getViewCount($file) {
        $sql = "SELECT COUNT(id) FROM file_view WHERE file_id = :file_id;";
        $sql = Registry::get('db')->prepare($sql);
        $sql->execute(array(
            ":file_id" => $file
        ));
        return $sql->fetchColumn();
    }

    private function printDirections($file, $viewed_id) {
        $sql = "SELECT id, file_id, user_id, position, group_id, receiver_id FROM file_share WHERE file_id = :file_id;";
        $sql = Registry::get('db')->prepare($sql);
        $sql->execute(array(":file_id" => $file['id']));
        $file_props = $sql->fetch(PDO::FETCH_ASSOC);

        $options = array();

        $group_sql = "SELECT group_name FROM `group` WHERE id = :receiver_id;";

        $final_sql;

        switch ($file_props) {
            case isset($file_props['group_id']):
                $final_sql = $group_sql;
                $options = array(
                    ":receiver_id" => $file_props['group_id']
                );
                break;

            case isset($file_props['receiver_id']):
                echo $receiver = Registry::get('user')->getName($file_props['receiver_id']);
                break;
        }
        $sql = Registry::get('db')->prepare($final_sql);
        $sql->execute($options);
        $receiver = $sql->fetchColumn();

        if (!empty($final_sql)) {
            echo $receiver;
        }
        echo "<p class='files ellipsis_overflow'>" . Registry::get('user')->getName($viewed_id) . " &#65515; &#10162; <em>" . $this->stripexts($file['name']) . "</em></p><br>";
    }

    public function getName($file_id) {
        $sql = "SELECT name FROM file WHERE id = :file_id;";
        $sql = Registry::get('db')->prepare($sql);
        $sql->execute(array(
            ":file_id" => $file_id
        ));
        return $sql->fetchColumn();
    }

    public function getDescription($file_id) {
        $sql = "SELECT description FROM file WHERE id = :file_id;";
        $sql = Registry::get('db')->prepare($sql);
        $sql->execute(array(
            ":file_id" => $file_id
        ));
        return $sql->fetchColumn();
    }

    public function getParentFolder($file_id) {
        $sql = "SELECT parent_folder_id FROM file WHERE id = :file_id;";
        $sql = Registry::get('db')->prepare($sql);
        $sql->execute(array(
            ":file_id" => $file_id
        ));
        return $sql->fetchColumn();
    }

    public function filePreview($file, $size = 'icon') {
//        if ($file['type'] == NULL) {
//            $file['type'] == $this->getType($info['path']);
//        }
        if ($size == 'icon') {
            return $this->tinyPreview($file);
        }
        else if ($size == 'thumb') {
            return $this->thumbPreview($file);
        }
    }

    public function tinyPreview($file) {
        if ($file['type'] == "Image") {
            return $this->tinyPreviewHelper($file['thumb_path'], "files_icon_preview_image");
        }
        else {
            return $this->tinyPreviewHelper($this->getFileTypeImage($file, 'ICON'), "files_icon_preview_image");
        }
    }

    public function getPath($file_id, $size = 'thumb') {
        if ($size != "") {
            $size .= "_";
        }
        $sql = "SELECT " . $size . "path FROM file WHERE id = :file_id;";
        $sql = Registry::get('db')->prepare($sql);
        $sql->execute(array(
            ":file_id" => $file_id
        ));
        return $sql->fetchColumn();
    }

    public function getAttr($file_id, $attr) {
        $sql = "SELECT " . $attr . " FROM file WHERE id = :file_id;";
        $sql = Registry::get('db')->prepare($sql);
        $sql->execute(array(
            ":file_id" => $file_id
        ));
        return $sql->fetchColumn();
    }

    private function tinyPreviewHelper($path, $div_classes = NULL) {
        return "<div class='files_icon_preview " . $div_classes . "' style='background-image:url(\"" . $path . "\");'></div>";
    }

    private function thumbPreview($file) {
        if ($file['type'] == "Audio") {
            echo $this->audioPreview($file);
        }
        else if ($file['type'] == "Image") {
            echo $this->imagePreview($file);
        }
        else if ($file['type'] == "Video") {
            echo $this->videoPreview($file);
        }
        else if ($file['type'] == "Folder") {
            echo $this->folderPreview($file);
        }
        else if ($file['type'] == "PDF Document") {
            echo $this->pdfPreview($file);
        }
        else {
            $file['thumb_path'] = $this->getFileTypeImage($file);
            echo $this->imagePreview($file);
        }
    }

    function getFileTypeImage($file, $size = "THUMB") {
        if ($file['type'] == "Audio") {
            return constant("BASE::AUDIO_THUMB"); //CHANGE TO SIZE
        }
        else if ($file['type'] == "Image") {
            return constant("BASE::IMAGE_" . $size);
        }
        else if ($file['type'] == "Video") {
            return constant("BASE::VIDEO_" . $size);
        }
        else if ($file['type'] == "Folder") {
            return constant("BASE::FOLDER_" . $size);
        }
        else if ($file['type'] == "PDF Document") {
            return constant("BASE::PDF_" . $size);
        }
        else if ($file['type'] == "WORD Document") {
            return constant("BASE::WORD_" . $size);
        }
        else if ($file['type'] == "EXCEL Document") {
            return constant("BASE::EXCEL_" . $size);
        }
        else if ($file['type'] == "ZIP Archive") {
            return constant("BASE::ZIP_" . $size);
        }
        else if ($file['type'] == "PPT Document") {
            return constant("BASE::POWERPOINT_" . $size);
        }
        else if ($file['type'] == "Text File") {
            return constant("BASE::TEXT_" . $size);
        }
        else if ($file['type'] == "Code") {
            return constant("BASE::CODE_" . $size);
        }
        else {
            return constant("BASE::FILE_" . $size);
        }
    }

    function createFolder($parent_folder = 1, $name) {
        $sql = "SELECT MAX(folder_id) FROM file WHERE user_id = :user_id;";
        $sql = Registry::get('db')->prepare($sql);
        $sql->execute(array(":user_id" => Registry::get('user')->user_id));
        $new_folder_id = $sql->fetchColumn();
        if ($new_folder_id == "") {
            $new_folder_id = 1;
        }
        else {
            $new_folder_id++;
        }

        $sql = "INSERT INTO file(user_id, name, path, type, folder_id, parent_folder_id, time, last_mod) "
                . "VALUES (:user_id, :name, :path, :type, :folder_id, :parent_folder_id, :time, :time);";
        $sql = Registry::get('db')->prepare($sql);
        $sql->execute(array(
            ":user_id" => Registry::get('user')->user_id,
            ":name" => $name,
            ":path" => "User/Files/" . Registry::get('user')->user_id . "/" . $name . ".zip",
            ":type" => "Folder",
            ":folder_id" => $new_folder_id,
            ":parent_folder_id" => $parent_folder,
            ":time" => time(),
        ));

//        $this->create_zip($_SERVER['DOCUMENT_ROOT'] . "/User/Files/" . Registry::get('user')->user_id . "/" . $name . ".zip", array(), true);

        return $new_folder_id;
    }

    function getParentId($folder_id) {
        $sql = "SELECT parent_folder_id FROM file WHERE user_id = :user_id AND folder_id = :folder_id;";
        $sql = Registry::get('db')->prepare($sql);
        $sql->execute(array(":user_id" => Registry::get('user')->user_id, ":folder_id" => $folder_id));
        return $sql->fetchColumn();
    }

    function delete($id) {
        $sql = "SELECT path, "
                . "thumb_path, icon_path,"
                . " flv_path, mp4_path, "
                . "webm_path, ogg_path, "
                . "thumbnail, type, folder_id "
                . "FROM `file` WHERE user_id = :user_id AND id = :id";
        $sql = Registry::get('db')->prepare($sql);
        $sql->execute(array(":user_id" => Registry::get('user')->user_id, ":id" => $id));
        $file = $sql->fetch(PDO::FETCH_ASSOC);
        echo $file['type'] . "/";
        if ($file['type'] != "Folder") {
            foreach ($file as $key => $value) {
                if ($key != "type" || $key != "folder_id") {
                    //unlink("../" . $value);
                }
            }
        }
        else {
            $sql = "SELECT id "
                    . "FROM `file` WHERE user_id = :user_id AND parent_folder_id = :parent_folder;";
            $sql = Registry::get('db')->prepare($sql);
            $sql->execute(array(
                ":user_id" => Registry::get('user')->user_id,
                ":parent_folder" => $file['folder_id']
            ));
            $sub_file = $sql->fetchAll();
            //echo "Fetching sub files...";
            foreach ($sub_file as $new_file) {
                //echo " - Sub File: ".$id['id'];
                $this->delete($new_file['id']);
            }
        }
        $sql = "UPDATE file SET visible=0 WHERE user_id = :user_id AND id = :id;";
        $sql = Registry::get('db')->prepare($sql);
        $sql->execute(array(
            ":user_id" => Registry::get('user')->user_id,
            ":id" => $id));
    }

    private function removeDir_r($dir) {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (filetype($dir . "/" . $object) == "dir")
                        rrmdir($dir . "/" . $object);
                    else
                        unlink($dir . "/" . $object);
                }
            }
            reset($objects);
            rmdir($dir);
        }
    }

    function shareFile($file_id, $receivers) {

        switch ($receivers) {
            case isset($file_props['group_id']):
                $final_sql = $group_sql;
                $options = array(
                    ":receiver_id" => $file_props['group_id']
                );
                break;

            case isset($file_props['receiver_id']):
                echo $receiver = Registry::get('user')->getName($file_props['receiver_id']);
                break;
        }
    }

    function fileView($file_id) {
        $sql = "INSERT INTO `file_view` (file_id, user_id) VALUES (:file_id, :user_id);";
        $sql = Registry::get('db')->prepare($sql);
        $sql->execute(array(
            ":file_id" => $file_id,
            ":user_id" => Registry::get('user')->user_id
        ));
        return "";
    }

    function rename($file_id, $name) {
        $sql = "UPDATE file SET name = :name WHERE id = :file_id;";
        $sql = Registry::get('db')->prepare($sql);
        $sql->execute(array(
            ":name" => $name,
            ":file_id" => $file_id
        ));
    }

    function removeFromPost($file_id, $post_id) {
        $sql = "UPDATE activity_media SET visible = 0 WHERE file_id = :file_id AND activity_id = :post_id;";
        $sql = Registry::get('db')->prepare($sql);
        $sql->execute(array(
            ":post_id" => $post_id,
            ":file_id" => $file_id
        ));
    }

    function getUsedSize() {
        $sql = "SELECT size FROM file WHERE user_id = :user_id AND visible=1;";
        $sql = Registry::get('db')->prepare($sql);
        $sql->execute(array(
            ":user_id" => Registry::get('user')->user_id
        ));
        $used_size = 0;
        foreach ($sql->fetchAll(PDO::FETCH_ASSOC) as $size) {
            $used_size += $size['size'];
        }
        $used_size = round(($used_size / 1073741824) * 100);
        return $used_size;
    }

    function createFolders($folders) {
        $arranged = $this->parseArrayToTree($folders);
        foreach ($arranged as $arrange) {
            
        }
    }

    function parseArrayToTree($paths) {
        sort($paths);
        $tree = array();
        foreach ($paths as $path) {
            $path = trim($path, '/');
            $list = explode('/', $path);
            $n = count($list);

            $arrayRef = &$tree; // start from the root
            for ($i = 0; $i < $n; $i++) {
                $key = $list[$i];
                $arrayRef = &$arrayRef[$key]; // index into the next level
            }
        }

        $pf = 0;
        $dataArray = $this->formatTree($tree, '', $pf);
        die(json_encode($dataArray));
        return $dataArray;
    }

    function formatTree($tree, $prefix = '', $base_f) {
        $finalArray = array();
        
        foreach ($tree as $key => $value) {
            $levelArray = array();
            $path_parts = pathinfo($key);
            if (!empty($path_parts['extension']) && $path_parts['extension'] != '') {
                $extension = $path_parts['extension'];
            } else {
                if (empty($value)) {
                    $extension = "";
                }
                else if (is_array($value)) {
                    $extension = 'folder';
                }
            }

            if (is_array($value)) { //its a folder
//                $levelArray['data'] = array();
               
            } else { //its a file
                $levelArray['name'] = $key;
                $levelArray['href'] = $prefix . $key;
                $levelArray['parent_folder_id'] = $base_f;
            }

            // if the value is another array, recursively build the list$key
            if (is_array($value)) {
                $pf = $this->createFolder($base_f, $path_parts['filename']);
                $levelArray['folder_id'] = $pf;
                $levelArray['parent_folder_id'] = $base_f;
                $levelArray['children'] = $this->formatTree($value, $prefix . $key . "/", $pf);
            }

            $finalArray[] = $levelArray;
        } //end foreach

        return $finalArray;
    }

    function upload($post, $file) {
        require_once ('thumbnail.php');

        $tmpFilePath = $file['file']['tmp_name'];
        $savename = preg_replace("/[^a-z0-9]+/i", '', $file['file']['name']);
        $savepath = 'User/Files/' . Registry::get('user')->user_id . "/";
        $base_path = $_SERVER['DOCUMENT_ROOT'] . "/";
        $dir = $base_path . $savepath;
        $parent_folder = $post['parent_folder'];
        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }

        if ($file['file']['error'] > 0) {
            switch ($file['file']['error']) {
                case 1:
                    echo "File too large!";
                    break;

                case 2:
                    echo "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.";
                    break;

                case 3:
                    echo "The uploaded file was only partially uploaded.";
                    break;

                case 4:
                    //echo "No file was uploaded.";
                    break;

                case 6:
                    echo "Missing a temporary folder. Introduced in PHP 4.3.10 and PHP 5.0.3.";
                    break;

                case 7:
                    echo "Failed to write file to disk. Introduced in PHP 5.1.0.";
                    break;

                case 8:
                    echo "A PHP extension stopped the file upload. PHP does not provide a way to ascertain which extension caused the file upload to stop; examining the list of loaded extensions with phpinfo() may help. Introduced in PHP 5.2.0.";
                    break;

                default:
                    echo "Unknown Image Error." . $file['file']['error'][$count];
                    break;
            }
        }
        else {
            if ($file['file']['name'] != "" || ".") {
                $return_info = array();
                $lastInsertId;
                $name = preg_replace("/[^A-Za-z0-9]/", '', Registry::get('system')->stripexts($file['file']['name']));
                $ext = pathinfo($file['file']['name'], PATHINFO_EXTENSION);
                $file_name = $name . ($ext != null && $ext != "" ? "." : "" ) . $ext;
                $thumbnail = $savepath . $name . ".jpg";

                $flv_path = $mp4_path = $ogg_path = $swf_path = $webm_path = $mp3_path = $thumbsavepath = $iconsavepath = '';
                ignore_user_abort(true);
                set_time_limit(0);
                if (move_uploaded_file($tmpFilePath, "../" . $savepath . $file_name)) {
                    $mimetype = $this->mime_content_type($savepath . $file_name);
                    $size = filesize("../" . $savepath . $file_name);
                    $type = $this->getType($mimetype);
                    if ($type == "Audio") {
                        $convert_path = $base_path . $savepath . $file_name;
                        $iconsavepath = $mp3_path = $savepath . $name . "_thumb.mp3";
                        $thumbsavepath = $savepath . $name . "_thumb.ogg";
                        $convert = $this->convert($convert_path, $base_path . $mp3_path, '-ab 64');
                        if ($convert != $base_path . $mp3_path) {
                            echo ("Error: " . $convert);
                        }
                        $convert = $this->convert($convert_path, $base_path . $thumbsavepath, '-acodec libvorbis');
                        if ($convert != $base_path . $thumbsavepath) {
                            echo ("Error: " . $convert);
                        }
                    }
                    else if ($type == "Video") {
                        $convert_path = $base_path . $savepath . $file_name;
                        $mp4_path = $savepath . $name . ".mp4";
                        $ogg_path = $savepath . $name . ".ogg";
                        $flv_path = $savepath . $name . ".flv";
                        $webm_path = $savepath . $name . ".webm";
                        $flv_path = $savepath . $name . ".flv";
                        if ($ext != "mp4") {
                            $convert = $convert = $this->convert($convert_path, $base_path . $mp4_path, " -vcodec libx264 -profile:v baseline -preset ultrafast ");
                            if ($convert != $base_path . $mp4_path) {
                                echo("Error: " . $convert);
                            }
                        }

                        if ($ext != "webm") {
                            $convert = $convert = $this->convert($convert_path, $base_path . $webm_path, "-b 1500k -vcodec libvpx -acodec libvorbis -aq 3 -ab 128000 -f webm -g 30 -s 640x360", "");
                            if ($convert != $base_path . $webm_path) {
                                echo("Error: " . $convert);
                            }
                        }

//                       $convert = $this->convert($convert_path, $base_path . $webm_path, "-b 1500k -vcodec libvpx -acodec libvorbis -aq 3 -ab 128000 -f webm -g 30 -s 640x360", "");
//                       if ($convert != $base_path . $webm_path) {
//                           echo("Error: " . $convert);
//                       }
//                        $convert = $convert = $this->convert($convert_path, $base_path . $ogg_path, "");
//                        if ($convert != $base_path . $ogg_path) {
//                            echo("Error: " . $convert);
//                        }


                        if ($ext != "flv") {
                            $convert = $convert = $this->convert($convert_path, $base_path . $flv_path, "");
                            if ($convert != $base_path . $flv_path) {
                                echo("Error: " . $convert);
                            }
                        }
//                        $convert = $this->convert($convert_path, $base_path . $flv_path, "");
//                        if ($convert != $base_path . $flv_path) {
//                            echo("Error: " . $convert);
//                        }
                        $convert = $this->convert($convert_path, $base_path . $thumbnail, "");
                        if ($convert != $base_path . $thumbnail) {
                            echo("Error: " . $convert);
                        }
                    }
                    else {
                        $thumbsavepath = $savepath . "thumb_" . $file_name;
                        $iconsavepath = $savepath . "icon_" . $file_name;
                    }

                    if ($type == 'Image') {
                        require 'thumbnail.php';
                        $resizeObj = new resize("../" . $savepath . $file_name);
                        $resizeObj->resizeImage(300, 300, 'crop');
                        $resizeObj->saveImage("../" . $thumbsavepath);
                        $resizeObj->resizeImage(50, 50, 'crop');
                        $resizeObj->saveImage("../" . $iconsavepath);
                    }
                    Registry::get('db')->beginTransaction();
                    $sql = "INSERT INTO `file` (user_id, "
                            . "size, path, thumb_path, icon_path, "
                            . "thumbnail, flv_path, mp4_path, ogg_path, webm_path, "
                            . "name, type, mime_type, parent_folder_id, time, last_mod) "
                            . "VALUES (:user_id, :size, :file_path, "
                            . ":thumbsavepath, :iconsavepath, :thumbnail, "
                            . ":flv_path, :mp4_path, :ogg_path, :webm_path, "
                            . ":name, :type, :mime_type, :parent_folder, :time, :time);";
                    $sql = Registry::get('db')->prepare($sql);
                    $sql->execute(array(
                        ":user_id" => Registry::get('user')->user_id,
                        ":size" => $size,
                        ":file_path" => $savepath . $file_name,
                        ":thumbsavepath" => $thumbsavepath,
                        ":iconsavepath" => $iconsavepath,
                        ":thumbnail" => $thumbnail,
                        ":name" => $file['file']['name'],
                        ":type" => $type,
                        ":mime_type" => $mimetype,
                        ":parent_folder" => $parent_folder,
                        ":flv_path" => $flv_path,
                        ":mp4_path" => $mp4_path,
                        ":ogg_path" => $ogg_path,
                        ":webm_path" => $webm_path,
                        ":time" => time(),
                    ));
                    $lastInsertId = Registry::get('db')->lastInsertId();
                    Registry::get('db')->commit();

                    Registry::get('db')->beginTransaction();
                    $sql = "INSERT INTO `activity` (user_id, type, visible, time) "
                            . "VALUES (:user_id, :type, 0, :time);";
                    $sql = Registry::get('db')->prepare($sql);
                    $sql->execute(array(
                        ":user_id" => Registry::get('user')->user_id,
                        ":type" => 'File',
                        ":time" => time(),
                    ));
                    $lastActivityInsertId = Registry::get('db')->lastInsertId();
                    Registry::get('db')->commit();

                    Registry::get('db')->beginTransaction();
                    $sql = "INSERT INTO `activity_media` (activity_id, file_id) "
                            . "VALUES (:activity_id, :file_id);";
                    $sql = Registry::get('db')->prepare($sql);
                    $sql->execute(array(
                        ":activity_id" => $lastActivityInsertId,
                        ":file_id" => $lastInsertId,
                    ));
                    $lastActivityInsertId = Registry::get('db')->lastInsertId();
                    Registry::get('db')->commit();
                    if ($parent_folder == 0) {
                        $path = "User/Files/" . Registry::get('user')->user_id . "/root.zip";
                    }
                    else {
                        $path = $this->getAttr($this->get_folder_file_id($parent_folder), 'path');
                    }
//                    $this->add_to_zip($path, array($savepath . $file_name), TRUE);
                    return $this->format_file($this->getInfo($lastInsertId));
                }
                else {
                    echo "Upload Failed!";
                }
            }
        }
    }

}

if ($_SERVER['REQUEST_METHOD'] === "POST" && isset($_POST['action'])) {
    require('home.class.php');
    $home = Home::getInstance();
    $files = Files::getInstance();
    if (isset($_POST['dir'])) {
        $pdir = $_POST['dir'] . "/";
    }
    else {
        $pdir = null;
    }
    $dir = "../" . $pdir;
    if ($_POST['action'] == "getContents") {
        $files->getContents($_POST['parent_folder']);
    }
    else if ($_POST['action'] == "delete") {
        die($files->delete($_POST['id']));
    }
    else if ($_POST['action'] == "createFolder") {
        $files->createFolder($_POST['parent_folder'], $_POST['folder_name']);
    }
    else if ($_POST['action'] == "convert") {
        foreach ($_POST['file_info'] as $file) {
            $files->convert($file['from'], $file['to'], $file['args'], $file['before_args']);
        }
    }
    else if ($_POST['action'] == "get_conversion_progress") {
        foreach ($_POST['file_info'] as $file) {
            $files->getConversionProgress($file['to']);
        }
    }
    else if ($_POST['action'] == "share") {
        die($files->shareFile($_POST['file_id'], $_POST['receivers']));
    }
    else if ($_POST['action'] == "view") {
        die($files->fileView($_POST['file_id']));
    }
    else if ($_POST['action'] == "rename") {
        die($files->rename($_POST['file_id'], $_POST['text']));
    }
    else if ($_POST['action'] == "upload") {
        ignore_user_abort(true);
        die(json_encode($files->upload($_POST, $_FILES)));
    }
    else if ($_POST['action'] === "createFolders") {
        $folders = $_POST['files'];
        $files->createFolders($folders);
    }
    else if ($_POST['action'] == "preview") {
        $activity_id = NULL;
        if (isset($_POST['activity_id'])) {
            $activity_id = $_POST['activity_id'];
        }
        die(json_encode($home->homeify($home->getSingleActivity($files->getActivity($_POST['file_id'], "File")), 'home', $activity_id), JSON_HEX_APOS)); //HOME WAS PREVIEW
    }
    else if ($_POST['action'] == "removePostFile") {
        $files->removeFromPost($_POST['file_id'], $_POST['activity_id']);
    }
} 
else if ($_SERVER['REQUEST_METHOD'] == "GET" && isset($_GET['action'])) {
    require_once('declare.php');
    $files = Files::getInstance();
    if ($_GET['action'] === "list") {
        die(json_encode($files->getList($_GET['pf'])));
    }
}
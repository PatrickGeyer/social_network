<?php

class Base {
    public static $FB_LOGIN;
    public static $FB;

    public function __construct() {
        require_once($_SERVER['DOCUMENT_ROOT']."/../Global_Tools/facebook-php-sdk-master/facebook.php");
        $config = array(
            'appId' => '219388501582266',
            'secret' => 'c1684eed82295d4f1683367dd8c9a849',
            'fileUpload' => false, // optional
            'allowSignedRequest' => false, // optional, but should be set to false for non-canvas apps
        );
        self::$FB = new Facebook($config);
        self::$FB_LOGIN = self::$FB->getLoginUrl(array(
           'next' => '',
           'cancel_url' => '',
           //'req_perms' => 'email,publish_stream,status_update',
           'scope' => 'email,publish_stream,status_update,user_birthday'
        ));
    }

    const MALE_DEFAULT_ICON = "Images/male-default-icon.jpg";
    const MALE_DEFAULT_CHAT = "Images/male-default-chat.jpg";
    const MALE_DEFAULT_THUMB = "Images/male-default-chat.jpg";
    const FEMALE_DEFAULT_ICON = "Images/female-default-chat.jpg";
    const FEMALE_DEFAULT_CHAT = "Images/female-default-chat.jpg";
    const FEMALE_DEFAULT_THUMB = "Images/female-default-chat.jpg";
    const UNKNOWN_DEFAULT_ICON = "Images/unknown-default-chat.jpg";
    const UNKNOWN_DEFAULT_CHAT = "Images/unknown-default-chat.jpg";

    const SHORTCUT_ICON = "http://icons.iconarchive.com/icons/dakirby309/windows-8-metro/256/Other-Shortcuts-Metro-icon.png";
    const LOADING_ICON = "Images/ajax-loader.gif";
    const LIKE_ICON = "Images/Icons/Icon_Pacs/Batch-master/Batch-master/PNG/16x16/arrow-up.png";
    const DOWNLOAD_ARROW = "Images/Icons/Icon_Pacs/typicons.2.0/png-48px/arrow-down.png";
    const DOWN_ARROW_DARK = "Images/down.png";
    const DOWN_ARROW_LIGHT = "Images/down1.png";
    const DOWN_ARROW = "Images/down_arrow_select.png";
    const ARROW_RIGHT_BLACK = "Images/Icons/Icon_Pacs/typicons.2.0/png-48px/chevron-right.png";
    const ARROW_LEFT_BLACK = "Images/Icons/Icon_Pacs/typicons.2.0/png-48px/chevron-left.png";
    const ARROW_RIGHT_WHITE = "Images/Icons/Icon_Pacs/typicons.2.0/png-48px/chevron-right-outline.png";
    const ARROW_LEFT_WHITE = "Images/Icons/Icon_Pacs/typicons.2.0/png-48px/chevron-left-outline.png";
    const CONNECTION_ICON = "Images/Icons/icons/user-silhouette.png";
    const HEART_ICON = "Images/Icons/Icon_Pacs/Batch-master/Batch-master/PNG/16x16/heart-full.png";
    const EYE_ICON = "Images/Icons/Icon_Pacs/Batch-master/Batch-master/PNG/16x16/eye.png";
    const COMMENT_ICON = "Images/Icons/Icon_Pacs/Batch-master/Batch-master/PNG/16x16/speech-bubble-left-3.png";
    const VIDEO_BUTTON = "Images/icons/v_play.png";
    
    const INBOX_IMG = "Images/Icons/Icon_Pacs/ecqlipse2/system_white/MAIL_32x32-32.png";
    const NOTIFICATION_IMG = "Images/Icons/Icon_Pacs/ecqlipse2/system_white/WIFI_32x32-32.png";
    const NETWORK_IMG = "Images/Icons/Icon_Pacs/ecqlipse2/system_white/CD_32x32-32.png";
    const INBOX_IMG_BLACK = "Images/Icons/Icon_Pacs/ecqlipse2/system_black/MAIL_32x32-32.png";
    const NOTIFICATION_IMG_BLACK = "Images/Icons/Icon_Pacs/ecqlipse2/system_black/WIFI_32x32-32.png";
    const NETWORK_IMG_BLACK = "Images/Icons/Icon_Pacs/ecqlipse2/system_black/CD_32x32-32.png";
    
    const COMMENT_UNLIKE_TEXT = "Unlike";
    const COMMENT_LIKE_TEXT = "Like";
    const UNLIKE_TEXT = "dislikes";
    const LIKE_TEXT = "likes";
    const DELETE_OUTLINE = "Images/Icons/Icon_Pacs/typicons.2.0/png-48px/delete-outline.png";
    const DELETE = "Images/Icons/Icon_Pacs/typicons.2.0/png-48px/delete.png";
    
    
    const RELOAD_STILL_BLACK = "Images/Icons/Icon_Pacs/Batch-master/Batch-master/PNG/64x64/refresh.png";
    const ERROR_RED = "Images/Icons/bonus/icons-shadowless-32/cross.png";
    
    const IMAGE_QUALITY = 60;
    
    const VIDEO_THUMB = "Images/Icons/file_type/play-red.png";
    const IMAGE_THUMB = "Images/Icons/Icon_Pacs/typicons.2.0/png-24px/image.png";
    const FOLDER_THUMB = "Images/Icons/bonus/icons-32/blue-folder.png";
    const PDF_THUMB = "Images/Icons/file_type/pdficon_large.png";
    const WORD_THUMB = "Images/Icons/file_type/Microsoft_Word_2013_logo_thumb.png";
    const POWERPOINT_THUMB = "Images/Icons/file_type/Microsoft_PowerPoint_2013_logo_thumb.png";
    const EXCEL_THUMB = "Images/Icons/file_type/Microsoft_Excel_2013_logo_thumb.png";
    const ZIP_THUMB = "Images/Icons/Icon_Pacs/ecqlipse2/system_black/FILE - ZIP_48x48-32.png";
    const FILE_THUMB = "Images/Icons/Icon_Pacs/ecqlipse2/ecqlipse 2 - system black/FILE.png";
    const TEXT_THUMB = "Images/Icons/Icon_Pacs/ecqlipse2/ecqlipse%202%20-%20system%20black/FILE%20-%20TEXT.png";
    const CODE_THUMB = "Images/Icons/file_type/code_large.png";
    
    const VIDEO_ICON = "Images/Icons/file_type/play-red.png";
    const IMAGE_ICON = "Images/Icons/Icon_Pacs/typicons.2.0/png-24px/image.png";
    const FOLDER_ICON = "Images/Icons/bonus/icons-32/blue-folder.png";
    const PDF_ICON = "Images/Icons/file_type/pdficon_icon.png";
    const WORD_ICON = "Images/Icons/file_type/Microsoft_Word_2013_logo_icon.png";
    const POWERPOINT_ICON = "Images/Icons/file_type/Microsoft_PowerPoint_2013_logo_icon.png";
    const EXCEL_ICON = "Images/Icons/file_type/Microsoft_Excel_2013_logo_icon.png";
    const ZIP_ICON = "Images/Icons/Icon_Pacs/ecqlipse2/system_black/FILE - ZIP_48x48-32.png";
    const FILE_ICON = "Images/Icons/Icon_Pacs/ecqlipse2/ecqlipse 2 - system black/FILE.png";
    const TEXT_ICON = "Images/Icons/Icon_Pacs/typicons.2.0/png-24px/document-text.png";
    const CODE_ICON = "Images/Icons/file_type/code_icon.png";
    
    const AUDIO_THUMB = "Images/Icons/file_type/music-blue.png";
    const AUDIO_PLAY_THUMB = "Images/Icons/file_type/music-play-blue.png";
    const AUDIO_PAUSE_THUMB = "Images/Icons/file_type/music-pause-blue.png";
    
    const JQUERY_UI = "//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js";
    const JQUERY_UI_CSS = "//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/themes/smoothness/jquery-ui.css";
    const DATETIMEPICKER = "Scripts/external/datetimepicker/jquery.datetimepicker.js";
    const DATETIMEPICKER_CSS = "Scripts/external/datetimepicker/jquery.datetimepicker.css";
    
    const COPYRIGHT_ZIP = ' * Copyright 2014 Patrick Geyer.
 *
 * Licensed under the Apache License, Version 2.0;
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
';
    
//    DATABASE
    const LARGEST_INT = 2147483648;

}

?>
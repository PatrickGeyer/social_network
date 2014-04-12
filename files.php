<?php
include_once 'Scripts/declare.php';
function print_body() {
    global $used_width,
    $file_id,
    $files_user_id;
    $files = Registry::get('files');
    $files::$PARENT_DIR = 0;
    if (!isset($_GET['i'])) {
        $page_identifier = 'files';
    }

    if (isset($_GET['pd'])) {
        Files::$PARENT_DIR = urldecode(Registry::get('system')->decrypt($_GET['pd']));
        if (empty(Files::$PARENT_DIR)) {
            Files::$PARENT_DIR = 0;
        }
    }
    if (isset($_GET['a'])) {
        $actions = $_GET['a'];
    }
    else {
        $actions = true;
    }
    if (isset($_GET['u'])) {
        $files_user_id = urldecode(Registry::get('system')->decrypt($_GET['u']));
    }
    else {
        $files_user_id = Registry::get('user')->user_id;
    }
    if (isset($_GET['f'])) {
        $file_id = $_GET['f'];
        Registry::get('files')->fileView($file_id);
    }
    if (TRUE) {
        ?>
        <script>
            document.title = "Files";
            var parent_folder = <?php echo Files::$PARENT_DIR; ?>;
            var encrypted_folder = '<?php echo urlencode(Registry::get('system')->encrypt(Files::$PARENT_DIR)); ?>';

            function getInputFiles(element) {
                var files1 = document.getElementById(element).files;
                var files = new Array();
                for (var i = 0, len = files1.length; i < len; i++) {
                    files[i] = new Object();
                    files[i].file = files1[i];
                    files[i].extension = files1[i].name.split('.').pop();
                    files[i].name = files1[i].name;
                    files[i].type = 'File';
                    files[i].pic = VIDEO_THUMB;
                    files[i].size = files1[i].size;
                }
                return files;
            }
            function setRecentFileScroller() {
                $('.files_recently_shared_container').mCustomScrollbar({
                    scrollInertia: 100,
                    horizontalScroll: true,
                    theme: 'dark',
                    advanced: {
                        updateOnContentResize: true,
                        //autoExpandHorizontalScroll: true,
                    },
                    scrollButtons: {
                        enable: true,
                        scrollSpeed: 50,
                        // scrollAmount:
                    }
                });
                $('.files_recently_shared_container').find('.mCSB_horizontal').height("");
            }
            function readURL(input, image) {
                var reader = new FileReader();
                reader.readAsDataURL(input);
                reader.onload = function(e) {
                    image.css('background-image', "url('" + e.target.result + "')");
                };
            }
        </script>
        <div class='container'>
            <div class="files_recently_shared_container">
                <div class='files_recently_shared'>
                    <table>
                        <tr>
                            <?php
                            foreach (Registry::get('files')->getSharedList() as $file) {
                                echo Registry::get('files')->styleRecentlyShared($file);
                            }
                            ?>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="file_actions">
                <table style='padding-top: 15px;'>
                    <tr>
                        <td>
                            <button id='create_folder' class='pure-button-secondary small files_upload_option'>
                                <img style='height: 30px; width:30px' src='Images/Icons/Icon_Pacs/typicons.2.0/png-48px/folder-add-white.png'></img>
                            </button>
                        </td>
        <!--                        <td>
                            <div style='display:none;' id='folder_upload_dialog'>
                                <table cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td>
                                            <input type="file" name="file" id="folder" directory="" webkitdirectory="" mozdirectory="" />
                                        </td>
                                        <td>
                                            <button class='pure-button-green small files_upload_option' onclick='uploadFile("folder", "#folder");'>Upload Folder</button>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <button style='float:right;' id="folder_upload_option" class="pure-button-green small files_upload_option" onclick="showUpload('folder');">
                                <img style='height: 30px; width:30px' src='Images/Icons/Icon_Pacs/typicons.2.0/png-48px/folder-add-white.png'></img>
                            </button>
                        </td>-->
                        <td>
                            <div style='display:none' id='file_upload_dialog'>
                                <input type="file" id="file" multiple/>
                            </div>
                            <button id="file_upload_option" class='pure-button-error small files_upload_option' onclick="showUpload('file');">
                                <img style='height: 30px; width:30px' src='Images/Icons/Icon_Pacs/typicons.2.0/png-48px/upload-white.png'></img>
                            </button>
                        </td>
                        <td>
                            <img id='loading_icon' src='Images/ajax-loader.gif'></img>
                        </td>
                    </tr>
                </table>
            </div>
            <div id='file_container' style='padding-top: 20px;'>
                <div id='main_file' class="file feed_container" style='border-bottom:1px solid lightblue;'>
                    <?php
                    if (isset($file_id)) {
                        echo $file_id . " - ";
                        echo Registry::get('files')->getActivity($file_id);
                        echo "<script>$('.feed_container').prepend( Application.prototype.feed.homify(" . json_encode(Registry::get('home')->homeify(Registry::get('home')->getSingleActivity(Registry::get('files')->getActivity($file_id)))) . "));</script>";
                        echo "<script>$('.file_actions').hide();</script>";
                        echo "<script>$('#main_file').css('border', '0px');</script>";
                    }
                    else {
                        $files_list = Registry::get('files')->get_content(Files::$PARENT_DIR, $files_user_id);
                        echo "<script>$('.feed_container').append(Application.prototype.file.print_folder(" . json_encode($files_list) . "));</script>";
                    }
                    ?>
                </div>
            </div>
            <?php
            if ($files::$PARENT_DIR != 0) {
                echo "<button class='pure-button-neutral smallest' style='margin-top:20px;margin-left:20px;' "
                . "onclick='window.location.assign(&quot;files?pd=" . urlencode(Registry::get('system')->encrypt(Registry::get('files')->getParentId($parent_folder))) . "&quot;)'>Back</button>";
            }
            ?>
            <div id="progress_bar_holder"></div>
        </div>
    <?php
    }
}

require_once('Scripts/lock.php');

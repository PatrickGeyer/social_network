<?php
include_once('Scripts/lock.php');
if (!isset($_GET['i'])) {
    $page_identifier = 'files';
}

if (isset($_GET['pd'])) {
    $parent_folder = urldecode($system->decrypt($_GET['pd']));
    if (empty($parent_folder)) {
        $parent_folder = 0;
    }
}
else {
    $parent_folder = 0;
}
if (isset($_GET['a'])) {
    $actions = $_GET['a'];
}
else {
    $actions = true;
}
if (isset($_GET['u'])) {
    $files_user_id = urldecode($system->decrypt($_GET['u']));
}
else {
    $files_user_id = $user->user_id;
}
if (isset($_GET['f'])) {
    $file_id = $_GET['f'];
    $files->fileView($file_id);
}
include_once('welcome.php');
include_once('chat.php');
include_once('Scripts/base.class.php');

$used_width = $files->getUsedSize();
?>
<html>
    <head>
        <script src="Scripts/external/jquery.form.js"></script>
        <title>My Files</title>
        <script>
            var parent_folder = <?php echo $parent_folder; ?>;
            var encrypted_folder = '<?php echo urlencode($system->encrypt($parent_folder)); ?>';
            $(function() {
                $(document).on('click', '#create_folder', function() {
                    dialog(
                            content = {
                                type: "html",
                                content: '<input id="creat_folder_name" type="text" style="width:100%;" name="folder_name" placeholder="Name..." />'
                            },
                    buttons = [{
                            text: "Create",
                            type: "primary",
                            onclick: function() {
                                createFolder(parent_folder);
                            }
                        }],
                    properties = {
                        modal: false,
                        title: 'Create Folder',
                    });
                });
                $(document).on('click', 'div.files', function() {
                    var id = $(this).attr('file_id');
                    window.location.assign('files?f=' + id);
                });

                refreshVideoJs();

                $(document).on('change', '#file', function() {
                    var input = $(this).get(0).files;
                    var index = 0;
                    var files = getInputFiles('file');
                    var uploadCount = 0;
                        uploadFile(files,
                        function() {},
                        function(percent) {
                        }, function() {
                            refreshFileContainer(encrypted_folder);
                        }, 
                        properties = {
                            type: "File",
                        });
                });
                $('#loading_icon').fadeOut();

//                $(document).on('click', "div.files", function()
//                {
//                    var $this = $(this);
//                    $('.audio_hidden_container').not($this.children('.audio_hidden_container')).hide();
//                    $this.find('.audio_hidden_container').fadeIn("fast");
//                    setTimeout(function() {
//                        $('.audio_hidden_container').not($this.children('.audio_hidden_container')).hide();
//                    }, 10);
//                });

//var next = Math.ceil(n/12) * 12;

                $(document).on('mouseover', "div.files, div.folder", function()
                {
//                    $('.audio_hidden_container').not($(this).children('.audio_hidden_container')).hide();
//                    $(this).find('.audio_hidden_container').fadeIn("fast");
//                    setTimeout(function() {
//                        $('.audio_hidden_container').not($(this).children('.audio_hidden_container')).hide();
//                    }, 10);
                });
                $(document).on('mouseleave', "div.files, div.folder", function()
                {
                    $('.audio_hidden_container').fadeOut("fast");
                });

//                $(document).on('mouseover', "div.folder, div.folder", function()
//                {
//                    $('.audio_hidden_container').hide();
//                });

                $('.audio_hidden_container').hover(function() {
                }, function() {
                });

//                $(document).on('click', '.files_actions_share', function(e) {
//                    e.preventDefault();
//                    var file_id = $(this).data('file_id');
//                    var receivers;
//                    //alert($(this).data('file_id'));
//                    dialog(
//                            content = {
//                                type: "html",
//                                content: "<input type='text' class='search' mode='share'/><div class='search_results' id='share_div'></div>"
//                            },
//                    buttons = [{
//                            type: "success",
//                            text: "Share",
//                            onclick: function() {
//                                $.post("Scripts/files.class.php", {action: "share", file_id: file_id, receivers: receivers}, function(response) {
//                                    console.log(response);
//                                });
//                            }
//                        }],
//                    properties = {
//                        title: "Share file",
//                        modal: false
//                    }
//                    );
//                });
                setRecentFileScroller();
            });

            function getInputFiles(element) {
                var files1 = document.getElementById(element).files;
                var files = new Array();
                for (var i = 0, len = files1.length; i < len; i++) {
                    files[i] = new Object();
                    files[i].file = files1[i];
                    files[i].extension = files1[i].name.split('.').pop();
                    files[i].name = files1[i].name;
                    files[i].type = getType(files[i].extension);
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
    </head>
    <body>
        <div class='global_container'>
            <?php include_once 'left_bar.php'; ?>
            <div class="container" id="files">
                <div class='files_space_container'>
                    <div class='files_space_meter' style='width:<?php echo $used_width; ?>%;'></div>
                </div>
                <div class="files_recently_shared_container">
                    <div class='files_recently_shared'>
                        <table>
                            <tr>
                                <?php
                                foreach ($files->getSharedList() as $file) {
                                    echo $files->styleRecentlyShared($file);
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
                                                <button class='pure-button-success small files_upload_option' onclick='uploadFile("folder", "#folder");'>Upload Folder</button>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                <button style='float:right;' id="folder_upload_option" class="pure-button-success small files_upload_option" onclick="showUpload('folder');">
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
                            echo "<script>$('.feed_container').prepend(homify(".json_encode($home->homeify($home->getSingleActivity($files->getActivity($file_id))))."));</script>";
                            echo "<script>$('.file_actions').hide();</script>";
                            echo "<script>$('#main_file').css('border', '0px');</script>";
                        }
                        else {
                            $files_list = $files->getContents($parent_folder, $files_user_id);
                            $nmr = count($files_list);
                            foreach ($files_list as $file) {
                                $files->tableSort($file);
                            }
                            if (isset($nmr) && $nmr <= 0) {
                                echo "<div class='files' onclick='if(event.stopPropagation){event.stopPropagation();}event.cancelBubble=true;'>No Files in this Directory</div>";
                            }
                        }
                        ?>
                    </div>
                </div>

                <?php
                if ($parent_folder != 0) {
                    echo "<button class='pure-button-neutral smallest' style='margin-top:20px;margin-left:20px;' onclick='window.location.assign(&quot;files?pd=" . urlencode($system->encrypt($files->getParentId($parent_folder))) . "&quot;)'>Back</button>";
                }
                ?>
                <div id="progress_bar_holder"></div>
            </div>
            <?php include_once 'right_bar.php';?>
        </div>
    </body>
</html>
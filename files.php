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
    $user_id = urldecode($system->decrypt($_GET['u']));
}
else {
    $user_id = $user->getId();
}
if (isset($_GET['f'])) {
    $file_id = $_GET['f'];
}
include_once('welcome.php');
include_once('chat.php');
include_once('Scripts/base.class.php');

$used_width = $files->getUsedSize();
$used_width = ($used_width / 1073741824) * 100;
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
                    dialog(
                            content = {
                                type: "html",
                                content: getInputFiles('file')
                            },
                    buttons = [{
                            type: 'success',
                            text: "Upload",
                            onclick: function() {
                                var uploadCount = 0;
                                $('.upload_preview').each(function() {
                                    uploadCount++;
                                    $(this).css('padding-bottom', '25px');
                                    $(this).css('margin-bottom', '3px');
                                    $(this).css('position', 'relative');
                                    progressBar($(this), $(this).attr('id'));
                                });
                                for (var i = 0; i < input.length; i++) {
                                    (function(sync_i) {
                                        uploadFile(input[sync_i],
                                                function() {

                                                }, function(percent) {
                                            updateProgress(sync_i + "_upload_preview", percent);
                                            //console.log(i + "_upload_preview - " +  percent);
                                        }, function() {
                                            removeProgress(sync_i + "_upload_preview");
                                            $("#" + sync_i + "_upload_preview").css('background-color', '#98FB98');
                                            uploadCount--;
                                            if (uploadCount == 0) {
                                                removeDialog();
                                                refreshFileContainer(encrypted_folder);
                                            }
                                        }, properties = {
                                            type: "File",
                                        }
                                        );
                                    })(i);
                                }
                                ;
                            }
                        },
                        {
                            type: 'primary',
                            text: "Cancel",
                            onclick: function() {

                            }
                        }],
                    properties = {
                        title: "Upload Files",
                        modal: false,
                    }
                    );
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
                    var $this = $(this);
                    $('.audio_hidden_container').not($this.children('.audio_hidden_container')).hide();
                    $this.find('.audio_hidden_container').fadeIn("fast");
                    setTimeout(function() {
                        $('.audio_hidden_container').not($this.children('.audio_hidden_container')).hide();
                    }, 10);
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

                $(document).on('click', '.files_actions_share', function(e) {
                    e.preventDefault();
                    var file_id = $(this).data('file_id');
                    var receivers;
                    //alert($(this).data('file_id'));
                    dialog(
                            content = {
                                type: "html",
                                content: "<input type='text' class='search' mode='share'/><div class='search_results' id='share_div'></div>"
                            },
                    buttons = [{
                            type: "success",
                            text: "Share",
                            onclick: function() {
                                $.post("Scripts/files.class.php", {action: "share", file_id: file_id, receivers: receivers}, function(response) {
                                    console.log(response);
                                });
                            }
                        }],
                    properties = {
                        title: "Share file",
                        modal: false
                    }
                    );
                });
                setRecentFileScroller();
            });

            function getInputFiles(element) {
                var index = 0;

                var div = $("<div></div>");
                var files = document.getElementById(element).files;
                var image = new Array();

                for (var i = 0, len = files.length; i < len; i++) {
                    var file = files[i];
                    var extension = file.name.split('.').pop();
                    var name = $("<span style='vertical-align:top;'></span>");
                    image[i] = $("<div class='files_upload_image_preview'></div>");
                    name.append(file.name);
                    var type = getType(extension)
                    if (type == "Video") {
                        image[i].css('background-image', "url('" + VIDEO_THUMB + "')");
                    } else if (type == "Image") {
                        readURL(file, image[i]);
                    } else if (type == "Audio") {
                        image[i].css('background-image', "url('" + AUDIO_THUMB + "')");
                    }
                    var size = file.size;
                    var file_container = $("<div  class='upload_preview' style='margin-bottom:10px;'>");
                    file_container.attr('id', index + "_upload_preview");
                    file_container.append(image[i]);
                    file_container.append(name);
                    file_container.append("<br />");
                    div.append(file_container);
                    index++;
                }
                return div;
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
                    <div id='main_file' class="file" style='border-bottom:1px solid lightblue;'>
                        <?php
                        if (isset($file_id)) {
                            $home->homeify($home->getSingleActivity($files->getActivity($file_id)));
                            echo "<script>$('.file_actions').hide();</script>";
                            echo "<script>$('#main_file').css('border', '0px');</script>";
                        }
                        else {
                            $files_list = $files->getContents($parent_folder, $user_id);
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
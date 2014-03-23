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
                $(document).on('click', 'div.files, div.folder', function() {
                    var id = $(this).attr('file_id');
                    $('.file_hidden_container').slideUp('fast');
                    //if($(this).hasClass('file_hidden_container_active')) {
                        //$(this).children('.file_hidden_container').slideUp('fast');
                    //} else {
                        $(this).children('.file_hidden_container').slideDown('fast');
                    //}
                    $(this).toggleClass('file_hidden_container_active');
                    
                    //window.location.assign('files?f=' + id);
                });

                refreshVideoJs();

                $(document).on('change', '#file', function() {
                    var input = $(this).get(0).files;
                    var index = 0;
                    var files = getInputFiles('file');
                    var uploadCount = 0;
                        Application.prototype.file.upload.upload(files,
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
            function print_folder_content(folder) {
                var string = '';
                console.log(folder);

                for(var file in folder) {
                    string += print_single_file(folder[file]);
                }
                $('.feed_container').append(string);
            }
            function print_single_file(file) {
                var string = '';

                if(file.type != "Folder") {
                    string += "<div data-file_id='" + file.id + "' class='files'>";
                } else {
                    string += "<div data-file_id='" + file.id + "' class='folder'>"; // SAME
                }
                string += "<div class='files_icon_preview' style='background-image:url(\"" + file.type_preview + "\");'></div>";
                string += "<p class='files ellipsis_overflow'>" + file.name + "</p>";
                
                string += "<div class='files_actions'><table cellspacing='0' cellpadding='0'><tr style='vertical-align:middle;'><td>";

                string +=  "<a href='" + file.path + "' download><div class='files_actions_item files_actions_download'></div></a></td><td>";
                string +=  "<hr class='files_actions_seperator'></td><td>";

                string += "<div class='files_actions_item files_actions_delete' "
                + "onclick='deleteFile(this, " + file.id + ");if(event.stopPropagation){event.stopPropagation();}"
                + "event.cancelBubble=true;'></div></td><td>"
                + "<hr class='files_actions_seperator'></td><td>"
                + "<div class='files_actions_item files_actions_share' data-file_id='" + file.id + "'></div></td>";
                string += "</tr></table></div>";

                //string += "<p class='files ellipsis_overflow' style='float:right;'>" + file.view.count + " <img class='heart_like_icon' src='" + EYE_ICON + "'/></p>";
                //string += "<p class='files ellipsis_overflow' style='float:right;margin-right:5px;'>" + file.like.count + " <img class='heart_like_icon' src='" + HEART_ICON + "'/></p>";

                string += "<div class='file_hidden_container'>";
                string += Application.prototype.file.print(file, "File");
                string += "</div>";

                string += "</div>";

                return string;
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
                            echo $file_id." - ";
                            echo $files->getActivity($file_id);
                            echo "<script>$('.feed_container').prepend( Application.prototype.feed.homify(".json_encode($home->homeify($home->getSingleActivity($files->getActivity($file_id))))."));</script>";
                            echo "<script>$('.file_actions').hide();</script>";
                            echo "<script>$('#main_file').css('border', '0px');</script>";
                        }
                        else {
                            $files_list = $files->get_content($parent_folder, $files_user_id);
                            //$nmr = count($files_list);
                            echo "<script>print_folder_content(".json_encode($files_list).");</script>";
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
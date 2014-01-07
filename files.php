<?php
include_once('Scripts/lock.php');
if (!isset($_GET['i'])) {
    $page_identifier = 'files';
}

if (isset($_GET['pd'])) {
    $parent_folder = urldecode($system->decrypt($_GET['pd']));
}
else {
    $parent_folder = 1;
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
include_once('welcome.php');
include_once('chat.php');
?>
<html>
    <head>
        <script src="Scripts/external/jquery.form.js"></script>
        <title>My Files</title>
        <script>
            var parent_folder = <?php echo $parent_folder; ?>;
            var encrypted_folder = '<?php echo urlencode($system->encrypt($parent_folder)); ?>';
            $(function(){
                $(document).on('click', '#create_folder', function(){
                    dialog(
                    {
                        type:"html",
                        content: '<input id="creat_folder_name" type="text" style="width:100%;" name="folder_name" placeholder="Name..." />'
                    },
                    {
                        text:"Create",
                        type:"primary",
                        onclick:function(){createFolder(parent_folder);}
                    },
                    {
                        modal:false,
                        title:'Create Folder',
                    });
                });
                
                refreshVideoJs();
                $('div.files_actions').hide();
                $(document).on('mouseenter', 'div.files', function(){
                $('div.files_actions').hide();
                    $(this).children('div.files_actions').fadeIn();
                });
                $(document).on('mouseleave', 'div.files', function(){
                    $(this).children('div.files_actions').hide();
                });
            });
        </script>
    </head>
    <body>
        <div class="container" id="files">
            <div class='files_recently_shared scroll_thin_horizontal'>
                <?php 
                foreach($files->getSharedList() as $file) {
                    echo $files->styleRecentlyShared($file);
                } 
                ?>
            </div>
            <table style='margin-left:16px;padding-top: 20px;'>
                <tr>
                    <td>
                        <button id='create_folder' class='pure-button-secondary small'>Create Folder</button>
                    </td>
                    <td>
                        <div style='display:none;' id='folder_upload_dialog'>
                            <table cellpadding="0" cellspacing="0">
                                <tr>
                                    <td>
                                        <input type="file" name="file" id="folder" directory="" webkitdirectory="" mozdirectory="" />
                                    </td>
                                    <td>
                                        <button class='pure-button-success small' onclick='uploadFile("folder", "#folder");'>Upload Folder</button>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <button style='float:right;' id="folder_upload_option" class="pure-button-success small" onclick="showUpload('folder');">Upload Folder</button>
                    </td>
                    <td>
                        <div style='display:none' id='file_upload_dialog'>
                            <table cellpadding="0" cellspacing="0">
                                <tr>
                                    <td>
                                        <input type="file" name="file" id="file" multiple/>
                                    </td>
                                    <td>
                                        <button class='pure-button-success small' onclick='uploadFile("file", "#file");'>Upload File</button>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <button id="file_upload_option" class='pure-button-success small' onclick="showUpload('file');">Upload File</button>
                    </td>
                    <td>
                        <img id='loading_icon' src='Images/ajax-loader.gif'></img>
                    </td>
                </tr>
            </table>
            <div id='file_container' style='padding-top: 20px;'>
                <div id='main_file' class="file" style='border-bottom:1px dotted lightblue;'>
                <?php
                $files_list = $files->getContents($parent_folder, $user_id);
                $nmr = count($files_list);
                foreach ($files_list as $file) {
                    $files->tableSort($file);
                }
                if (isset($nmr) && $nmr <= 0) {
                    echo "<div class='files' onclick='if(event.stopPropagation){event.stopPropagation();}event.cancelBubble=true;'>No Files in this Directory</div>";
                }
                ?>
                </div>
            </div>
            
                <?php
                if ($parent_folder != 1) {
                    echo "<button class='pure-button-neutral smallest' style='margin-top:20px;' onclick='window.location.assign(&quot;files?pd=" . urlencode($system->encrypt($files->getParentId($parent_folder))) . "&quot;)'>Back</button>";
                }
                ?>
            <div id="progress_bar_holder"></div>
        </div>
    </body>
</html>
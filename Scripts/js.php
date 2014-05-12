 <?php
Registry::get('system')->getGlobalMeta();
Registry::get('system')->jsVars();
?>

<script src="Scripts/external/jquery-1.10.2.js"></script>
<!--<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>-->
<!--<script>window.jQuery || document.write('<script src="Scripts/external/jquery-1.10.2.js">\x3C/script>');</script>-->

<link rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/themes/smoothness/jquery-ui.min.css" />
<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
<script>window.jQuery.ui || document.write('<script src="Scripts/external/jquery-1.10.2.js">\x3C/script>');</script>

<script src="Scripts/external/cookie.min.js"></script>

<script src="//cdn.jsdelivr.net/jquery.mcustomscrollbar/2.8.1/jquery.mCustomScrollbar.min.js"></script>
<script>window.mCustomScrollbar || document.write('<script src="Scripts/external/jquery.mCustomScrollbar.min.js">\x3C/script>');</script>
<link href="Scripts/external/jquery.mCustomScrollbar.min.css" rel="stylesheet" type="text/css" />

<link href="Scripts/external/video-js/video-js.css" rel="stylesheet">
<script src="Scripts/external/video-js/video.js"></script>

<script src="Scripts/external/swfobject/swfobject.js"></script>

<script>
if(swfobject.hasFlashPlayerVersion("3")) {
    _V_.options.techOrder = ["flash", "html5", "links"];
} else {
	_V_.options.techOrder = ["html5", "links"];
    console.log('noflash');
}</script>

<!--ScrollTo plugin-->
<!--<script defer src="http://balupton.github.com/jquery-scrollto/scripts/jquery.scrollto.min.js"></script>-->
 
<!-- History.js --> 
<script defer src="http://balupton.github.com/history.js/scripts/bundled/html4+html5/jquery.history.js"></script>
 
<!-- Ajaxify -->
<!--<script defer src="Scripts/external/ajaxify-html5.js"></script>-->


<script type='text/javascript' src="Scripts/js.js"></script>

<!--<script src="Scripts/external/wavesurfer.js"></script>-->

<script src='<?php echo Base::DATETIMEPICKER; ?>'></script>
<link rel="stylesheet" href="<?php echo Base::DATETIMEPICKER_CSS; ?>" />


<script>
//     
    _V_.options.flash.swf = "Scripts/external/video-js/video-js.swf";
</script>

<script>
var MyUser = new Application.prototype.User.prototype.MyUser();
MyUser.attr = <?php echo json_encode(Registry::get('user')->get_user_preview()); ?> ;
</script>


<script type="text/javascript">
    var loggedIn = getCookie('id');
    var min_activity_id = 0;

//    function createMap(city, country) {
//        var geocoder = new google.maps.Geocoder();
//        geocoder.geocode({'address': city + ", " + country}, function(results, status) {
//            if (status == google.maps.GeocoderStatus.OK) {
//                var latLng = new google.maps.LatLng(results[0].geometry.location.lat(), results[0].geometry.location.lng());
//                var mapOptions = {
//                    center: latLng,
//                    zoom: 8
//                };
//                var element = $("#map-canvas")[0];
//                var map = new google.maps.Map(element, mapOptions);
//
//                var marker = new google.maps.Marker({
//                    position: latLng,
//                    map: map,
//                    title: "User's location, based on IP.",
//                    draggable: true,
//                });
//                google.maps.event.addListener(marker, "dragend", function() {
//                    Application.prototype.UI.dialog(
//                            content = {
//                                type: "html",
//                                content: "Are you sure you want to update your location?",
//                            },
//                            buttons = [{
//                                    type: "primary",
//                                    text: "Yes",
//                                    onclick: function() {
//                                        updateLocation(" + marker.position.lat() + ", " + marker.position.lng() + ");
//                                    },
//                                }],
//                            properties = {
//                                modal: false,
//                                title: 'Location',
//                            });
//                });
//            } else {
//                $("#map-canvas").hide();
//            }
//        });
//    }
//    function updateLocation(lat, lng)
//    {
//        $.post('Scripts/user.class.php', {lat: lat, lng: lng}, function(response) {
//            alert(response);
//        });
//    }

// FILES

    var parent_folder = <?php echo (isset($parent_folder) ? $parent_folder : "0" ); ?>;

//END FILES

    $(function() {
        var id;
    });

    var currentPage = getCookie('current_feed');

// #HOME
    $(function() {
        $(document).on('click', '.post_media_single_close_webpage', function() {
            removeFromStatus(object = {type: "Webpage", value: $(this).attr('post_file_id')});
        });
    });
    var post_media_added_files = new Array();
    function dialogLoad(start)
    {
        if (start == "stop")
        {
            $('.dialog_loading').fadeOut(0);
        }
        else
        {
            $('.dialog_loading').fadeIn(0);
        }
    }

    function show_Confirm(post_id)
    {
        $('#delete1_post_' + post_id).hide();
        $('#delete_post_' + post_id).css('visibility', 'visible').hide().slideDown('slow');
    }

    function delete_post(post_id)
    {
        $.post("Scripts/home.class.php", {action: "deletePost", post_id: post_id}, function() {
            $('#post_height_restrictor_' + post_id).slideUp(function() {
                $(this).remove();
            });
        });
    }

    function parseUrl1(data) {
        var e = /^\b((http|ftp):\/)?\/?([^:\/\s]+)((\/\w+)*\/)([\w\-\.]+\.[^#?\s]+)(#[\w\-]+)?\b/;

        if (data.match(e)) {
            return  {url: RegExp['$&'],
                protocol: RegExp.$2,
                host: RegExp.$3,
                path: RegExp.$4,
                file: RegExp.$6,
                hash: RegExp.$7};
        }
        else {
            return  {url: "", protocol: "", host: "", path: "", file: "", hash: ""};
        }
    }

    function parseUrl2(data) {
        var e = /((((https?|ftp|file):\/\/)|www.)[-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|](\s))/ig;

        if (data.match(e)) {
            return  {url: RegExp['$&'],
                protocol: RegExp.$2,
                host: RegExp.$3,
                path: RegExp.$4,
                file: RegExp.$6,
                hash: RegExp.$7};
        }
        else {
            return false;
        }
    }
    var addedURLs = new Array();
    var typedURLs = new Array();
    function checkLink(element) {
        var regularExpression = parseUrl2(element.val());
        if (regularExpression) {
            var new_url = regularExpression.url.trim();

            if (typedURLs.indexOf(new_url) == -1)
            {
                addedURLs.push(new_url);
                typedURLs.push(new_url);
                return new_url;
            }
            else
            {
                return false;
            }
        }
        else
        {
            return false;
        }
    }
    $(function() {
        $('#status_text').on('input', function() {
            var status_text = $(this).val();
            var link = checkLink($(this));
            var time = new Date().getTime();
            if (link != false) {
                post_media_load();
                $.post('Scripts/system.class.php', {action: "get_page_preview", url: link}, function(response) {
                    response = $.parseJSON(response);
                    response.path = link;
                    response.info = response;
                    response.id = formatToID(link);
                    response.type = "Webpage";
                    addToStatus(object, 'create');
                    post_media_load("stop");
                });
            }
            for (var i = 0; i < addedURLs.length; i++) {
                var e = new RegExp("(\\b)" + addedURLs[i] + "(\\b)");
                if (e.test(status_text) == false)
                {
                    //console.log("Removed from Status: " + formatToID(addedURLs[i]));
                    //console.log("typed: "+typedURLs + "Added"+ addedURLs[i]);
                    typedURLs = removeFromArray(typedURLs, addedURLs[i]);
                    removeFromStatus(object = {type: "Webpage", value: addedURLs[i]});
                }
            }
        });
    });

    function post_media_load(action) {
        if (action != "stop") {
            $('.post_media_loader').show();
        } else {
            $('.post_media_loader').hide();
        }
    }

    function removeFromArray(array, match) {
        for (var i = 0; i < array.length; i++)
        {
            if (array[i] == match)
            {
                array.splice(i, 1);
            }
        }
        return array;
    }
    function formatToID(text) {
        return text.replace(/^[^a-z]+|[^\w-]+/gi, "");
    }
    function resizeScrollers()
    {
        //$('.scroll_thin').mCustomScrollbar("update");
        // $('.scroll_thin_horizontal').mCustomScrollbar("update");
    }

// END HOME



    function joinGroup(group_id, invite_id)
    {
        $.post("Scripts/group.class.php", {action: "join", group_id: group_id, invite_id: invite_id}, function(response)
        {
            $('#join_button_' + invite_id).fadeOut('fast');
            $('#reject_button_' + invite_id).fadeOut('fast', function()
            {
                $('#leave_button_' + invite_id).fadeIn();
            });
        });
    }
    function rejectGroup(group_id, invite_id)
    {
        $.post("Scripts/group.class.php", {action: "reject", group_id: group_id, invite_id: invite_id}, function(response)
        {
            $('#reject_button_' + invite_id).slideUp();
            $('#join_button_' + invite_id).slideUp();
            //alert(response);
        });
    }

    function get_folder_contents(element, action, parent_folder, actions)
    {
        $('#loading_icon').show();
        if (action == "remove") {
            $(element).children('div').slideUp(function() {
            });
            var new_onclick = $(element).attr('previous_onclick');
            $(element).attr('onclick', new_onclick);
            $.post('Scripts/current_directory_cookie.php', {dir: "../"}, function(response)
            {
                current_directory = response;
            });
            $('#loading_icon').fadeOut();
        }
        else {
            $.post('Scripts/files.class.php', {parent_folder: parent_folder, action: "getContents", actions: actions}, function(response)
            {
                var previous_onclick = $(element).attr("onclick");
                $(element).attr("previous_onclick", previous_onclick);
                $(element).attr("onclick", "if(event.stopPropagation){event.stopPropagation();}event.cancelBubble=true;get_folder_contents(this, 'remove');");
                if (animation != "none")
                {
                    $("<div style='padding:6px;'></div>" + response).appendTo($(element)).hide().slideToggle();
                }
                else
                {
                    $("<div style='padding:6px;'></div>" + response).appendTo($(element));
                }
                $('#loading_icon').fadeOut();
            });
            $.post('Scripts/current_directory_cookie.php', {dir: dir}, function(response)
            {
                current_directory = response;
            });
        }
    }
    function deleteFile(element, id)
    {
        $('#loading_icon').show();
       $('#file_div_' + id).hide();
        $.post('Scripts/files.class.php', {action: "delete", id: id}, function(response)
        {
            $('#file_div_hidden_container_' + id).remove();
            $('#file_div_' + id).fadeOut(function() {
                $(this).remove();
            });
            $('#loading_icon').fadeOut();
            refreshFileContainer(encrypted_folder);
        });
    }
    function refreshFileContainer(encrypted_folder) {
        refreshElement('#file_container', 'files', 'pd=' + encrypted_folder, '#main_file', function(data) {
            var files = $(data).find('#main_file');
            $('#main_file').html(files.html());
            refreshVideoJs();
        });
    }

    function refreshVideoJs() {
        return;
        $('video.video-js').each(function() {
            var video_id = $(this).attr('id');
            //console.log('Video: ' + video_id + ", type is: " + typeof _V_.players[video_id]);

            if (typeof _V_.players[video_id] === "undefined") {
                //console.log('Creating video for: '+ video_id);
                videojs(video_id, {}, function() {
                    this.on('play', function() {
                        videoPlay(video_id);
                    });
                    this.on('pause', function() {
                        videoPause(video_id);
                    });
                    this.on('ended', function() {
                        videoEnded(video_id);
                    });
                });

            }
        });
    }
    function fileView(id) {
        $.post("Scripts/files.class.php", {file_id: id, action: "view"}, function(response) {
            //console.log("ID: " + id + " -Viewed File: " + response);
        });
    }
    function createFolder(parent_folder)
    {
        dialogLoad();
        $('#loading_icon').show();
        var folder_name = $('#creat_folder_name').val();
        $.post("Scripts/files.class.php", {action: "createFolder", parent_folder: parent_folder, folder_name: folder_name}, function(response) {
            if ("error".indexOf(response) > 0)
            {
                alert("error!");
            }
            else
            {
                setTimeout(function() {
                    refreshCurrentDiv();
                }, 1000);
            }
            $('#loading_icon').fadeOut();
            dialogLoad('stop');
            removeDialog();
            refreshFileContainer(encrypted_folder);
        });
    }

    function getContentWidth(element) {
        var width = 0;
        $(element).children().each(function() {
            width += $(this).outerWidth(true);
        });
        return width;
    }
    function videoPause(id) {
        //console.log('paused video');
        if ($("#" + id).parents('#file_container').length !== 0) {
            id = id.replace(/[A-Za-z_$-]/g, "");
            $('#audio_play_icon_' + id).animate({opacity: "0"}, 200, function() {
                $('#audio_play_icon_' + id).css('visibility', 'hidden');
            });
        }
        // } else if($("#" + id).parents('.files_recently_shared').length !== 0) {
        //     $("#" + id).parents(".files_feed_active").animate({height :"-=200px", width: "-=200"},200, function(){
        //         resizeScrollers();
        //         //$('.files_recently_shared').mCustomScrollbar("scollTo", "#" + id);
        //     }).removeClass("files_feed_active");
        // }
    }
    function videoEnded(id) {
        if ($("#" + id).parents('#file_container').length !== 0) {

        } else if ($("#" + id).parents('.files_recently_shared').length !== 0) {
            //$("#" + id).parents().animate({height :"-=200px", width: "-=200"},200);
        }
    }
    function showUpload(type)
    {
        if (type == "folder")
        {
            $('#folder').trigger('click');
            $('#folder_upload_option').hide();
            $('#folder_upload_dialog').show();

            $('#file_upload_option').show();
            $('#file_upload_dialog').hide();
        }
        else if (type == "file")
        {
            $('#file').trigger('click');
            //$('#file_upload_option').hide();
            //$('#file_upload_dialog').show();

            //$('#folder_upload_option').show();
            //$('#folder_upload_dialog').hide();
        }
    }

    function pad(number)
    {
        return (number < 10 ? '0' : '') + number;
    }
    function refreshElement(element_to_load, page, query, div, onComplete)
    {
        $.get(page + '?' + query, function(data, status)
        {
            onComplete(data);
        });
    }
</script>
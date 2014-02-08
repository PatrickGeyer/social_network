<?php
include("lock.php");
$system->getGlobalMeta();
$system->jsVars();
?>

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
<script>window.jQuery || document.write('<script src="Scripts/external/jquery-1.10.2.js">\x3C/script>');</script>

<link rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/themes/smoothness/jquery-ui.min.css" />
<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
<script>window.jQuery.ui || document.write('<script src="Scripts/external/jquery-1.10.2.js">\x3C/script>');</script>

<script src="Scripts/external/cookie.min.js"></script>

<script src="//cdn.jsdelivr.net/jquery.mcustomscrollbar/2.8.1/jquery.mCustomScrollbar.min.js"></script>
<script>window.mCustomScrollbar || document.write('<script src="Scripts/external/jquery.mCustomScrollbar.min.js">\x3C/script>');</script>
<link href="Scripts/external/jquery.mCustomScrollbar.min.css" rel="stylesheet" type="text/css" />

<link href="Scripts/external/video-js/video-js.min.css" rel="stylesheet">
<script src="Scripts/external/video-js/video.min.js"></script>

<script src="Scripts/external/jquery.scrollTo-1.4.3.1.js"></script>

<script src="Scripts/eventhandlers.js"></script>
<script src="Scripts/js.js"></script>

<script>
    _V_.options.flash.swf = "Scripts/external/video-js/video-js.swf";

    window.onerror = function(message, url, lineNumber) {
        if (message.indexOf("offsetTop") !== -1)
        {
            return true;
        }
    };
</script>

<script type="text/javascript">
    var min_activity_id = 0;

    function createMap(city, country) {
        var geocoder = new google.maps.Geocoder();
        geocoder.geocode({'address': city + ", " + country}, function(results, status) {
            if (status == google.maps.GeocoderStatus.OK) {
                var latLng = new google.maps.LatLng(results[0].geometry.location.lat(), results[0].geometry.location.lng());
                var mapOptions = {
                    center: latLng,
                    zoom: 8
                };
                var element = $("#map-canvas")[0];
                var map = new google.maps.Map(element, mapOptions);

                var marker = new google.maps.Marker({
                    position: latLng,
                    map: map,
                    title: "User's location, based on IP.",
                    draggable: true,
                });
                google.maps.event.addListener(marker, "dragend", function() {
                    dialog(
                            content = {
                                type: "html",
                                content: "Are you sure you want to update your location?",
                            },
                            buttons = [{
                                    type: "primary",
                                    text: "Yes",
                                    onclick: function() {
                                        updateLocation(" + marker.position.lat() + ", " + marker.position.lng() + ");
                                    },
                                }],
                            properties = {
                                modal: false,
                                title: 'Location',
                            });
                });
            } else {
                $("#map-canvas").hide();
            }
        });
    }
    function updateLocation(lat, lng)
    {
        $.post('Scripts/user.class.php', {lat: lat, lng: lng}, function(response) {
            alert(response);
        });
    }
</script>
<script>
    function getType(extension) {
        switch (extension)
        {
            case "png":
            case "jpg":
            case "jpeg":
            case "gif":
                return "Image";
                break;

            case "mov":
            case "wmv":
            case "mp4":
            case "avi":
                return "Video";
                break;

            default:
                return "File";
        }
    }
</script>

<script>
    function delay(elem, time, callback) {
        var timeout = null;
        elem.onmouseover = function() {
            // Set timeout to be a timer which will invoke callback after 1s
            timeout = setTimeout(callback, time);
        };

        elem.onmouseout = function() {
            // Clear any timers set to timeout
            clearTimeout(timeout);
        }
    }

    $(function() {
        refreshVideoJs();
    });
// FILES

    var parent_folder = <?php echo (isset($parent_folder) ? $parent_folder : "0" ); ?>;

    $(function()
    {
        $('.image_placeholder').load(function() {
            resizeDiv($(this).attr('f_id'));
        });

        $('#upload_file').click(function() {
            $('#upload_file').hide();
            $('#upload_file_dialog').fadeIn();
        });

        $('#create_folder').click(function() {
            //$('#create_folder_dialog').dialog(
            //	{ buttons: [ { text: "Create Folder", click: function() { $( this ).dialog( "close" ); createFolder(parent_folder);} } ] });
        });
    });
    function progressBar(element, id) {
        $(element).append("<div id='progress_container_" + id + "' class='progress_container'><div id='progress_bar_"+id+"' class='progress_bar'></div></div>");
    }
    function updateProgress(id, progress) {
        $('#progress_bar_' + id).width(progress + "%");
    }
    function removeProgress(id) {
        $('#progress_container_' + id).remove();
    }
    var q = 0;
    function uploadFile(input, onStart, onProgress, onComplete, properties)
    {
        onStart();
        var files;
        var length = 1;

        properties = typeof properties !== 'undefined' ? properties : {type: 'Input'};
        properties.type = typeof properties.type !== 'undefined' ? properties.type : 'Input';

        if(properties.type == "File") {
            files = new Array();
            files[0] = input;
        } else {
            if(input instanceof $) {
                files = input.get(0).files;
                // alert('jquery');
                // return;
            }
            else {
               files = input;
            }
            length = files.length;
        }

        if(!length) {
            length = 1; //Incase single file is chosen
        }
        //console.log(files);
        for (var count = 0; count < length; count++)
        {
            q++;
            var file = files[count];
            var formdata = new FormData();
            formdata.append("file", file);
            formdata.append("action", 'upload');
            formdata.append("parent_folder", parent_folder);
            var xhr = new XMLHttpRequest();
            xhr.upload.onprogress = function(event) {
                progressHandler(event, count, onProgress);
            };
            xhr.onload = function() {
                completeHandler(this, count - 1, onComplete, name);
            };
            xhr.addEventListener("error", errorHandler, false);
            xhr.addEventListener("abort", abortHandler, false);
            xhr.open("post", "Scripts/files.class.php");
            xhr.send(formdata);
        }
    }
    function progressHandler(event, id, callback)
    {
        $('#loading_icon').show();
        var percent = (event.loaded / event.total) * 100;
        percent = Math.round(percent);
        $("#progressBar").width(percent + '%');
        if (q > 1)
        {
            $("#status").text(q + " items uploading...");
        }
        else
        {
            $("#status").text(q + " item uploading... " + percent + "%");
        }
        callback(percent);
    }
    function completeHandler(event, id, onComplete, name)
    {
        if (q > 1)
        {
            q--;
        }
        else
        {
            $("#status").text("Upload Successful!");
            $("#progressContainer").hide();
            $("#status").fadeOut(5000);
            $('#loading_icon').fadeOut();
        }
        if (onComplete == "addToStatus")
        {
            response = $.parseJSON(event.responseText);
            var object = new Object();
            object.path = response.path;
            object.name = response.filename;
            object.file_id = response.file_id;
            addToStatus("Image", object);
        }
        else
        {
            if(q == 0) {
                removeDialog();
                refreshFileContainer(encrypted_folder);
            }
        }
        onComplete($.parseJSON(event.responseText));
        $("#progressContainer").hide();
    }
    function errorHandler(event)
    {
        if (q > 1)
        {
            q--;
        }
        _("status").innerHTML = "Upload Failed!";
        $('#loading_icon').fadeOut();
    }
    function abortHandler(event)
    {
        if (q > 1)
        {
            q--;
        }
        _("status").innerHTML = "Upload Aborted!";
        $('#loading_icon').fadeOut();
    }
    function resizeDiv(element)
    {
        if ($(element).height() > $(element).width())
        {
            $(element).width('auto');
        }
        else
        {
            $(element).height('auto');
        }
    }

//END FILES


    function scrollToBottom(element, force)
    {
        var bottom = false;
        if ($(element).scrollTop() - $(element).scrollHeight < 10) {
            bottom = true;
        }
        if (bottom === true || force === true) {
            $(element).scrollTop($(element)[0].scrollHeight);
        }
    }
    function adjustSize(input, above, offset) {
        $(input).on('keypress change propertychange input', function() {
            var height = $(input).height();
            $(above).css('bottom', height + offset);
        });
    }

    $(function() {
        var id;
        $(document).on('mouseenter', '.who_liked_hover', function() {
            id = $(this).attr("activity_id");
            $('.theater-info-container').find('*').each(function() {
                //alert('hi');
            });
            $('#who_liked_' + id).fadeIn();
        });
        $(document).on('mouseout', '.who_liked_hover', function() {
            id = $(this).attr("activity_id");
            $('#who_liked_' + id).fadeOut();
        });

        $('body').delegate('div[data-placeholder]', 'keydown keypress input', function()
        {
            if ($(this).text() == "")
            {
                this.dataset.divPlaceholderContent = 'true';
            }
            else
            {
                $(this).attr('data-placeholder', '');
            }
        });

        $(document).on('click', function()
        {
            $('#names_universal').hide();
            removeUserPreview("force");
        });

        $(document).on('click', '.search_box', function(e) {
            e.stopPropagation();
        });

        $(document).on('click', function()
        {
            $('#names_universal').hide();
            removeUserPreview("force");
        });

        $(document).on('mouseover', '.user_preview', function(event) {
            event.stopPropagation();
            var user_id = $(this).attr('user_id');
            showUserPreview($(this), user_id);
        });

        $(document).on('mouseover', "html", function(event) {
            removeUserPreview('check mouse', event);
        });

        alignNavFriend();
    });

    $(window).resize(function() {
        alignNavFriend();
        resizeScrollers();
        adjustTheater();
    });

    var currentPage = getCookie('current_feed');


    setInterval(function() {
        alignNavFriend();
        resizeScrollers();
    }, 500);
    function alignNavFriend()
    {
        var container_left = $('.container_headerbar').offset().left;
        container_left = container_left - $('.left_bar_container').outerWidth();

        var nav_height = $('.navigation').outerHeight(true);

        $('.left_bar_container').css('left', container_left - 1);
        $('#friends_container').css('top', nav_height + 22);
        $('.messagecomplete').css('top', nav_height + 22);
    }

    function scrollH(element_id, wrapper_id, speed)
    {
        //speed = typeof speed !== 'undefined' ? speed : 400;
        //var offset = ($(wrapper_id).width() / 2) - $(element_id).width() / 2;
        //$(wrapper_id).scrollTo(element_id, speed, {offset: 0 - offset});
    }

    function submitPost()
    {
        var text = $('#status_text').val();
        if (text != "" || post_media_added_files.length != 0)
        {
            modal($('body'), properties = {type: "", text: "Posting content..."});
            $.post("Scripts/update_status.php", {status_text: text, group_id: share_group_id, post_media_added_files: post_media_added_files}, function(data)
            {
                if (data == "")
                {
                    removeModal('', function() {
                        getFeedContent(share_group_id, min_activity_id, 'home', function() {
                        });
                    });
                    clearPostArea();
                }
                else
                {
                    alert(data);
                }
            });
        }
    }
    function Feed(value)
    {
        scrollH("#" + value, "#feed_wrapper_scroller", 0);
    }


// POPUP

    function getDialog()
    {
        return $('.dialog_container');
    }
    function dialog(content, buttons, properties)
    {
        properties.modal = (typeof properties.modal === "undefined") ? true : properties.modal;
        properties.loading = (typeof properties.loading === "undefined") ? false : properties.loading;
        properties.title = (typeof properties.title === "undefined") ? "Undefined Title" : properties.title;
        properties.width = (typeof properties.width === "undefined") ? "auto" : properties.width;

        var dialog_container = $("<div class='dialog_container'></div>").css({'opacity': '0'});
        $('body').append(dialog_container);

        var dialog_title = $("<div class='dialog_title'>" + properties.title +
                "<span onclick='removeDialog();' class='dialog_close_button'>x</span></div>");
        var content_container = $("<div class='dialog_content_container'></div>");
        dialog_container.append(dialog_title);
        dialog_container.append(content_container);

        if (content.type == "text")
        {
            dialog_container.append(content.content);
        }
        else if (content.type == "html")
        {
            content_container.append(content.content);
        }
        dialog_container.width(properties.width);
        var button_complete = $('<div></div>');
        for (var i = 0; i < buttons.length; i++) {
            var single_button = document.createElement('button');
            $(single_button).addClass('small');
            $(single_button).addClass('pure-button-' + buttons[i].type);
            $(single_button).css('float', 'right');
            $(single_button).text(buttons[i].text);
            single_button.onclick = buttons[i].onclick;
            button_complete.append(single_button);
        }

        var dialog_buttons = $("<div class='dialog_buttons'><img class='dialog_loading' src='Images/ajax-loader.gif'></img></div>");
        dialog_container.append(dialog_buttons);
        dialog_buttons.append(button_complete);

        if (properties.modal == true) {
            $('body').append("<div class='background-overlay'></div>");
        } else {
            $('body').append("<div onclick='removeDialog()' style='opacity:0.25' class='background_white_overlay'></div>");
        }

        if (properties.loading == true)
        {
            dialogLoad();
        }
        alignDialog();
        var real_height = dialog_container.height();
        dialog_container.css({height: "0px"});
        dialog_container.animate({minHeight: real_height + "px", opacity: 1}, 'fast', function() {
            dialog_container.css({height: "auto"});
        });
        content_container.mCustomScrollbar({
            scrollInertia: 10,
            autoHideScrollbar: true,
        });
        content_container.mCustomScrollbar("update");
        setTimeout(function() {
            content_container.mCustomScrollbar("update");
        }, 200);
    }

    function alignDialog()
    {
        var width = $('.dialog_container').width();
        var height = $('.dialog_container').height();
        $('.dialog_container').css({
            'margin-left': '-' + width / 2 + "px",
            'margin-top': '-' + height / 2 + "px",
        });
    }

    function removeDialog()
    {
        $('.background-overlay, .background_white_overlay').remove();

        $('.dialog_container').css('min-height', '0px');
        $('.dialog_container').animate({height: 0, opacity: 0}, 'fast', function() {
            $(this).remove();
        });
    }
// #POPUP
// #HOME
    $(function() {
        $(document).on('click', '.post_media_single_close_file', function() {
            removeFromStatus(object = {type: "File", value: $(this).attr('post_file_id')});
        });
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
    function submitcomment(comment_text, post_id, callback)
    {
        comment_text = comment_text.replace(/^\s+|\s+$/g,"");
        if (comment_text == "")
        {
            comment_text = $('div[actual_id="comment_' + post_id + '"]').val();
            comment_text = comment_text.replace(/^\s+|\s+$/g,"");
            if (comment_text == "")
            {
                $('#comment_' + post_id).focus();
                return;
            }

        }
        $('#comment_' + post_id).val("").blur();
        $('[actual_id="comment_' + post_id + '"]').blur().val('');

        $.post("Scripts/extends.class.php", {comment_text: comment_text, post_id: post_id, action: 'submitComment'}, function(data)
        {
            refreshContent(post_id);
            callback();
        });
    }

    function refreshContent(id)
    {
        // console.log('refeshing post');
       //var number_of_updates = 2;
       //var updates_done = 0;
       refreshPure(id); //Refresh immediately after comment
       var refresh_interval = setTimeout(function() {
           refreshPure(id)
       }, 10000);
       //if (++updates_done >= number_of_updates)
       //{
           //window.clearInterval(refresh_interval);
       //}
    }

    function refreshPure(id)
    {
        if (1 == 2)//$("*:focus").is("textarea, input") || $('.inputtext').is(":focus"))
        {
            //console.log('Comment submit cancelled: Textarea in focus.');
        }
        else
        {
            $.post('Scripts/home.class.php', {activity_id: id, action: "getComments"}, function(response)
            {
                var activity_id = $("#theater-info-container").attr("activity_id");
                if (typeof activity_id !== "undefined")
                {
                    $('.theater-info-container').children('.comments').find('.comment_box').children('div.single_comment_container, hr.post_comment_seperator').remove();
                    $('.theater-info-container').children('.comments').find('.comment_box').prepend(response);
                }
                $('#comment_div_' + id).children('div.single_comment_container').remove();
                $('#comment_div_' + id).children('hr.post_comment_seperator').remove();
                $('#comment_div_' + id).prepend(response);
            });
        }
    }

    function submitlike(id, receiver_id, type)
    {
        $.post("Scripts/home.class.php", {id: id, type: type, receiver_id: receiver_id, action: "like"}, function(data)
        {
            if (type == 1)
            {
                $('#' + id + 'likes').text(data + " " + LIKE_TEXT + " -");
            }
            else
            {
                $('#' + id + 'dislikes').text(data + " " + LIKE_TEXT + " -");
            }
        });
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
                    var object = new Object();
                    object.path = link;
                    object.info = response;
                    object.file_id = formatToID(link);
                    addToStatus("Webpage", object);
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

    function addToStatus(type, object)
    {
        $('.post_media_wrapper_background').hide();
        var post_media_classes = '';
        var post_media_style = " style=' ";
        var text_to_append = '';
        var additional_close = '';
        var extra_params = " post_file_id='" + object.file_id + "' ";
        if (type == "Image")
        {
            post_media_style += "background-image:url(&quot;" + object.path + "&quot;);";
            additional_close += " post_media_single_close_file";
            text_to_append += (">");
        } else if (type == "Audio")
        {
            post_media_classes += " post_media_audio";
            additional_close += " post_media_single_close_file";
            text_to_append += ">"
            var rndm = new Date().getTime();
            var title = object.name;
            var text = '<?php echo $system->audioPlayer(null, null, false, "blank"); ?>';
            text = text.replace(/:::path:::/g, object.path);
            text = text.replace(/:::name:::/g, title);
            text = text.replace(/:::uid:::/g, rndm);
            text_to_append += (text);

        } else if (type == "Video") {
            post_media_classes += " post_media_video";
            additional_close += " post_media_single_close_file";
            text_to_append += ">";
            var text = "<?php echo $system->videoPlayer(NULL, NULL, "post_media_video_element", "width:100%", "home_video_", TRUE); ?>";
            text = text.replace(/:::webm_path:::/g, object.info.webm_path);
            text = text.replace(/:::mp4_path:::/g, object.info.mp4_path);
            text = text.replace(/:::flv_path:::/g, object.info.flv_path);
            text = text.replace(/:::ogg_path:::/g, object.info.ogg_path);
            text = text.replace(/:::name:::/g, object.name);
            text = text.replace(/:::vid:::/g, object.file_id);
            text = text.replace(/:::thumb:::/g, object.info.thumbnail);
            text_to_append += (text);

        } else if (type == "Webpage") {
            post_media_classes += " post_media_full";
            post_media_style += "height:auto;";
            additional_close += " post_media_single_close_webpage";
            extra_params = " post_file_id='" + object.path + "' ";
            text_to_append += "><table style='height:100%;'><tr><td rowspan='3'>" +
                    "<div class='post_media_webpage_favicon' style='background-image:url(&quot;" + object.info.favicon + "&quot;);'></div></td>" +
                    "<td><div class='ellipsis_overflow' style='position:relative;margin-right:30px;'>" +
                    "<a class='user_preview_name' target='_blank' href='" + object.path + "'><span style='font-size:13px;'>" + object.info.title + "</span></a></div></td></tr>" +
                    "<tr><td><span style='font-size:12px;' class='user_preview_community'>" + object.info.description + "</span></td></tr></table>";
        } else if (type == "Folder") {
            additional_close += " post_media_single_close_file";
            text_to_append += "><div class='post_media_folder_image'></div>" +
                    "<span style='font-size:13px;'></span>";
        } else if (type == "WORD Document") {
            text_to_append += documentStatus(object.path, object.name, object.description, WORD_THUMB);

        } else if (type == "PDF Document") {
            text_to_append += documentStatus(object.path, object.name, object.description, PDF_THUMB);
        } else if (type == "PPT Document") {
            text_to_append += documentStatus(object.path, object.name, object.description, POWERPOINT_THUMB);
        } else {
            alert(type);
            text_to_append += "Type undetected";
        }
        if (type == "WORD Document" || type == "PDF Document" || type == "PPT Document" || type == "ACCESS Document" || type == "EXCEL Document") {
            post_media_classes += " post_media_double";
            post_media_style += "height:auto;";
            additional_close += " post_media_single_close";
            extra_params = " post_file_id='" + object.path + "' ";
        }
        var index;
        for (var i = 0; i < post_media_added_files.length; i++)
        {
            if (post_media_added_files[i].file_id == object.file_id || post_media_added_files[i] == object.file_id)
            {
                index = "found";
                dialog(
                        content = {
                            type: 'html',
                            content: "Sorry, but you have already added this file to your post. Please choose another instead!"
                        },
                buttons = [{
                        type: "error",
                        text: "OK",
                        onclick: function() {
                            removeDialog();
                        }
                    }],
                properties = {
                    modal: false,
                    title: "Whoops!"
                });
            }
        }
        var post_media = '<div class="post_media_single ' +
                post_media_classes + '" ' + post_media_style + "' " + ' id="post_media_single_' + object.file_id + '"';
        if (index != "found")
        {
            if (type == "Webpage") {
                post_media_added_files.push(object);
            } else {
                post_media_added_files.push(object.file_id);
            }

            $('.post_media_wrapper').append(post_media + text_to_append + "<div " + extra_params + " class='post_media_single_close" +
                    additional_close + "'></div><div class='post_media_single_close_background'></div></div>");
        }
        if (type == "Video") {

            videojs('home_video_' + object.file_id, {}, function() {
            });
        }
        if (post_media_added_files.length > 1) {
            $('#status_text').attr('placeholder', 'Write about these files...');
        }
        else {
            $('#status_text').attr('placeholder', 'Write about this file...');
        }
        $('#status_text').focus();
        $('#file_share').mCustomScrollbar("update");
    }
    function documentStatus(path, name, description, image) {
        return "><table style='margin:10px;height:100%;'><tr><td rowspan='3'>" +
                "<div style='margin-right:10px;height:63px;width:64px;background-size:contain;background-image:url(&quot;" + image + "&quot;);'></div></td>" +
                "<td><div class='ellipsis_overflow' style='position:relative;margin-right:30px;'>" +
                "<a class='user_preview_name' target='_blank' href=''><span style='font-size:13px;'>" + name + "</span></a></div></td></tr>" +
                "<tr><td><span style='font-size:12px;' class='user_preview_community'>" + description + "</span></td></tr></table>"
    }
    function removeFromStatus(object)
    {
        var id;
        if (object.type == "Webpage") {
            addedURLs = removeFromArray(addedURLs, object.value);
            id = '#post_media_single_' + formatToID(object.value);
            for (var i = 0; i < post_media_added_files.length; i++) {
                if (object.value == post_media_added_files[i].path) {
                    //console.log('Website detected in Post Attachements: '+object.value + " to " + post_media_added_files[i].path + " now REMOVED!");
                    post_media_added_files.splice(i, 1);
                } else {
                    //console.log('No Website detected in Post Attachements: Submitted value of '+object.value + " to " + post_media_added_files[i].path);
                }
            }
        } else if (object.type == "File") {
            id = '#post_media_single_' + object.value;
            for (var i = 0; i < post_media_added_files.length; i++) {
                if (object.value == post_media_added_files[i]) {
                    //console.log('File detected in Post Attachements: '+object.value + " to " + post_media_added_files[i] + " now REMOVED!");
                    post_media_added_files.splice(i, 1);
                } else {
                    //console.log('No File detected in Post Attachements: Comparing submitted value of '+object.value + " to " + post_media_added_files[i] + " of " + post_media_added_files);
                }
            }

        }
        $(id).remove();

        if (post_media_added_files.length == 0)
        {
            $('#status_text').attr('placeholder', 'Update Status or Share Files...');
            $('.post_media_wrapper_background').show();
        }
        $('#status_text').focus();
        resizeScrollers();
        //console.log("Resulting media list: " + post_media_added_files + "/n Added URLS = " + addedURLs);
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
    function resizeToMax(element) {
        if ($(element).width() / getViewPortWidth() > $(element).height() / getViewPortHeight()) {
            $(element).css('height', "auto");
            $(element).css('width', getViewPortWidth() - 500 + "px");
        } else {
            $(element).css('width', "auto");
            $(element).css('height', getViewPortHeight() - 100 + "px");
        }
    }
    function initiateTheater(activity_id, file_id, properties)
    {
        fileView(file_id);
        
        $('.theater-picture').remove();
        $(".background-overlay").remove();
        
        var background = $("<div hidden onclick='hideTheater();' class='background-overlay'></div>").show();
        var close_theater = $("<div onclick='hideTheater();' class='close-theater'></div>");
        var theater_picture_container = $("<div id='theater-picture-container' class='theater-picture-container'></div>");
        var theater_info_container = $("<div id='theater-info-container' class='theater-info-container'></div>");
        var theater_info_padding = $("<div class='theater-info-padding'></div>");
            theater_info_container.append(theater_info_padding);
        var theater_picture = $("<div id='theater-picture' class='theater-picture'></div>");
            theater_picture.append(close_theater);
        $('body').append(background);
        $('body').append(theater_picture);
        $("body").css("overflow", "hidden");
        
        var string = "<table style='border-spacing: 0px;' cellspacing='0' cellpadding='0'><tr><td id='theater_image'></td><td style='width:360px;' id='theater_info'></td></tr></table>";
        theater_picture.append(string);

        $('#theater_info').append(theater_info_container);
        
       theater_picture_container.append("<table id='load_popup' style='height:100%;width:100%;padding:200px;'><tr style='vertical-align:middle;'><td style='text-align:center;'><img src='Images/ajax-loader.gif'></img></td></tr></table>");
       $('#theater_image').append(theater_picture_container);
       
       adjustTheater();
            
        $.post('Scripts/files.class.php', {action: "preview", file_id: file_id, activity_id: activity_id}, function(response) {
            //console.log(response);
            response = $.parseJSON(response);
            theater_info_padding.append(response.post);
//            if(activity_id == null) {
//                theater_info_padding.remove('.switch_container');
//            }
            
            if(response.file.type == "Image") {
                $('<img/>').attr('src', response.file.path).load(function() {
                    $(this).remove();
                    $('#load_popup').remove();
                    adjustTheater();
                    setTimeout(adjustTheater, 1000);
                });
                
                theater_picture_container.css('background-image', "url('" + response.file.path + "')");
                
                var image = $('<img class="image" />').attr('src', response.file.path);
                image.css('visibility', "hidden");
            }
            else {
                var image = "<?php echo $system->videoPlayer('preview_video_player', NULL, "file_video_element", "height:500px;width:700px", "home_video_", TRUE); ?>";
                image = image.replace(/:::webm_path:::/g, response.file.webm_path);
                image = image.replace(/:::mp4_path:::/g, response.file.mp4_path);
                image = image.replace(/:::flv_path:::/g, response.file.flv_path);
                image = image.replace(/:::ogg_path:::/g, response.file.ogg_path);
                image = image.replace(/:::name:::/g, response.file);
                image = image.replace(/:::vid:::/g, response.file.id);
                image = image.replace(/:::thumb:::/g, response.file.thumbnail);
                image = $(image);
            }
            theater_picture_container.append(image);

            if(response.file.type == "Video") {
                //videojs('home_video_preview_video_player', {}, function() {
                    // this.on('play', function() {
                    //     videoPlay(video_id);
                    // });
                    // this.on('pause', function() {
                    //     videoPause(video_id);
                    // });
                    // this.on('ended', function() {
                    //     videoEnded(video_id);
                    // });
                //});
                $('#home_video_preview_video_player').on('canplay', function() {
                    $('#load_popup').remove();
                    $(this).get(0).play();
                    adjustTheater();
                    setTimeout(adjustTheater, 1000);
                });
                //setTimeout(function(){document.getElementById('home_video_preview_video_player').play();}, 1500);
            }
            theater_info_container.mCustomScrollbar(SCROLL_OPTIONS);
            
//            if(properties.type == "Post") {
//                theater_info_container.find('.switch_container').find('.switch_option');
//            }
            adjustSwitches();
        });
    }

    function adjustTheater()
    {
        $('.theater-picture').css('background-color', 'transparent');
        var theater = $('.theater-picture');
        resizeToMax($('.theater-picture-container').children(':first').not('div'));
        theater.height($('.theater-picture-container').height());
        theater.css('top', getViewPortHeight() / 2 - $(theater).height() / 2);

        var spare_width = getViewPortWidth() - theater.width();
        theater.children('table').css('height', theater.height() + 'px');
        theater.css('left', spare_width / 2 + "px");

        $('#theater-info-container').height($('.theater-picture-container').height());
        $('#theater-info-container').mCustomScrollbar("update");
    }

    function refreshInfo()
    {
        alert('refrsh');
    }

    function hideTheater()
    {
        $('.background-overlay').hide();
        $('.theater-picture').hide();
        removeTheater();
        $("body").css("overflow", "auto");
    }

    function removeTheater()
    {
        $('.background-overlay').remove();
        $('.theater-picture').remove();
    }


    function joinGroup(group_id, invite_id)
    {
        $.post("Scripts/group_actions.php", {action: "join", group_id: group_id, invite_id: invite_id}, function(response)
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
        $.post("Scripts/group_actions.php", {action: "reject", group_id: group_id, invite_id: invite_id}, function(response)
        {
            $('#reject_button_' + invite_id).slideUp();
            $('#join_button_' + invite_id).slideUp();
            //alert(response);
        });
    }



    function autoresize(textarea)
    {
    }

    function autoresizecomment(textarea)
    {
        textarea.style.height = '0px';
        textarea.style.height = (textarea.scrollHeight + 5) + 'px';
    }

    function playSound(element)
    {
        var name = $(element).prev().get(0).play();
    }

    function pauseSound(element)
    {
        var name = $(element).prev().get(0).pause();
    }


    function get_folder_contents(element, action, parent_folder, actions)
    {
        $('#loading_icon').show();
        if (action == "remove")
        {
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
        else
        {
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
            refreshElement('#file_container', 'files', 'pd=' + encrypted_folder, '#main_file');
        });
    }
    function audioPlay(id)
    {
        fileView(id);
        var src = $('#image_' + id).css('background-image');
        if (src.indexOf(AUDIO_PLAY_THUMB) >= 0)
        {
            startAudioInfo(id);
            $("#audio_info_" + id).slideDown();
            $('#audio_' + id).get(0).play();
            $('#image_' + id).css('background-image', "url('" + AUDIO_PAUSE_THUMB + "')");
            $('#audio_play_icon_' + id).slideDown();
            $('#audio_play_icon_seperator_' + id).slideDown();
        }
        else
        {
            $('#audio_' + id).get(0).pause();
            $('#image_' + id).css('background-image', "url('" + AUDIO_PLAY_THUMB + "')");
            $('#audio_play_icon_' + id).hide();
            $('#audio_play_icon_seperator_' + id).hide();
        }
        //console.log($('#audio_play_icon_' + id).length + "/File ID = " + id);
    }
    function startAudioInfo(id)
    {
        $("#audio_" + id).bind('progress', function() {
            var track_length = $("#audio_" + id).get(0).duration;
            var secs = $("#audio_" + id).get(0).buffered.end(0);
            var progress = (secs / track_length) * 100;
            $("#audio_buffered_" + id).css('width', progress + "%");
        });
        $("#audio_" + id).bind('timeupdate', function() {
            var track_length = $("#audio_" + id).get(0).duration;
            var secs = $("#audio_" + id).get(0).currentTime;
            var progress = (secs / track_length) * 100;
            $("#audio_progress_" + id).css('width', progress + "%");
            var track_length = $("#audio_" + id).get(0).duration;
            var secs = $("#audio_" + id).get(0).buffered.end(0);
            var progress = (secs / track_length) * 100;
            $("#audio_buffered_" + id).css('width', progress + "%");

            var minutes = Math.floor(track_length / 60);
            var seconds = Math.floor(track_length - minutes * 60);

            var done_secs = $("#audio_" + id).get(0).currentTime;
            var done_minutes = Math.floor(done_secs / 60);
            var done_remaining_secons = Math.floor(done_secs - done_minutes * 60);
            $("#audio_time_" + id).html(done_minutes + ":" + pad(done_remaining_secons) + " - " + minutes + ":" + seconds);
        });

        $("#audio_" + id).bind('canplaythrough', function() {
            $('#audio_buffered_' + id).css('background-color', 'grey');
        });
        $('#audio_' + id).bind('ended', function() {
            $("#audio_" + id).get(0).currentTime = 0;
            $('#image_' + id).css('background-image', "url('../Images/Icons/Icon_Pacs/glyph-icons/glyph-icons/PNG/Play.png')");
        });
        $("#audio_progress_container_" + id).click(function(e)
        {
            var x = $(this).offset().left;
            var width_click = e.pageX - x;
            var width = $(this).width();
            var percent_width = (width_click / width) * 100;
            $("#audio_progress_" + id).css('width', percent_width + "%");

            var secs = $("#audio_" + id).get(0).duration;
            var new_secs = secs * (percent_width / 100);

            $("#audio_" + id).get(0).currentTime = new_secs;
        });
    }

    function removeAudio(id)
    {
        $('#audio_container_' + id).remove();
    }
</script>
<script>

    function videoPlay(id) {
        var file_id = id.replace(/[A-Za-z_$-]/g, "");
        fileView(file_id);
        if ($("#" + id).parents('#file_container').length !== 0) {
            id = file_id;
            $('#audio_play_icon_' + id).css('visibility', 'visible');
            $('#audio_play_icon_' + id).animate({opacity: "1"}, 200);
        } else if ($("#" + id).parents('.files_recently_shared').length !== 0) {
            $('.files_recently_shared').find(".files_feed_active").not(":has(#" + id + ")").removeClass("files_feed_active").find('video').each(function() {
                if (videojs("#" + $(this).attr('id')).paused() === false) {
                    videojs("#" + $(this).attr('id')).player().pause();
                }
            });
            $('.files_recently_shared_container').mCustomScrollbar("scrollTo", "#" + id, {
                scrollInertia: 600,
                scrollOffset: "200px"
            });

            $("#" + id).parents('.files_feed_item').not(".files_feed_active").addClass("files_feed_active");

        }
        else {
            initiateTheater(null, file_id);
            videojs("#" + id).player().pause();
            videojs("#" + id).player().currentTime(0); // 2 minutes into the video            
            videojs("#" + id).player().posterImage.el.style.display = 'block';
            videojs("#" + id).player().bigPlayButton.show();
        }

    }
</script>
<script>
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
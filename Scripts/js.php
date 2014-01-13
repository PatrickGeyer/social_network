<?php
include("lock.php");
$system->getGlobalMeta();
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
<script>
    _V_.options.flash.swf = "Scripts/external/video-js/video-js.swf";
    
    window.onerror = function(message, url, lineNumber) {  
        if(message.indexOf("offsetTop") !== -1)
        {
            return true;
        }
    };
</script>

<script type="text/javascript">
    //var audioPlayer = <?php ?>;
    //var videoPlayer = <?php ?>;
    var WORD_THUMB = '<?php echo System::WORD_THUMB; ?>';
    var PDF_THUMB = '<?php echo System::PDF_THUMB; ?>';
    var AUDIO_THUMB = '<?php echo System::WORD_THUMB; ?>';
    var VIDEO_THUMB = '<?php echo System::WORD_THUMB; ?>';
    
    
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
                            content={
                                type: "html",
                                content: "Are you sure you want to update your location?",
                            },
                            buttons=[{
                                type: "primary",
                                text: "Yes",
                                onclick: function(){updateLocation(" + marker.position.lat() + ", " + marker.position.lng() + ");},
                            }],
                            properties={
                                modal: false,
                                title:'Location',
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

    // SEARCH
    $(function() {
        $('.name_selector').hover(function() {
            $('.match').css('background-color', 'transparent');
        }, function()
        {
            //mouseleave
        });
        $('.match').hover(function() {
            $('.match').css('background-color', '#FAFAFA');
        });

        $('.scroll_thin').mCustomScrollbar({
                    scrollButtons:{
                        enable:false
                    },
                    advanced:{
                        updateOnContentResize: true,
                        updateOnBrowserResize:true,
                    },
                    scrollInertia:100,
                    theme:'dark',
                    autoHideScrollbar: false,
                    mouseWheelPixels: 20 
                    
                });
        $('.scroll_thin_left').mCustomScrollbar({
                    scrollButtons:{
                        enable:false
                    },
                    advanced:{
                        updateOnContentResize: true,
                        autoScrollOnFocus: true,
                    },
                    scrollInertia:10,
                    theme:'dark-thin',
                    autoHideScrollbar : true
                });

        $('.scroll_thin_horizontal').mCustomScrollbar({
                    scrollButtons:{
                        enable:false
                    },
                    advanced:{
                        updateOnContentResize: true,
                        autoScrollOnFocus: true,
                    },
                    scrollInertia:10,
                    autoHideScrollbar : true,
                    horizontalScroll:true,
                });
       $('.search_results').mCustomScrollbar({
                    scrollButtons:{
                        enable:false
                    },
                    advanced:{
                        updateOnContentResize: true,
                        updateOnBrowserResize:true,
                    },
                    scrollInertia:100,
                    theme:'dark',
                    autoHideScrollbar: false,
                    mouseWheelPixels: 20 
                    
                });
//                 $( ".search_option file, #post_media_wrapper" ).sortable({
//      connectWith: ".connectedSortable"
//    }).disableSelection();
        //$(".scroll_thin").niceScroll({ autohidemode: true,horizrailenabled:false });
        //$(".scroll_thin_horizontal").niceScroll({ autohidemode: true });
        refreshVideoJs();
        
    });
    //END SEARCH
    var alignment;
    var needs_loading = true;
    setInterval(function() {
        $.post('Scripts/user.class.php', {action: 'setOnline'}, function() {

        });
    }, 10000);
    function getViewPortHeight()
    {
        var viewportwidth;
        var viewportheight;

        //Standards compliant browsers (mozilla/netscape/opera/IE7)
        if (typeof window.innerWidth != 'undefined')
        {
            viewportwidth = window.innerWidth,
                    viewportheight = window.innerHeight
        }

        // IE6
        else if (typeof document.documentElement != 'undefined'
                && typeof document.documentElement.clientWidth !=
                'undefined' && document.documentElement.clientWidth != 0)
        {
            viewportwidth = document.documentElement.clientWidth,
                    viewportheight = document.documentElement.clientHeight
        }

        //Older IE
        else
        {
            viewportwidth = document.getElementsByTagName('body')[0].clientWidth,
                    viewportheight = document.getElementsByTagName('body')[0].clientHeight
        }

        return viewportheight;
    }
    
    function addReceiver(array, receiver_id, community_id, group_id, callback) {
        var to_push = {
            receiver_id : receiver_id,
            community_id: community_id, 
            group_id: group_id
        };
        array.push(to_push);
        callback();
        console.log(array);
        return array;
    }
    
    function removeReceiver(array, value, callback) {
        var index = receivers.indexOf(receiver_id);
                if (index > -1)
                {
                    receivers.splice(index, 1);
                }
                $('.message_added_receiver_' + receiver_id).remove();
                alignDialog();
    }
    
    function search(text, mode, element, callback) {
        if(text == "") {
            $(element).hide();
        } else {
            var loader = $("<img src='Images/ajax-loader.gif'></img>");
            $(element).prepend(loader);
            $(element).show();
        }
        $.post("Scripts/searchbar.php", {search: mode, input_text: text}, function(response) {
            $(element).find('.search_slider').slideUp("fast", function(){$(this).remove();});
            var slider = $("<div class='search_slider'></div>").hide();
            slider.append(response);
            $(element).append(slider);
            loader.remove();
            var scrollElement = document.querySelector(element);
            slider.slideDown(function(){
                if( scrollElement.offsetHeight < scrollElement.scrollHeight || scrollElement.offsetWidth < scrollElement.scrollWidth){
                    $(element).mCustomScrollbar();
                    $(element).find('.mCustomScrollBox ').height('');
                    $(element).mCustomScrollbar("update");
                    $(element).find('.mCS_container').css('top', '0px');
                }
                callback();
            });
        });
        $(element).off('click');
        $(element).on('click', function(e){
            e.preventDefault();
            e.stopPropagation();
        });
    }
    
    function getViewPortWidth()
    {
        var viewportwidth;
        var viewportheight;

        //Standards compliant browsers (mozilla/netscape/opera/IE7)
        if (typeof window.innerWidth != 'undefined')
        {
            viewportwidth = window.innerWidth,
                    viewportheight = window.innerHeight
        }

        // IE6
        else if (typeof document.documentElement != 'undefined'
                && typeof document.documentElement.clientWidth !=
                'undefined' && document.documentElement.clientWidth != 0)
        {
            viewportwidth = document.documentElement.clientWidth,
                    viewportheight = document.documentElement.clientHeight
        }

        //Older IE
        else
        {
            viewportwidth = document.getElementsByTagName('body')[0].clientWidth,
                    viewportheight = document.getElementsByTagName('body')[0].clientHeight
        }

        return viewportwidth;
    }

// FILES

    var parent_folder = <?php echo (isset($parent_folder) ? $parent_folder : "1" ); ?>;

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

    var q = 0;
    function uploadFile(type, name, onComplete)
    {
        if (type == "folder")
        {
            var files;
            files = $(name)[0].files;
        }
        else if (type == "file")
        {
            var files = $(name)[0].files;
            var parent = $(name).parent();
            var html = $(name).html();
            $(name).remove();
            $(parent).append(html).hide();
        }
        var length = files.length;
        for (var count = 0; count < length; count++)
        {
            q++;
            $("#progress_bar_holder").empty();
            $("#progress_bar_holder").append("<div id='progressContainer' class='progress_container'><div id='progressBar' class='progress_bar'></div></div>");
            $("#progress_bar_holder").append("<span id='status'></span>");
            var file = files[count];
            var formdata = new FormData();
            formdata.append("file", file);
            formdata.append("parent_folder", parent_folder);
            var xhr = new XMLHttpRequest();
            xhr.upload.onprogress = function(event) {
                progressHandler(event, count);
            };
            xhr.onload = function() {
                completeHandler(this, count - 1, onComplete, name);
            };
            xhr.addEventListener("error", errorHandler, false);
            xhr.addEventListener("abort", abortHandler, false);
            xhr.open("post", "Scripts/upload_file.php");
            xhr.send(formdata);
        }
    }
    function progressHandler(event, id)
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
        if(onComplete == "addToStatus")
        {
            response = $.parseJSON(event.responseText);
            var object = new Object();
            object.path = response.filepath;
            object.name = response.filename;
            object.file_id = response.file_id;
            addToStatus("Image", object);
        }
        else
        {
            var response = $.parseJSON(event.responseText);
            $.post('Scripts/files.class.php', {file_info:response, action:"convert"}, function(){
                //console.log('posted file conversion data');
                removeDialog();
            });
            refreshFileContainer(encrypted_folder);
        }
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
    function showUserPreview(element, user_id) {
        if (element == "force") {
            $('.user_preview_info').show();
        }
        else
        {
            $('.user_preview_info').not(element.children('.user_preview_info').first()).remove();
            if (element.children('.user_preview_info').length == 0)
            {
                createUserPreview(element, user_id);
            }
            else
            {
                //console.log("Preview is already in place.");
            }
        }
    }
    function createUserPreview(element, user_id)
    {
        var bg = "<div id='user_preview_initial_loader' style='width:100%;height:100px;background-image:url(Images/ajax-loader.gif);background-position:center;background-repeat:no-repeat;'></div>";
        var cont = $('<div style="display:none;" class="user_preview_info">' + bg + '</div>');
        element.append(cont);
        var createTimeout = setTimeout(function() {
            if ($('*[user_id="' + user_id + '"]:hover').length > 0)
            {
                $.post('Scripts/user.class.php', {action: "get_preview_info", id: user_id}, function(response) {
                    fillUserPreview(response);
                });
                cont.fadeIn();
                alignUserPreview(element);

                //console.log("User preview was successfully created!");
            }
            else
            {
                //$('.user_preview_info').remove();
                //console.log("User preview was NOT created because the hover status disappeared.");
            }
        }, 500);
    }
    function alignUserPreview(element)
    {
        $(document).off('scroll');
        $(document).on('scroll', function() {
            alignUserPreview(element);
        });
        //console.log('align');
        var left = element.offset().left;
        var width = element.width();
        var left_total = left + width + 20;
        if (left_total > getViewPortWidth() / 2) {
            left_total = left - 40 - $('.user_preview_info').width();
            alignment = "left";
        } else {
            alignment = "right";
        }
        var arrow;
        if (alignment == "left")
        {
            arrow = 'user_preview_arrow-right';
        } else if (alignment == "top") {

        } else if (alignment == "bottom") {
        }
        else
        {
            arrow = 'user_preview_arrow-left';
        }

        $('.user_preview_info').append('<div class="' + arrow + '-border"></div>');
        $('.user_preview_info').append('<div class="' + arrow + '"></div>');

        var top = element.offset().top;
        var element_height = element.outerHeight(true);
        var scrolled_height = $(document).scrollTop();
        var top_total = top - scrolled_height - element_height / 2;
        var arrow_height = $('.' + arrow + "-border").outerHeight(true) / 2 - 2;
        //top_total = top_total + arrow_height;

        $('.user_preview_info').css('left', left_total + "px");
        $('.user_preview_info').css('top', top_total + "px");
    }
    function fillUserPreview(response)
    {
        response = $.parseJSON(response);
        var user_string;
        user_string = ("<table style='height:100%;width:100%;' cellspacing='0'><tr><td rowspan='3' style='width:80px;'><div style='width:70px;height:70px;background-image:url(" +
                response[2] + ");background-size:cover;background-repeat:no-repeat;'></div></td>");
        user_string += ("<td><a style='padding:0px;' href='user?id=" + response[0] +
                "'><span class='user_preview_name'>" + response[1] + "</span></a></td></tr>");

        user_string += "<tr><td><a style='padding:0px;display:inline;' href='community?id=" + response[6] +
                "'><span class='user_preview_community'>" + response[4] + "</span></a><span class='user_preview_position'> &bull; " + response[5] + "</span></td></tr>";
        user_string += "<tr><td><span class='user_preview_about'>" + response[3] + "</span></td></tr>";

        user_string += "<tr><td></td><td><div class='user_preview_buttons'>" +
                "<button class='pure-button-primary smallest'>Chat</button><button class='pure-button-success smallest'>Message</button></div></td></tr>";

        user_string += "</table>";

        $('.user_preview_info').find('*').not('.user_preview_arrow-right, .user_preview_arrow-right-border, .user_preview_arrow-left, .user_preview_arrow-left-border').remove();
        $('.user_preview_info').append(user_string);
    }

    function removeUserPreview(mode, event)
    {
        if ($('.user_preview_info').length > 0)
        {
            if (mode == "check mouse")
            {
                if (calculateDistance($('.user_preview_info'), event.pageX, event.pageY) > 300)
                {
                    $('.user_preview_info').fadeOut('fast', function() {
                        $(this).remove();
                    });
                    //console.log('Removed Preview in Mode: ' + mode + ", successfully");
                }
            }
            else
            {
                $('.user_preview_info').remove();
            }
        }
    }

    function calculateDistance(elem, mouseX, mouseY)
    {
        return Math.floor(Math.sqrt(Math.pow(mouseX - (elem.offset().left + (elem.width() / 2)), 2) + Math.pow(mouseY - (elem.offset().top + (elem.height() / 2)), 2)));
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


        $(document).on('click', ".default_dropdown_selector", function(event)
        {
            event.stopPropagation();
            $(this).toggleClass('default_dropdown_active');
            var wrapper = '#' + $(this).attr('wrapper_id');
            $(wrapper).toggle();
        });
        $(document).on('click', "html", function(event)
        {
            $('.default_dropdown_selector').removeClass('default_dropdown_active');
            $('.default_dropdown_wrapper').hide();
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
        
        $(document).on('click', '.search_box', function(e){
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
    }, 200);
    function alignNavFriend()
    {
        var container_left = $('.container_headerbar').offset().left;
        container_left = container_left - $('.left_bar_container').outerWidth();
        
        var nav_height = $('.navigation').outerHeight(true);

        //$('#logo').css('left', container_left);
        $('.left_bar_container').css('left', container_left-1);
        $('.messagecomplete').css('left', container_left-1);
        $('#friends_container').css('padding-top', 22);
        $('.messagecomplete').css('padding-top', nav_height + 30);

        //var top_height = $('.navigation').position().top + $('.navigation').height();

        //$('#friends_container').css('top', top_height + 22);

//        $('.messagecomplete').css('top', top_height + 22);
//        $('.messagecomplete').css('left', container_left - 2);
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
            dialog(
                content=
                {
                    type:"html", 
                    content:"Your post is being sent... Please wait."
                },
                buttons=
                [{
                    type:"success", 
                    text:"OK",
                    onclick: function(){alert('the_function');}
                }],
                properties=
                {
                    modal:false,
                    loading:true,
                    title:"Posting..."
                }
            );
            $.post("Scripts/update_status.php", {status_text: text, group_id: share_group_id, post_media_added_files : post_media_added_files}, function(data)
            {
                if (data == "200")
                {
                    getHomeContent(share_group_id, function(data){
                        $('.home_feed_container').prepend(data);
                        removeDialog();
                        refreshVideoJs();
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
        for(var i = 0; i < buttons.length; i++ ) {
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
            $('body').append("<div onclick='removeDialog()' style='opacity:0.1' class='background-overlay'></div>");
        }

        if(properties.loading == true)
        {
            dialogLoad();
        }
        alignDialog();
        var real_height = dialog_container.height();
        dialog_container.css({height: "0px"});
        dialog_container.animate({ minHeight: real_height + "px", opacity: 1}, 'fast', function(){dialog_container.css({height: "auto"});});
        content_container.mCustomScrollbar({
                    scrollInertia:10,
                    autoHideScrollbar : true,
                });
        content_container.mCustomScrollbar("update");
        setTimeout(function(){content_container.mCustomScrollbar("update"); }, 200);
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
        $('.background-overlay').remove();

        $('.dialog_container').css('min-height', '0px');
        $('.dialog_container').animate({ height: 0, opacity: 0 }, 'fast', function(){
            $(this).remove();
        });
    }
// #POPUP
// #HOME
    $(function() {
        $(document).on('click', '.post_media_single_close_file', function(){
            removeFromStatus(object={type:"File", value: $(this).attr('post_file_id')});
        });
        $(document).on('click', '.post_media_single_close_webpage', function(){
            removeFromStatus(object={type:"Webpage", value: $(this).attr('post_file_id')});
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
    function submitcomment(comment_text, post_id)
    {
        if (comment_text == "")
        {
            comment_text = $('div[actual_id="comment_' + post_id + '"]').html();
        }
        $.post("Scripts/extends.class.php", {comment_text: comment_text, post_id: post_id, action: 'submitComment'}, function(data)
        {
            $('#comment_' + post_id).html("").blur();
            $('div[actual_id="comment_' + post_id + '"]').blur().html('');
            refreshContent(post_id);
        });
    }

    function refreshContent(id)
    {
        var number_of_updates = 10;
        var updates_done = 0;
        refreshPure(id); //Refresh immediately after comment
        var refresh_interval = setInterval(function() {
            refreshPure(id)
        }, 10000);
        if (++updates_done >= number_of_updates)
        {
            window.clearInterval(refresh_interval);
        }
    }

    function refreshPure(id)
    {
        if ($("*:focus").is("textarea, input") || $('.inputtext').is(":focus"))
        {
            console.log('Comment submit cancelled: Textarea in focus.');
        }
        else
        {
            $.post('Scripts/home.class.php', {activity_id: id}, function(response)
            {
                var activity_id = $("#theater-info-container").attr("activity_id");
                if (typeof activity_id !== "undefined")
                {
                    $('.theater-info-container').children('.comments').find('.comment_box').children('div.single_comment_container, hr.post_comment_seperator').remove();
                    $('.theater-info-container').children('.comments').find('.comment_box').prepend(response);
                }
                $('#comment_div_' + id).children('div.single_comment_container, hr.post_comment_seperator').remove();
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
                $('#' + id + 'likes').text(data);
            }
            else
            {
                $('#' + id + 'dislikes').text(data);
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
    var e=/^\b((http|ftp):\/)?\/?([^:\/\s]+)((\/\w+)*\/)([\w\-\.]+\.[^#?\s]+)(#[\w\-]+)?\b/;

        if (data.match(e)) {
            return  {url: RegExp['$&'],
                    protocol: RegExp.$2,
                    host:RegExp.$3,
                    path:RegExp.$4,
                    file:RegExp.$6,
                    hash:RegExp.$7};
        }
        else {
            return  {url:"", protocol:"",host:"",path:"",file:"",hash:""};
        }
    }

    function parseUrl2(data) {
        var e=/((((https?|ftp|file):\/\/)|www.)[-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|](\s))/ig;

        if (data.match(e)) {
            return  {url: RegExp['$&'],
                    protocol: RegExp.$2,
                    host:RegExp.$3,
                    path:RegExp.$4,
                    file:RegExp.$6,
                    hash:RegExp.$7};
        }
        else {
            return false;
        }
    }
    var addedURLs = new Array();
    var typedURLs = new Array();
    function checkLink(element){
        var regularExpression = parseUrl2(element.val());
        if(regularExpression){
            var new_url = regularExpression.url.trim();
            
            if(typedURLs.indexOf(new_url) == -1)
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
    $(function(){
        $('#status_text').on('input', function(){
            var status_text = $(this).val();
            var link = checkLink($(this));
            var time = new Date().getTime();
            if(link != false){
                post_media_load();
                $.post('Scripts/system.class.php', {action:"get_page_preview", url: link}, function(response){
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
                    removeFromStatus(object={type: "Webpage", value: addedURLs[i]});
                } 
            }
        });
    });

    function post_media_load(action) {
        if(action != "stop") {
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
            
        } else if(type == "Webpage") {
            post_media_classes += " post_media_full";
            post_media_style += "height:auto;";
            additional_close += " post_media_single_close_webpage";
            extra_params = " post_file_id='" + object.path + "' ";
            text_to_append += "><table style='height:100%;'><tr><td rowspan='3'>" + 
                    "<div class='post_media_webpage_favicon' style='background-image:url(&quot;" + object.info.favicon + "&quot;);'></div></td>"+
                    "<td><div class='ellipsis_overflow' style='position:relative;margin-right:30px;'>"+
                    "<a class='user_preview_name' target='_blank' href='" + object.path + "'><span style='font-size:13px;'>" + object.info.title + "</span></a></div></td></tr>"+
                    "<tr><td><span style='font-size:12px;' class='user_preview_community'>" + object.info.description + "</span></td></tr></table>";
        } else if(type == "Folder") {
            additional_close += " post_media_single_close_file";
            text_to_append += "><div class='post_media_folder_image'></div>"+
                    "<span style='font-size:13px;'></span>";
        } else if(type == "WORD Document"){ 
            text_to_append += documentStatus(object.path, object.name, object.description, WORD_THUMB);
            
        } else if(type == "PDF Document") {
            text_to_append += documentStatus(object.path, object.name, object.description, PDF_THUMB);
        } else {
            alert(type);
            text_to_append += "Type undetected";
        }
        if(type == "WORD Document" || type == "PDF Document" ){ 
            post_media_classes += " post_media_double";
            post_media_style += "height:auto;";
            additional_close += " post_media_single_close";
            extra_params = " post_file_id='" + object.path + "' ";
        }
        var index;
        for(var i=0;i<post_media_added_files.length;i++) 
        {
           if (post_media_added_files[i].file_id == object.file_id || post_media_added_files[i] == object.file_id)
           { 
               index = "found";
               dialog(
                content={
                   type:'html',
                   content:"Sorry, but you have already added this file to your post. Please choose another instead!"
                },
                buttons=[{
                    type:"error",
                    text:"OK",
                    onclick:function(){removeDialog();}
                }],
                properties={
                    modal:false,
                    title: "Whoops!"
                });
           }
        }
        var post_media = '<div class="post_media_single ' + 
            post_media_classes + '" ' +  post_media_style + "' " + ' id="post_media_single_' + object.file_id + '"';
        if(index != "found")
        {
            if(type == "Webpage") {
                post_media_added_files.push(object);
            } else {
                post_media_added_files.push(object.file_id);
            }
            
            $('.post_media_wrapper').append(post_media + text_to_append + "<div " + extra_params + " class='post_media_single_close" + 
                    additional_close + "'></div><div class='post_media_single_close_background'></div></div>");
        }
        if(type == "Video") {
           // console.log('Attempting to load: file_video_' + object.file_id);
//           $('#file_video_'+object.file_id).append('<source src="'+object.info.mp4_path+'" type="video/mp4" />');
//           $('#file_video_'+object.file_id).append('<source src="'+object.info.flv_path+'" type="video/x-flv" />');
//           $('#file_video_'+object.file_id).append('<source src="'+object.info.ogg_path+'" type="application/ogg" />');
             videojs('home_video_'+object.file_id, {}, function(){
//                    this.src([
//                       // { type: "video/mp4", src: object.info.mp4_path },
//                        //{ type: "video/flv", src: object.info.flv_path },
//                        //{ type: "video/ogg", src: object.info.ogg_path },
//                        { type: "video/x-msvideo", src: object.path },
//                      ]);
//                    //this.play(); //autostart it
                });
        }
        $('#status_text').focus();
        $('#file_share').mCustomScrollbar("update");
    }
    function documentStatus(path, name, description, image) {
        return "><table style='margin:10px;height:100%;'><tr><td rowspan='3'>" + 
                    "<div style='margin-right:10px;height:63px;width:64px;background-size:contain;background-image:url(&quot;" + image + "&quot;);'></div></td>"+
                    "<td><div class='ellipsis_overflow' style='position:relative;margin-right:30px;'>"+
                    "<a class='user_preview_name' target='_blank' href=''><span style='font-size:13px;'>" + name + "</span></a></div></td></tr>"+
                    "<tr><td><span style='font-size:12px;' class='user_preview_community'>" + description + "</span></td></tr></table>"
    }
    function removeFromStatus(object)
    {
        var id;
        if(object.type == "Webpage") {
            addedURLs = removeFromArray(addedURLs, object.value);
            id = '#post_media_single_' + formatToID(object.value);
            for (var i = 0; i < post_media_added_files.length; i++) {
                if(object.value == post_media_added_files[i].path) {
                    //console.log('Website detected in Post Attachements: '+object.value + " to " + post_media_added_files[i].path + " now REMOVED!");
                    post_media_added_files.splice(i, 1);
                } else {
                    //console.log('No Website detected in Post Attachements: Submitted value of '+object.value + " to " + post_media_added_files[i].path);
                }
            }
        } else if(object.type == "File") {
            id = '#post_media_single_' + object.value;
            for (var i = 0; i < post_media_added_files.length; i++) {
                if(object.value == post_media_added_files[i]) {
                    //console.log('File detected in Post Attachements: '+object.value + " to " + post_media_added_files[i] + " now REMOVED!");
                    post_media_added_files.splice(i, 1);
                } else {
                    //console.log('No File detected in Post Attachements: Comparing submitted value of '+object.value + " to " + post_media_added_files[i] + " of " + post_media_added_files);
                }
            }
            
        }
        $(id).remove();

        if(post_media_added_files.length == 0)
        {
            $('.post_media_wrapper_background').show();
        }
        $('#status_text').focus();
        resizeScrollers();
        //console.log("Resulting media list: " + post_media_added_files + "/n Added URLS = " + addedURLs);
    }
    function removeFromArray(array, match) {
        for(var i=0;i<array.length;i++) 
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

    function initiateTheater(src, id, no_text, file_id)
    {
        var background = $("<div hidden onclick='hideTheater();' class='background-overlay'></div>");
        var close_theater = $("<div onclick='hideTheater();' class='close-theater'></div>");
        $('body').append(background);
        fileView(file_id);
        //console.log(src + " --- " + id +" --- "+ no_text);
        var checker = $('.theater-picture-container');
        if (checker.length != 0)
        {
            $('.theater-picture').remove();
            //$(".background-overlay").remove();
        }
        var theater_picture = $("<div id='theater-picture' class='theater-picture'></div>");
        $('body').append(theater_picture);
        theater_picture.append(close_theater);
        
        var theater_picture_container = $("<div id='theater-picture-container' " + 
                "style='background-image: url(&apos;" + src + "&apos;);' class='theater-picture-container'></div>")
        theater_picture.append(theater_picture_container);
        
        if (id != "no_text")
        {
            var theater_info_container = $("<div id='theater-info-container' class='theater-info-container'></div>");
            theater_picture.append(theater_info_container);

            var info_html = $('#single_post_' + id).clone();
            info_html = info_html.find("*").each(function()
            {
                var previous_id = $(this).attr("id");
                if (previous_id != "")
                {
                    $(this).attr("id", Math.random());
                    $(this).attr("actual_id", previous_id);
                    $(this).attr("activity_id", id);
                }
            });
            $("#theater-info-container").attr("activity_id", id);
            var info_container = $('.theater-info-container');

            var user_image = info_html.find('.imagewrap');
            var post = info_html.find('.singleupdate');
            post.find('.updatepic').remove();
            post.css('width', '100%');
            post.find('.update').css('width', '100%');
            var comments = info_html.find('.comments');
            comments.find('.comment_box').css('margin', '0');
            comments.find('.comment_box').css('margin-top', '50');

            info_container.append(post);
            info_container.append(comments);

            var small_image = $(' .theater-info-container').find("img");
            small_image.css('max-height', '100px');
            small_image.css('width', 'auto');
        }

        $('.background-overlay').show();
        theater_picture.show();
        $("body").css("overflow", "hidden");
        adjustTheater(no_text, src);
    }
</script>
<script>
    function adjustTheater(no_text, src)
    {
        var theater = $('.theater-picture');
        theater.css('margin-top', getViewPortHeight() / 2 - $(theater).height() / 2 - 20);
        if (no_text == 'no_text')
        {
            $('#theater-picture-container').width('100%');
            $('#theater-info-container').hide();
        }
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
        $.post('Scripts/files.class.php', {action: "delete", id: id}, function(response)
        {
            $('#file_div_hidden_container_' + id).remove();
            $('#file_div_' + id).fadeOut(function(){$(this).remove();});
            $('#loading_icon').fadeOut();
            refreshFileContainer(encrypted_folder);
        });
    }
    function refreshFileContainer(encrypted_folder) {
        refreshElement('#file_container','files','pd=' + encrypted_folder, '#main_file', function(){
            refreshVideoJs();
        }); 
    }

    function refreshVideoJs() {
        $('video.video-js').each(function(){
            var video_id = $(this).attr('id');
            //console.log('Video: ' + video_id + ", type is: " + typeof _V_.players[video_id]);
            
            if(typeof _V_.players[video_id] === "undefined") {
                //console.log('Creating video for: '+ video_id);
                videojs(video_id, {}, function(){
                    this.on('play', function(){
                        videoPlay(video_id);
                    });
                    this.on('pause', function(){
                        videoPause(video_id);
                    });
                    this.on('ended', function(){
                        videoEnded(video_id);
                    });
                });

           }
        });
    }
    function fileView(id) {
        $.post("Scripts/files.class.php", {file_id: id, action: "view"}, function(response){
            console.log("ID: " + id + " -Viewed File: " + response);
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
            refreshElement('#file_container','files','pd=' + encrypted_folder, '#main_file');
        });
    }
    function audioPlay(id)
    {
        fileView(id);
        var src = $('#image_' + id).css('background-image');
        if (src.indexOf("Play") >= 0)
        {
            startAudioInfo(id);
            $("#audio_info_" + id).slideDown();
            $('#audio_' + id).get(0).play();
            $('#image_' + id).css('background-image', "url('../Images/Icons/Icon_Pacs/glyph-icons/glyph-icons/PNG/Pause.png')");
            $('#audio_play_icon_' + id).slideDown();
            $('#audio_play_icon_seperator_' + id).slideDown();
        }
        else
        {
            $('#audio_' + id).get(0).pause();
            $('#image_' + id).css('background-image', "url('../Images/Icons/Icon_Pacs/glyph-icons/glyph-icons/PNG/Play.png')");
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
        $('#audio_' + id).bind('ended', function(){
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
        if($("#" + id).parents('#file_container').length !== 0) {
            id = file_id;
            $('#audio_play_icon_' + id).css('visibility', 'visible');
            $('#audio_play_icon_' + id).animate({opacity: "1"}, 200);
        } else if($("#" + id).parents('.files_recently_shared').length !== 0) {      
            $('.files_recently_shared').find(".files_feed_active").not(":has(#" + id + ")").removeClass("files_feed_active").find('video').each(function(){
                        if(videojs("#" + $(this).attr('id')).paused() === false) {
                            videojs("#" + $(this).attr('id')).player().pause();
                        }
            });
            $('.files_recently_shared_container').mCustomScrollbar("scrollTo", "#" + id, {
                scrollInertia: 600,
                scrollOffset: "200px"
            }); 
                
            $("#" + id).parents('.files_feed_item').not(".files_feed_active").addClass("files_feed_active");
            
        }
    }
</script>
<script>
    function getContentWidth(element) {
        var width = 0;
        $(element).children().each(function(){
            width += $(this).outerWidth(true);
        });
        return width;
    }
    function videoPause(id) {
         console.log('paise');
        if($("#" + id).parents('#file_container').length !== 0) {
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
        if($("#" + id).parents('#file_container').length !== 0) {

        } else if($("#" + id).parents('.files_recently_shared').length !== 0) {
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
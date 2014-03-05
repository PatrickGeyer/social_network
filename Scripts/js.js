function changePostFeed(view) {
    if (view == "File") {
        $(".activity_comment_div").hide();
        $('.comment_box_original').show();
    }
    else if (view == "Post") {
        $(".activity_comment_div").show();
        $('.comment_box_original').hide();
    }
    else {

    }
}

function modal(container, properties) {
    if ($('.background_white_overlay').length > 0) {
        removeModal('', function() {
            modal(container, properties);
        });
        return;
    }

    properties.text = (typeof properties.text === "undefined") ? '' : properties.text;
    properties.type = (typeof properties.type === "undefined") ? '' : properties.type;
    properties.centered = (typeof properties.centered === "undefined") ? false : properties.centered;

    var overlay = $('<div class="background_white_overlay"></div>');
    overlay.on('click', function() {
        removeModal();
    });
    if (properties.type === "error") {
        var image_src = ERROR_RED;
        var image = '<img class="rotate_loader" src="'
                + image_src + '"></img>';
    } else {
        var image = $("<div class='loader_outside'></div><div class='loader_inside'></div>");
    }


    var text = $(document.createElement('span'));
    text.html(properties.text);
    if (properties.type === "error") {
        text.css('color', 'red');
        text.css('font-weight', 'bolder');
    }
    else {
        text.css('color', 'rgb(0, 140, 250)');
    }
    if (properties.centered === true) {
        var table = $("<table style='width:100%;height:100%;'><tr><td style='vertical-align:middle;text-align:center;' id='appender'></td></tr></table>");
        table.find("#appender").append(image);
        table.find("#appender").append(text);
        overlay.append(table);

    }
    else {
        overlay.append(image);
        overlay.append(text);
    }
    container.append(overlay).hide().fadeIn();
}

function removeModal(type, callback) {
    if (typeof callback == "undefined") {
        callback = function() {
        };
    }
    if (type !== "force") {
        $('.background_white_overlay').fadeOut(function() {
            $(this).remove();
            callback();
        });
    }
    else {
        $('.background_white_overlay').remove();
        callback();
    }
}

function scroll_to_bottom(element, is_bottom, force) {
    if (is_bottom === true || force === true) {
        //element.scrollTop(element.get(0).scrollHeight);
        element.mCustomScrollbar("scrollTo", 'bottom');
        alert($('.message_convo_wrapper').length);
        $('.message_convo_wrapper').mCustomScrollbar('scrollTo', 'bottom');
        element.mCustomScrollbar("update");
        //console.log('scrolling: ' + element.get(0).scrollHeight + " - " + element.scrollTop());
    } else {
        console.log('false');
    }
}

function pulsate(element, speed, color) {
    var prev_border = element.css('border');
    element.css("border", "1px solid " + color);
    setTimeout(function() {
        element.css("border", '');
    }, speed);
}

function toolTip(element, content, buttons, properties) {
    properties.modal = (typeof properties.modal === "undefined") ? true : properties.modal;
    properties.loading = (typeof properties.loading === "undefined") ? false : properties.loading;
    properties.title = (typeof properties.title === "undefined") ? "Undefined Title" : properties.title;
    properties.width = (typeof properties.width === "undefined") ? "auto" : properties.width;

    var dialog_container = $("<div class='dialog_container'></div>").css({'opacity': '0'});
    $('body').append(dialog_container);

    var content_container = $("<div class='dialog_content_container'></div>");
    dialog_container.append(content_container);

    if (content.type == "text")
    {
        dialog_container.append(content.content);
    }
    else if (content.type == "html")
    {
        content_container.append(content.content);
    }
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
    alert(element.html());
    alignTooltip(element, dialog_container);

}

function alignTooltip(element, tooltip) {
    var top = element.offset().top;
    var left = element.offset().left;
    tooltip.css({
        position: "fixed",
        top: top,
        left: left
    });
    //console.log('tooltip created');
}

function fileList(element, type) {
    $.post('Scripts/home.class.php', {type: type, action: "file_list"}, function(response) {
        $(element).html(response);
        $(element).mCustomScrollbar(SCROLL_OPTIONS);
    });
}

function setProfilePicture(file_id) {
    $.post('Scripts/user.class.php', {action: "profile_picture", file_id: file_id}, function(response) {
        //console.log("Profile Picture:" + file_id);
        removeDialog();
        window.location.reload();
    });
}

function show_photo_choose() {
    var content = $("<div><table><tr><td><div class='upload_here'></div></td><td><div class='profile_picture_chooser' style='max-height:200px;overflow:auto;margin-left:20px;height:100%;' id='file_container'>Loading...</div></td></tr></table></div>");
    dialog(
            content = {
                content: content.html(),
                type: "html"
            },
    buttons = [{
            type: "success",
            text: "Choose",
            onclick: function() {
                dialogLoad();
                setProfilePicture(profile_picture_id);
            }
        }, {
            type: "neutral",
            text: "Cancel",
            onclick: function() {
                removeDialog();
            }
        }],
    properties = {
        title: "Choose a photo"
    }
    );
    fileList('div#file_container', 'Image');
    //$('#file_container').attr('onclick','').unbind('click');
    $(document).on('click', '.profile_picture_chooser .file_item', function(event) {
        event.stopPropagation();
        var file = $(this).data('file');
        var activity_id = $(this).attr('activity_id');
        $.post('Scripts/files.class.php', {action: "preview", file_id: file.object.file_id, activity_id: activity_id}, function(response) {
            response = $.parseJSON(response);
            $('.upload_here').css('background-size', 'cover');
            $('.upload_here').css('background-image', 'url("' + response.file.path + '")');
            profile_picture_id = response.file.id;
        });
    });
}

function initializeWaveForm() {
    $('[uid]').each(function() {
        createWaveForm($(this).attr('uid'), function() {
        });
    });
}

function renameFile(id, text) {
    $.post('Scripts/files.class.php', {action: "rename", file_id: id, name: text}, function() {

    });
}
var entity_preview = null;
function showUserPreview(element, user_id) {
    if (element == "force") {
        $('.user_preview_info').show();
    }
    else
    {
        if ($('#user_preview_' + user_id).length <= 0) {
            $('.user_preview_info').remove();
            if (element.children('.user_preview_info').length == 0)
            {
                createUserPreview(element, user_id);
            }
            else
            {
                console.log("Preview is already in place.");
            }
        }
    }
}

function createUserPreview(element, user_id)
{
    var bg = "<div id='user_preview_initial_loader' style='width:100%;height:100px;background-image:url(Images/ajax-loader.gif);background-position:center;background-repeat:no-repeat;'></div>";
    var cont = $('<div id="user_preview_' + user_id + '" style="display:none;" class="user_preview_info">' + bg + '</div>');
    element.append(cont);
    var createTimeout = setTimeout(function() {
        if ($('*[user_id="' + user_id + '"]:hover').length > 0)
        {
            $.post('Scripts/user.class.php', {action: "get_preview_info", id: user_id}, function(response) {
                fillUserPreview(response);
            });
            cont.fadeIn(200);
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
    var arrow;

    if (element.parents('.chatcomplete').length > 0) {
        alignment = "right";
    } else {
        alignment = "left";
    }

    if (element.parents('.container').length > 0) {
        alignment = "vertical";
    }
    arrow = 'user_preview_arrow-' + alignment;

    //alert(alignment);

    $('.user_preview_info').append('<div class="' + arrow + '-border"></div>');
    $('.user_preview_info').append('<div class="' + arrow + '"></div>');

    var top = element.offset().top;
    var element_height = element.outerHeight(true);
    var scrolled_height = $(document).scrollTop();
    var top_total = top - scrolled_height - element_height / 2;
    var arrow_height = $('.' + arrow + "-border").outerHeight(true) / 2 - 2;
    //top_total = top_total + arrow_height;
    var left = element.offset().left;
    var width = element.width();
    var left_total = left + width + 20;

    if (alignment == "vertical") {
        left_total = element.offset().left;
        top_total += element.height() + 25;
    } else if (alignment == "right") {
        left_total = element.offset().left - $('.user_preview_info').outerWidth(true) - 25;
        top_total += element.height() - 5;
    } else if (alignment == "left") {
        left_total = element.offset().left + element.outerWidth(true) + 25;
        top_total += element.height();
    }

    $('.user_preview_info').css('left', left_total + "px");
    $('.user_preview_info').css('top', top_total + "px");
}
function fillUserPreview(response)
{
    response = $.parseJSON(response);
    //console.log(response);
    var user_string;
    user_string = ("<table style='height:100%;width:100%;' cellspacing='0'><tr><td rowspan='3' style='width:80px;'><div style='width:70px;height:70px;background-image:url(" +
            response[2] + ");background-size:cover;background-repeat:no-repeat;'></div></td>");
    user_string += ("<td><a style='padding:0px;' href='user?id=" + response[0] +
            "'><span class='user_preview_name'>" + response[1] + "</span></a></td></tr>");

    user_string += "<tr><td><a style='padding:0px;display:inline;' href='community?id=" + response[6] +
            "'><span class='user_preview_community'>" + response[4] + "</span></a><span class='user_preview_position'> &bull; " + response[5] + "</span></td></tr>";
    user_string += "<tr><td><span class='user_preview_about'>" + response[3] + "</span></td></tr>";

    user_string += "<tr><td></td><td><div class='user_preview_buttons'>" +
            "<button onclick='window.location.assign(\"message?c=" + response[0] + "\");' class='pure-button-success smallest'>Message</button></div></td></tr>";

    user_string += "</table>";

    $('.user_preview_info').find('#user_preview_initial_loader').remove();
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
                $('.user_preview_info').remove();
                //console.log('Removed Preview in Mode: ' + mode + ", successfully");
            }
        }
        else
        {
            $('.user_preview_info').remove();
        }
    }
}
$(function() {
    var activity = new Object;
    activity.user = new Object();
    activity.view = 'home';
    activity.type = 'Text';

    activity.user.id = '2';
    activity.user.name = 'Patrick Geyer';
    activity.user.encrypted_id = "MKE3";

    activity.comment = new Array();
    activity.comment.push(new Object());
    //$('.container').html(homify(activity));
});

function getFeed(entity_id, entity_type, min_activity_id, activity_id, callback) {
	var data = {
		action: "get_feed",
		min_activity_id: min_activity_id,
		entity_id: entity_id,
		entity_type: entity_type,
		activity_id: activity_id,
	};
    $.post('Scripts/home.class.php', data, function(response) {
        response = $.parseJSON(response);
        callback(response);
        console.log(response);
    });
}

function empty_feed(properties) {
	return '<div style="margin:auto;margin-top:20px;text-align:center;"><span class="post_comment_time">' + properties.text + "</span></div>";
}
function homify(activity) {
	activity.stats.like.count = parseInt(activity.stats.like.count);
	activity.status_text = typeof activity.status_text !== 'undefined' && !isEmpty(activity.status_text) ? activity.status_text : '';
	
    var string = "";
    if (activity.view == 'home') {
        string += "<div data-activity_id='" + activity.id + "' class='post_height_restrictor' id='post_height_restrictor_" + activity.id + "'>";
        string += '<div id="single_post_' + activity.id + '" class="singlepostdiv">';
        string += "<div id='" + activity.id + "'>";
        string += "<table class='singleupdate'><tr>"
                + "<td class='updatepic' style='width:65px;'>";
        string += "<a class='user_name_post' href='user?id=" + activity.user.encrypted_id + "'>";
        string += "<div class='imagewrap' style='background-image:url(" + activity.user.pic + ");'></div></a></td><td class='update'>";
        string += "<a class='user_name_post user_preview user_preview_name' user_id='" + activity.user.id
                + "' href='user?id=" + activity.user.encrypted_id + "'>";
        string += activity.user.name + "</a>";

        string += print_stats(activity);

        if (activity.type == "Text" || activity.type == "File") {
            string += "<hr class='post_user_name_underline'>";
            string += "<p class='post_text'>" + activity.status_text + '</p>';
        }

        if (activity.media.length > 0) {
            string += "<div class='post_feed_media_wrapper'>";
            for (var i in activity.media) {
                string += print_file(activity.media[i], activity.id, activity.user.id);
            }
            string += "</div>";
        }

        string += "</tr><tr><td></td><td>";

        string += '<div class="comment_box"><div class="comment_box_comment">';
        for (var i in activity.comment) {
            string += show_comment(activity.comment[i]);
        }

        string += "</div><div id='comment_input_" + activity.id + "' class='comment_input' ";
        string += "style='padding-left:2px;padding-top:2px;'><table style='width:100%;'>";
        string += "<tr><td style='vertical-align:top;width:40px;'>";
        string += "<div class='post_comment_profile_picture post_comment_profile_picture_user' ";
        string += "style='background-image:url(" + activity.user.pic + ");'></div></td><td cellspacing='0' style='vertical-align:top;'>"; //WRONG PIC
        string += '<textarea data-activity_id="' + activity.id + '" placeholder="Write a comment..." ';
        string += 'class="home_comment_input_text inputtext" id="comment_' + activity.id;
        string += '"></textarea>';
        string += "<div class='home_comment_input_text textarea_clone' id='comment_" + activity.id + "_clone'></div></td></tr></table></div>";

        string += "</div>";
        string += "</td></tr></table></div>";
        string += "</td></tr></table></div></div>";
    }
//        else if($view == "preview") {
//            string = '';
//            string += "<table class='singleupdate'><tr>"
//            + "<td class='updatepic' style='max-width:50px;'>";
//            string += "<a class='user_name_post' href='user?id=" + urlencode(base64_encode(activity['user_id'])) + "'>";
//            string += "<div class='imagewrap' style='background-image:url(\""
//            + $this->user->getProfilePicture("thumb", activity['user_id'])
//            + "\");'></div></a></td><td class='update'>";
//            string += "<a class='user_name_post user_preview user_preview_name' user_id='" + activity['user_id']
//            + "' href='user?id=" + urlencode(base64_encode(activity['user_id'])) + "'>";
//            string += $this->user->getName(activity['user_id']) + "</a>";
//
//            string += $this->getStats(activity, 'none');
//
//            string += "</tr><tr><td colspan='2'><hr class='post_user_name_underline'></td></tr>";
//            string += "<tr><td colspan='2'><div class='switch_container'><div class='switch_option switch_selected' onclick='changePostFeed(\"File\")'>File<div class='switch_corner'></div></div>
//                        <div class='switch_option' onclick='changePostFeed(\"Post\")'>Post<div class='switch_corner'></div></div></div></td></tr>";
//            string += "<tr><td><p class='post_text'>" + activity['status_text'] + '</p></td>';
//
//            string += "</tr><tr><td colspan='2'>";
//            
//            string += '<div activity_id="'.activity['id'].'" id= comment_div_' + activity['id'] + ' class="comment_box comment_box_original">';
//            string += $this->getComments(activity['id']);
//            string += $this->commentInput(activity);
//            string += "</div>";
//            
//            string += "<div activity_id='".activity_id."' class='activity_comment_div comment_box' style='display:none;'>";
//            string += $this->getComments(activity_id);
//            string += $this->commentInput(activity_id);
//            string += "</div>";
//            
//
//            string += "</td></tr></table></div></div>";
//            string += "</div></td></tr></table>";
//            return string;
//        }
    return string;
}

function show_comment(comment) {
    var string = '';
    comment.like.like_text = (comment.like.has_liked ? "Unlike" : "Like");
    string += "<div class='single_comment_container' data-comment_id='" + comment.id + "'>";
    string += "<table style='font-size: 0.9em;'><tr><td style='vertical-align:top;' rowspan='2'>";
    string += "<div class='post_comment_profile_picture post_comment_profile_picture_user' style='background-image:url(" + comment.user.pic + ");'></div></td><td style='vertical-align:top;'>";
    string += "<a class='userdatabase_connection' href='user?id=" + comment.user.encrypted_id + "'>";
    string += "<span class='user_preview user_preview_name post_comment_user_name' user_id='" + comment.user.id + "'>" + comment.user.name + " </span></a>";
    string += "";
    string += "<span class='post_comment_text'>" + comment.text + "</span>"
    string += "</td></tr><tr><td colspan=2 style='vertical-align:bottom;' >"
    string += "<span class='post_comment_time'>" + comment.time + "</span>"
    string += "<span class='post_comment_time post_comment_liked_num'>- "
    string += comment.like.count + " likes</span>"
    string += "<span data-has_liked='" + comment.like.has_liked + "' "
    string += "class='user_preview_name post_comment_time post_comment_vote'>"
    string += comment.like.like_text + "</span>";
    string += "</tr></table>";
    if (comment.user.id == USER_ID) {
        string += "<img height='15px'src='../Images/Icons/Icon_Pacs/typicons.2.0/png-48px/delete-outline.png' class='comment_delete'></img>";
    }

    string += "</div><hr class='post_comment_seperator'>";
    return string;
}

function print_stats(activity) {
	var string = '';
	string += "<div class='activity_stats'>";
	string += print_likes(activity);
	string +=  "<span class='post_comment_time'> " + activity.time + "</span>";
        if(activity.type == "File") {
            string +=  "<span class='post_comment_time'>| <span class='post_view_count'>" + activity.media[0].view.count + "</span> views</span>";
        }
        
    if (activity.user.id == USER_ID) {
    	string += "<div class='default_dropdown_actions' style='display:inline-block;' wrapper_id='activity_options_" + activity.id + "'>"
        string += "<span class='default_dropdown_preview'>|</span>"
        string += "<div class='default_dropdown_wrapper' id='activity_options_" + activity.id + "'>"
        string += "<ul class='default_dropdown_menu'>"
       	string += "<li class='default_dropdown_item delete_activity' controller_id='activity_options_" 
        string += activity.id + "'>Delete"
        string += "</li>"
        string += "<li class='default_dropdown_item edit_activity'>Edit</li>"
        string += "</ul>"
        string += "</div>"
        string += "</div>";
    }
    string += "</div>";
    return string;
    
    function print_likes(activity) {
    	var string = '';
    	string += '<div class="who_liked_hover" ';
        string += 'style="display:inline;"> ';
        string += '<span class="post_comment_time post_like_count">' + activity.stats.like.count + '</span>';
        string += '<img class="heart_like_icon" src="' + HEART_ICON + '"></img>';
        string += '<div style="display:inline;">';
        string += '<span has_liked="';
        string += (activity.stats.like.has_liked === true ? "true" : "false" );
        string += '" class="post_comment_time user_preview_name activity_like_text post_like_activity">';
        string += (activity.stats.like.has_liked === true ? COMMENT_UNLIKE_TEXT : COMMENT_LIKE_TEXT ) + '</span><span class="post_comment_time">|</span></div>';
        string += "</span></div>";

        string += '<div class="who_liked" id="who_liked_' + activity.id + '">';
        for (var i = 0; i < activity.stats.like.count; i++) {
            name = activity.stats.like.user[i].name;
            if (i == 1) {
                string += name;
            }
            else {
                string +=  ",<br>" + name;
            }
        }
        if (activity.stats.like.count == 0) {
            string +=  "No one has liked this post yet.";
        }
        string +=  "</div>";
        
        return string;
    }
}

function print_file(file, activity_id, activity_user) {

    file.name = typeof file.name !== 'undefined' && !isEmpty(file.name) ? file.name : 'Untitled';
    file.description = typeof file.description !== 'undefined' && !isEmpty(file.description) ? file.description : '';
    file.time = typeof file.time !== 'undefined' && !isEmpty(file.time) ? file.time : 'No Date';
    file.type_preview = typeof file.type_preview !== 'undefined' && !isEmpty(file.type_preview) ? file.type_preview : file.thumb_path;

    var string = '';

    var post_classes = " class='post_feed_item ";
    var post_styles = " style='";
    var post_content = "";
    var classes = '';

    if (file.type == "Audio") {
        post_classes += "post_media_double";
//            post_styles += " height:auto; ";
        post_content += print_doc_file(file);
    }
    else if (file.type == "Image") {
        //post_styles += "height:150px;";
        post_classes += "post_media_full post_media_photo";
        post_content += print_doc_file(file);
        //post_styles += "' onclick='initiateTheater(" + activity_id + ", " + file.id + ");";
    }
    else if (file.type == "Video") {
        post_classes += "post_media_video";
        post_content += video_player(file.id, file.path, $classes, "height:100%;", "home_feed_video_", TRUE);
    }
    else if (file.type == "WORD Document"
            || file.type == "PDF Document"
            || file.type == "EXCEL Document"
            || file.type == "PPT Document"
            || file.type == "Folder") {
        post_styles += "height:auto;";
        post_classes += "post_media_double";
        post_content += print_doc_file(file);
    }
    else if (file.type == "Folder") {
        post_styles += "height:auto;";
        post_classes += "post_media_double";
        post_content += print_doc_file(file);
    }
    else if (file.type == "Webpage") {
        post_classes += "post_media_full";
        post_styles += "height:auto;";
        post_content += "<table style='height:100%;'><tr><td rowspan='3'>" 
                + "<div class='post_media_webpage_favicon' style='background-image:url(&quot;" 
                + file.web_favicon + "&quot;);'></div></td>" + "<td>" 
                + "<a class='user_preview_name' target='_blank' href='" 
                + file.URL + "'><span style='font-size:13px;'>" 
                + file.web_title + "</span></a></div></td></tr>" 
                + "<tr><td><span style='font-size:12px;' class='user_preview_community'>" 
                + file.web_description + "</span></td></tr></table>";
    } else {
        post_classes += "post_media_full";
        post_content += print_doc_file(file);
    }
    if (file.type != "Webpage") {
        post_content += "<a href='" + file.path + "' download>" + "<div style='right:15px;background-image: url(\"" + DOWNLOAD_ARROW + "\");' class='delete_cross delete_cross_top'>" + "</div></a>";
    }
    string += "<div file_id='" + file.id + "' " + post_classes + "' " + post_styles + "'>" + post_content;
    if (activity_user == USER_ID) {
        string += "<div style='background-image: url(\"" + DELETE + "\");' class='delete_cross delete_cross_top remove_event_post'></div>";
    }
    string += "</div>";

    return string;
}

function print_doc_file(file) {
    var preview_classes = '';
    var preview_styles = '';
    var preview_content = '';
    var post_content = '<tr><td>';
    var post_content_under_title = '<tr><td>';
    var string = '';

    var link = "files?f=" + file.id;
    var path = file.type_preview;

    if (file.type == "Folder") {
        path = FOLDER_THUMB;
        link = "files?pd=" + file.encrypted_folder_id;
    } else if (file.type == "Image") {
        path = file.thumb_path;
        preview_classes += " post_media_photo ";
        preview_styles += " width:auto;height:auto; ";
        preview_content += "<div class='fade_right_shadow'></div><img style='opacity:0;max-width:150px;max-height:150px;' src='" + path + "'></img>"; //Shadow

    } else if (file.type == "Audio") {
        preview_styles = 'background-image: none;';
        preview_content += audio_player(file, 'button');
        post_content_under_title += audio_player(file, 'timeline');
    } else {
        preview_classes += "";
        preview_styles += "background-image: url(\"" + path + "\")";
        preview_content += "<div class='fade_right_shadow'></div><img style='opacity:0;max-width:150px;max-height:150px;' src='" + path + "'></img>";
    }

    string += "<table cellspacing='0' style='table-layout:auto;width:100%;'><tr><td style='width:10px;' rowspan='4'>";
    string += "<div class='post_media_preview " + preview_classes + "' style='background-image:url(&quot;";
    string += path + "&quot;); " + preview_styles + "'>" + preview_content + "</div></td>";
    string += "<td rowspan='4' style='padding-right:10px;'><hr class='file_preview_seperator'></td><td style='height:10px;'>";
    string += "<a class='user_preview_name' target='_blank' href='" + link + "'>";
    string += "<p style='max-width:90%;margin:0px;font-size:13px;'>";
    string += file.name + "</p></a></td></tr>";
    string += post_content_under_title;
    string += "</td></tr><tr><td style='height:20px;'><span style='font-size:12px;' class='post_comment_time'>";
    string += file.description + "</span></td></tr><tr><td><span class='post_comment_time'>Uploaded: ";
    string += file.time + "</span></td></tr>";
    string += post_content + "</td></tr></table>";
    return string;
}
function audio_player(file, part) {

    var string = '';

    string += '<div data-path="' + file.thumb_path + '" uid="' + file.uid + '" data-file_id="' + file.id + '">';

    if (part == 'all') {

        string += '<div class="audio_container">';
        string += audio_button(file.thumb_path);
        string += audio_info(file.name, file.thumb_path);
        string += '</div>';

    } else if (part == "button") {
        string += audio_button(file.thumb_path);
    } else if (part == "info") {
        string += audio_info(file.thumb_path);
    } else if (part == 'timeline') {
        string += audio_timeline();
    }
    string += "</div>";
    return string;

    function audio_button(path) {
        return '<audio style="display:none;"><source src="' + file.path + '"></source><source src="' + file.thumb_path + '"></source></audio><div class="audio_button" style="background-image:url(' + AUDIO_PLAY_THUMB + ')"><div class="audio_loader"><div class="loader_outside"></div><div class="loader_inside"></div><span class="audio_loader_text loader_text"></span></div></div>';
    }

    function audio_info(name) {
        return '<div class="audio_info"><div class="ellipsis_overflow audio_title">' + file.name + '</div>' + audio_timeline() + '<div class="audio_time">0:00</div></div>';
    }

    function audio_timeline() {
        return '<div class="audio_progress_container"><div class="audio_progress"></div><div class="audio_buffered"></div><div class="audio_line"></div>';
    }
}
var audio_items = new Array();
function audioPlay(id, start, progress, end, uid)
{
    fileView(id);

    var src = $('[uid="' + uid + '"] .audio_button').css('background-image');
    if (src.indexOf(AUDIO_PLAY_THUMB) >= 0)
    {
        startAudioInfo(id, start, progress, end, uid);
        $('[uid="' + uid + '"] .audio_button').css('background-image', "url('" + AUDIO_PAUSE_THUMB + "')");
    }
    else
    {
        audio_items[uid].pause();
        $('[uid="' + uid + '"] .audio_button').css('background-image', "url('" + AUDIO_PLAY_THUMB + "')");
    }
}
function startAudioInfo(id, start, progress, end, uid)
{
    if (uid in audio_items) {
        audio_items[uid].play();

    } else {
    $("[uid='" + uid + "'] .audio_loader").fadeIn();
        //            createWaveForm(uid, progress);
        //            audio_items[uid].on('ready', function() {
        //                audio_items[uid].play();
        //            });

        var audio = $('[uid="' + uid + '"] audio');
        audio.get(0).play();
        audio_items[uid] = audio.get(0);

        audio.bind('progress', function() {
            var track_length = audio.get(0).duration;
            var secs = audio.get(0).buffered.end(0);
            var progress = (secs / track_length) * 100;
            $('[uid="' + uid + '"] .audio_buffered').css('width', progress + "%");
        });
        audio.bind('timeupdate', function() {
            var track_length = audio.get(0).duration;
            var secs = audio.get(0).currentTime;
            var progress = (secs / track_length) * 100;
            $('[uid="' + uid + '"] .audio_progress').css('width', progress + "%");

            var minutes = Math.floor(track_length / 60);
            var seconds = Math.floor(track_length - minutes * 60);

            var done_secs = audio.get(0).currentTime;
            var done_minutes = Math.floor(done_secs / 60);
            var done_remaining_secons = Math.floor(done_secs - done_minutes * 60);
            $('[uid="' + uid + '"] .audio_time').html(done_minutes + ":" + pad(done_remaining_secons) + " - " + minutes + ":" + seconds);
            $("[uid='" + uid + "'] .audio_loader").fadeOut();
        });

        audio.bind('canplaythrough', function() {
            $('[uid="' + uid + '"] .audio_buffered').css('background-color', 'grey');
        });
            
        
        
        audio.bind('ended', function() {
            audio.get(0).currentTime = 0;
            $('[uid="' + uid + '"] .audio_button').css('background-image', "url('../Images/Icons/Icon_Pacs/glyph-icons/glyph-icons/PNG/Play.png')");
        });
        $('[uid="' + uid + '"] .audio_progress_container').click(function(e)
        {
            var x = $(this).offset().left;
            var width_click = e.pageX - x;
            var width = $(this).width();
            var percent_width = (width_click / width) * 100;
            $('[uid="' + uid + '"] .audio_progress').css('width', percent_width + "%");

            var secs = audio.get(0).duration;
            var new_secs = secs * (percent_width / 100);

            audio.get(0).currentTime = new_secs;
        });
    }

    start = typeof start !== 'undefined' ? start : function() {
    };
    progress = typeof progress !== 'undefined' ? progress : function() {
    };
    end = typeof end !== 'undefined' ? end : function() {
    };

    start();
}

function createWaveForm(uid, progress) {
    if (uid in audio_items === false) {
        $("[uid='" + uid + "'] .audio_loader").fadeIn();

        var wavesurfer = Object.create(WaveSurfer);

        wavesurfer.init({
            container: '[uid="' + uid + '"] .audio_progress_container',
            waveColor: 'lightblue',
            progressColor: 'rgb(0, 140, 250)',
            height: 30,
            loopSelection: false,
            dragSelection: false,
            cursorColor: 'grey',
            normalize: true,
            minPxPerSec: 20
        });
        wavesurfer.on('ready', function() {
            $("[uid='" + uid + "'] .audio_loader").fadeOut();
        });

        wavesurfer.on('progress', function(percent) {
            percent = percent * 100;
            progress(percent);
        });
        wavesurfer.on('loading', function(percent) {
            $("[uid='" + uid + "'] .audio_loader_text").html(percent + "%");
        });
        wavesurfer.load($("[uid='" + uid + "']").data('path'));
        audio_items[uid] = wavesurfer;
    }
}

function removeAudio(id)
{
    $('#audio_container_' + id).remove();
}
function videoPlayer() {
    var string = '';
    return string;
}
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
        //initiateTheater(null, file_id, {});
        videojs("#" + id).player().pause();
        videojs("#" + id).player().currentTime(0); // 2 minutes into the video            
        videojs("#" + id).player().posterImage.el.style.display = 'block';
        videojs("#" + id).player().bigPlayButton.show();
    }

}
//var activity_timeout;
function refreshContent(id) {
    //clearTimeout(activity_timeout);
    
    getFeed(null, null, id, null, function(response) {
        var activity_container = $('[data-activity_id="' + id + '"]');
        var comment_container = activity_container.find('.comment_box_comment');
        for (var i in response) { //SHOULD ONLY BE 1
            
            for (var key in response[i].comment) {
                if(comment_container.find('[data-comment_id="' + response[i].comment[key].id + '"]').length == 0) {
                    comment_container.append(show_comment(response[i].comment[key]));
                }
            }
            
            
        }
    });
//    activity_timeout = setTimeout(function() {
//        refreshContent(id);
//    }, 10000);
}

function isEmpty(input) {
    if (input == "null" || input == "" || input == null) {
        return true;
    } else {
        return false;
    }
}
$(function() {
    getOnlineMass();
    setInterval(getOnlineMass, 10000);
});
function getOnlineMass() {
    var users = new Array();
    $('[user_id]').each(function() {
        if ($.inArray($(this).attr('user_id'), users) === -1) {
            users.push($(this).attr('user_id'));
        }
    });
    $.post('Scripts/user.class.php', {action: "getOnlineMass", users: users}, function(response) {
        response = $.parseJSON(response);
//        for(var key in response) {
        for (var prop in response) {
            if (response.hasOwnProperty(prop)) {
                if (response[prop] == true) {
                    $('.profile_picture_' + prop).css('border-left', '2px solid rgb(0, 180, 250)');
                } else {
                    $('.profile_picture_' + prop).css('border-left', '2px solid grey');
                }
                //alert(prop + " = " + response[prop]);
            }
        }
        // }
    });
}

function calculateDistance(elem, mouseX, mouseY)
{
    return Math.floor(Math.sqrt(Math.pow(mouseX - (elem.offset().left + (elem.width() / 2)), 2) + Math.pow(mouseY - (elem.offset().top + (elem.height() / 2)), 2)));
}

$(document).on('click', '.delete_message', function(event) {
    event.stopPropagation();
    event.preventDefault();
    deleteMessage($(this).parents('[thread_id]').attr('thread_id'));
});

$(document).on('click', '.message_inbox_item', function() {
    window.location.assign('message?thread=' + $(this).attr('thread_id') + '');
});

function deleteMessage(thread) {
    $.post('Scripts/notifications.class.php', {action: "deleteMessage", thread: thread}, function(response) {
        console.log('response:' + response);
    });
    $('#inbox_message_' + thread).remove();
}
//USER

function connect(element, user_id) {
    $.post('Scripts/user.class.php', {action: "connect", user_id: user_id}, function() {
        element.addClass('connect_button_invited');
    });
}

function connectAccept(invite_id) {
    $.post('Scripts/user.class.php', {invite_id: invite_id, action: "acceptInvite"}, function() {

    });
}
//END USER
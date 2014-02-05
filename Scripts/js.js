function changePostFeed(view) {
    if(view == "File") {
        $(".activity_comment_div").hide();
        $('.comment_box_original').show();
    }
    else if(view == "Post") {
        $(".activity_comment_div").show();
        $('.comment_box_original').hide();
    }
    else {
        
    }
}

function modal(container, properties) {
    if($('.background_white_overlay').length > 0) {
        removeModal('', function(){modal(container, properties);});
        return;
    }
    
    properties.text = (typeof properties.text === "undefined") ? '' : properties.text;
    properties.type = (typeof properties.type === "undefined") ? '' : properties.type;
    properties.centered = (typeof properties.centered === "undefined") ? false : properties.centered;
    
    var overlay = $('<div class="background_white_overlay"></div>');
    overlay.on('click', function(){
        removeModal();
    });
    var image_src = RELOAD_STILL_BLACK;
    if(properties.type === "error") {
        image_src = ERROR_RED;
    }
    var image = '<img class="rotate_loader" src="'
            + image_src + '"></img>';
    
    var text = $(document.createElement('span'));
    text.html(properties.text);
    if (properties.type === "error") {
        text.css('color', 'red');
        text.css('font-weight', 'bolder');
    } 
    else {
        text.css('color', 'rgb(66, 184, 221)');
    }
    if(properties.centered === true) {
        var table = $("<table style='width:100%;height:100%;'><tr><td style='vertical-align:middle;text-align:center;' id='appender'></td></tr></table>");
        table.find("#appender").append(image);
        table.find("#appender").append("<br /><br />");
        table.find("#appender").append(text);
        overlay.append(table);
        
    }
    else {
        overlay.append(image);
        overlay.append("<br /><br />");
        overlay.append(text);
    }
    container.append(overlay).hide().fadeIn();
}

function removeModal(type, callback) {
    if(typeof callback == "undefined") {
        callback = function(){};
    }
    if(type !== "force") {
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
    $.post('Scripts/home.class.php', {type:type, action:"file_list"}, function(response) {
        $(element).html(response);
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
    var content = $("<div><table><tr><td><div class='upload_here'></div></td><td><div style='max-height:200px;overflow:auto;margin-left:20px;height:100%;' id='file_container'>Loading...</div></td></tr></table></div>");
    dialog(
            content={
                content: content.html(),
                type: "html"
            }, 
            buttons=[{
                    type: "success",
                    text: "Choose",
                    onclick: function() {dialogLoad();setProfilePicture(profile_picture_id);}
            },{
                    type: "neutral",
                    text: "Cancel",
                    onclick: function() {removeDialog();}
            }], 
            properties={
                title: "Choose a photo"
            }
            );
    fileList('div#file_container', 'Image');
    $('#file_container').attr('onclick','').unbind('click');
    $(document).on('click', '.file_search_option', function(event) {
        event.stopPropagation();
        var file_id = $(this).attr('file_id');
        var activity_id = $(this).attr('activity_id');
        $.post('Scripts/files.class.php', {action: "preview", file_id: file_id, activity_id: activity_id}, function(response) {
            response = $.parseJSON(response);
            $('.upload_here').css('background-size', 'cover');
            $('.upload_here').css('background-image', 'url("' + response.file.path + '")');
            profile_picture_id = response.file.id;
        });
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
    
        
    var user_message_info = new Array();
    user_message_info['name'] = response[1];
    user_message_info['id'] = response[0];
            
    var info = JSON.stringify(user_message_info);
    user_string += "<tr><td></td><td><div class='user_preview_buttons'>" +
            "<button onclick='window.location.assign(\"message?c=" + info + "\");' class='pure-button-success smallest'>Message</button></div></td></tr>";

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

function bodyNotification() {
    
}
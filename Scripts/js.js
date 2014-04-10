/****************************************************
 * This script provides client-side functionality.   *
 * It is split into a few parts.                     *
 * 0. Global Setup                                   *
 * 1. Functions (Network and Widgets)                *
 * 2. EventHandlers                                  *
 ****************************************************/

/****************************************************
 * 0. Global Setup                                   *
 ****************************************************/

function Application() {
}
;

Application.prototype.user = {
    preview: {
        showing: false
    },
    isMobile: navigator.userAgent.match(/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/)
};
Application.prototype.file = {
    theater: {
        active: false,
        removeTime: 0,
        previousUrl: ''
    },
    upload: {
        instance: new Array(),
        session: 0
    },
    files: new Array()
};
Application.prototype.chat = {
    room : new Array()
};
Application.prototype.generic = {
};
Application.prototype.feed = {
    comment: {
        comments: new Array()
    },
    post: {
    	files: new Array()
    }
};
Application.prototype.calendar = {
	event : {}
};
Application.prototype.UI = {
    progress: {
        instance: new Array()
    }
};
Application.prototype.notification = {
};
/****************************************************
 * 1. Functions (Network and Widgets)                *
 ****************************************************/

/****************************************************
 * 1.1 UI Functions                                  *
 ****************************************************/


Application.prototype.UI.resizeToMax = function(element, offset_width, offset_height) {
    if (element.width() / this.getViewPortWidth() > element.height() / this.getViewPortHeight()) {
        element.css('height', "auto");
        element.css('max-width', this.getViewPortWidth() - offset_width + "px");
    } else {
        element.css('width', "auto");
        element.css('max-height', this.getViewPortHeight() - offset_height + "px");
    }
}

Application.prototype.UI.getViewPortHeight = function()
{
    var viewportwidth;
    var viewportheight;

    if (typeof window.innerWidth != 'undefined') {
        viewportwidth = window.innerWidth,
                viewportheight = window.innerHeight
    } else if (typeof document.documentElement != 'undefined'
            && typeof document.documentElement.clientWidth !=
            'undefined' && document.documentElement.clientWidth != 0) {
        viewportwidth = document.documentElement.clientWidth,
                viewportheight = document.documentElement.clientHeight
    } else {
        viewportwidth = document.getElementsByTagName('body')[0].clientWidth,
                viewportheight = document.getElementsByTagName('body')[0].clientHeight
    }
    return viewportheight;
}

Application.prototype.UI.getViewPortWidth = function() {
    var viewportwidth;
    var viewportheight;

    if (typeof window.innerWidth != 'undefined') {
        viewportwidth = window.innerWidth,
                viewportheight = window.innerHeight
    } else if (typeof document.documentElement != 'undefined'
            && typeof document.documentElement.clientWidth !=
            'undefined' && document.documentElement.clientWidth != 0) {
        viewportwidth = document.documentElement.clientWidth,
                viewportheight = document.documentElement.clientHeight
    } else {
        viewportwidth = document.getElementsByTagName('body')[0].clientWidth,
                viewportheight = document.getElementsByTagName('body')[0].clientHeight
    }

    return viewportwidth;
};

Application.prototype.UI.progress.create = function(element, id) {
    var upload = $("<div class='progress_container'></div>");
    upload.attr('id', 'progress_container_" + id + "');
    upload.append($("<div class='progress_bar'></div>"));
    $(element).append(upload);
    this.instance[id] = {
        element: upload,
        progress: 0
    };
};

Application.prototype.UI.progress.update = function(id, progress) {
    this.instance[id].element.width(progress + "%");
    if (progress >= 100) {
        this.instance[id].element.addClass('progress_bar_processing');
    }
    this.instance[id].progress = progress;
};

Application.prototype.UI.progress.remove = function(id) {
    this.instance[id].element.remove();
    this.instance.splice(id, 1);
};

Application.prototype.UI.dialog = function(content, buttons, properties) {
    properties.modal = (typeof properties.modal === "undefined") ? true : properties.modal;
    properties.loading = (typeof properties.loading === "undefined") ? false : properties.loading;
    properties.title = (typeof properties.title === "undefined") ? "Undefined Title" : properties.title;
    properties.width = (typeof properties.width === "undefined") ? "auto" : properties.width;

    var dialog_container = $("<div class='dialog_container'></div>").css({'opacity': '0'});
    $('body').append(dialog_container);

    var closingX = $("<span class='dialog_close_button'>x</span>").click(function() {
        Application.prototype.UI.removeDialog();
    });
    var dialog_title = $("<div class='dialog_title'>" + properties.title + "</div>").append(closingX);

    var content_container = $("<div class='dialog_content_container'></div>");
    dialog_container.append(dialog_title);
    dialog_container.append(content_container);

    if (content.type == "text") {
        dialog_container.append(content.content);
    } else if (content.type == "html") {
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
        $('body').append("<div onclick='removeDialog()' style='opacity:0.5' class='background_white_overlay'></div>");
    }

    if (properties.loading == true) {
        dialogLoad();
    }
    this.alignDialog();
    var real_height = dialog_container.height();
    content_container.mCustomScrollbar({
        scrollInertia: 10,
        autoHideScrollbar: true,
    });
    dialog_container.css({height: "0px"});
    dialog_container.animate({minHeight: real_height + "px", opacity: 1}, 100, function() {
        dialog_container.css({height: "auto", opacity: 1}, 'fast');
        content_container.mCustomScrollbar("update");
        setTimeout(function() {
            content_container.mCustomScrollbar("update");
        }, 200);
    });
};

Application.prototype.UI.alignDialog = function() {
    var width = $('.dialog_container').width();
    var height = $('.dialog_container').height();
    $('.dialog_container').css({
        'margin-left': '-' + width / 2 + "px",
        'margin-top': '-' + height / 2 + "px",
    });
};

Application.prototype.UI.removeDialog = function() {
    $('.background-overlay, .background_white_overlay').remove();

    $('.dialog_container').css('min-height', '0px');
    $('.dialog_container').animate({height: 0, opacity: 0}, 100, function() {
        $(this).remove();
    });
};

/****************************************************
 * 1.1 Generic Functions                             *
 ****************************************************/

Application.prototype.search = function(text, mode, element, callback) {
    if (text == "") {
        $(element).hide();
    } else {
        var loader = $("<img src='Images/ajax-loader.gif'></img>");
        $(element).prepend(loader);
        $(element).show();
    }
    $.post("Scripts/searchbar.php", {search: mode, input_text: text}, function(response) {
        $(element).find('.search_slider').remove();
        var slider = $("<div class='search_slider'></div>");
        slider.append(response);
        $(element).append(slider);
        loader.remove();
        // var scrollElement = element.find('.search_slider').get(0);
        slider.show();
        //if (scrollElement.offsetHeight < scrollElement.scrollHeight || scrollElement.offsetWidth < scrollElement.scrollWidth) {
        //$(element).mCustomScrollbar();
        //$(element).find('.mCustomScrollBox ').height('');
        //$(element).mCustomScrollbar("update");
        //$(element).find('.mCS_container').css('top', '0px');
        //}
        callback();

    });
    $(element).off('click');
    $(element).on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
    });
}

Application.prototype.generic.relocate = function(event, element) {
    event.preventDefault();
    $('.container').html("<div class='loader_outside'></div><div class='loader_inside'></div>");
    $.get($(element).attr('href'), {ajax: 'ajax'}, function(response) {
        var container = $(response);
        container.hide();
        $('.container').replaceWith(container);
        container.fadeIn('fast');
        window.history.replaceState({}, 'WhatTheHellDoesThisDo?!', '/' + $(element).attr('href')); //pushState would be better
        $('body').scrollTop(0);
    });
}

/****************************************************
 * 1.1 Prototypes                                    *
 ****************************************************/

String.prototype.replaceLinks = function() {
    var replacedText, replacePattern1, replacePattern2, replacePattern3;

    //URLs starting with http://, https://, or ftp://
    replacePattern1 = /(\b(https?|ftp):\/\/[-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|])/gim;
    this.replace(replacePattern1, '<a href="$1" target="_blank">$1</a>');

    //URLs starting with "www." (without // before it, or it'd re-link the ones done above).
    replacePattern2 = /(^|[^\/])(www\.[\S]+(\b|$))/gim;
    this.replace(replacePattern2, '$1<a href="http://$2" target="_blank">$2</a>');

    //Change email addresses to mailto:: links.
    replacePattern3 = /(([a-zA-Z0-9\-\_\.])+@[a-zA-Z\_]+?(\.[a-zA-Z]{2,6})+)/gim;
    this.replace(replacePattern3, '<a href="mailto:$1">$1</a>');

    return this;
};

String.prototype.replaceEmoticons = function() {
    var emoticons = {
        ':)[': 'smiley/clean/money-mouth.png',
        ':)': 'smiley/clean/smile.png',
        '(:': 'smiley/clean/smile.png',
        ':(': 'smiley/clean/frown.png',
        '):': 'smiley/clean/frown.png',
        ':D': 'smiley/clean/laughing.png',
        ':P': 'smiley/clean/crazy.png',
        'B)': 'smiley/clean/cool.png',
        '<3': 'smiley/clean/heart.png',
        '{UGS}': 'meme/ultra gay seal.png'
    }, url = "http://www.hopto.redirectme.net/", patterns = [],
            metachars = /[[\]{}()*+?.\\|^$\-,&#\s]/g;

    for (var i in emoticons) {
        if (emoticons.hasOwnProperty(i)) { // escape metacharacters
            patterns.push('(' + i.replace(metachars, "\\$&") + ')');
        }
    }

    return this.replace(new RegExp(patterns.join('|'), 'g'), function(match) {
        return typeof emoticons[match] != 'undefined' ?
                '<img onload="Application.prototype.chat.scroll2Bottom(false, chat_room);" src="Images/' + emoticons[match] + '"/>' :
                match;
    });
};

Array.max = function(array) {
    var final_array = new Array();
    for (var i = 0; i < array.length; i++) {
        final_array.push(parseInt(array[i]));
    }
    return Math.max.apply(Math, final_array);
};

Array.min = function(array) {
    var final_array = new Array();
    for (var i = 0; i < array.length; i++) {
        final_array.push(parseInt(array[i]));
    }
    return Math.min.apply(Math, final_array);
};

/****************************************************
 * 1.2 Generic User Interface                        *
 ****************************************************/

Application.prototype.UI.modal = function(container, properties) {
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
};

Application.prototype.UI.removeModal = function(type, callback) {
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
};

Application.prototype.UI.pulsate = function(element, speed, color) {
    var prev_border = element.css('border');
    element.css("border", "1px solid " + color);
    setTimeout(function() {
        element.css("border", '');
    }, speed);
};

Application.prototype.UI.toolTip = function(element, content, buttons, properties) {
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
    alignTooltip(element, dialog_container);
};

Application.prototype.UI.alignTooltip = function(element, tooltip) {
    var top = element.offset().top;
    var left = element.offset().left;
    tooltip.css({
        position: "fixed",
        top: top,
        left: left
    });
};

Application.prototype.UI.resizeContainer = function() {
    var width = Application.prototype.UI.getViewPortWidth();
    if (width < 1400) {
        $('.container').addClass('container_small');
        $('.global_header_container').addClass('global_header_container_small');
    } else {
        $('.right_bar_container').removeClass('right_bar_shift');
        $('.global_header_container').removeClass('global_header_container_small');
        $('.container').removeClass('container_small');
    }
};

Application.prototype.UI.adjustSwitches = function() {
    $('.switch_container').find('.switch_option').each(function() {
        var siblings = $(this).parents('.switch_container').find('.switch_option').length;
        var width = $(this).parents('.switch_container').width() / siblings;
        $(this).width(width);
    });
};

/****************************************************
 * 1.3 Welcome Bar                                   *
 ****************************************************/

Application.prototype.notification.getMessageBox = function() {
    $.post('Scripts/notifications.class.php', {action: "messageList"}, function(response) {
        $('ul.message').html(response);
        $('#popup_message').height($('ul.message').outerHeight(true));
        $('#popup_message').mCustomScrollbar('update');
    });
};

Application.prototype.notification.getNotificationBox = function() {
    $.post('Scripts/notifications.class.php', {action: "notificationList"}, function(response) {
        $('ul.notify').html(response);
        $('#popup_notify').height($('ul.notify').outerHeight(true));
        $('#popup_notify').mCustomScrollbar('update');
    });
};

Application.prototype.notification.getNetworkBox = function() {
    $.post('Scripts/notifications.class.php', {action: "networkList"}, function(response) {
        $('ul.network').html(response);
        $('#popup_network').height($('ul.network').outerHeight(true));
        $('#popup_network').mCustomScrollbar('update');
    });
};

Application.prototype.notification.getNotificationNumber = function() {
    $.post('Scripts/notifications.class.php', {action: "alert_num"}, function(response) {
        response = JSON.parse(response);
        if (response.message != '0') {
            $('#message_num').text(response.message);
            $('#message_num').show();
            getMessageBox();
        }
        else {
            $('#message_num').hide();
        }
        if (response.notification != '0') {
            $('#notification_num').text(response.notification);
            $('#notification_num').show();
            getNotificationBox();
        }
        else {
            $('#notification_num').hide();
        }
        if (response.network != '0') {
            $('#network_num').text(response.network);
            $('#network_num').show();
            getNetworkBox();
        }
        else {
            $('#network_num').hide();
        }

        var all = response.message + response.notification + response.network;
        var title = document.title.lastIndexOf(')');
        if (all > 0) {
            if (title == -1) {
                document.title = "(" + all + ") " + document.title;
            }
            else {
                title = document.title.substring(title + 1);
                document.title = "(" + all + ") " + title;
            }
        }
        // $('#popup_message').mCustomScrollbar('scrollTo', 'top');
        // $('#popup_network').mCustomScrollbar('scrollTo', 'top');
        // $('#popup_notify').mCustomScrollbar('scrollTo', 'top');
        setTimeout(Application.prototype.notification.getNotificationNumber, 10000);
    });

};

/****************************************************
 * 1.3 Files                                         *
 ****************************************************/

/****************************************************
 * 1.3.0 Files - Upload                              *
 ****************************************************/

Application.prototype.file.upload.upload = function(files, onStart, onProgress, onComplete, properties) {
    if ($('.upload_file_container').length == 0) {
        var file_container = $('<div class="event upload_file_container contentblock"></div>');
        file_container.append("<a>Upload</a>");
        var file_upload_container = $('<div class="calendar-event-info-files"></div>');
        file_container.append(file_upload_container);
        $('.calendar-container').append(file_container);
    } else {
        var file_upload_container = $('.upload_file_container .calendar-event-info-files');
    }
    var session = this.session++;
    onStart();
    var length = files.length;
    var files_left = length;

    properties = typeof properties !== 'undefined' ? properties : {type: 'File'};
    properties.type = typeof properties.type !== 'undefined' ? properties.type : 'File';

    for (var i = 0; i < length; i++) {
        (function(count, session) {
            var file = files[count].file;
            var formdata = new FormData();
            formdata.append("file", file);
            formdata.append("action", 'upload');
            formdata.append("parent_folder", parent_folder);
            var xhr = new XMLHttpRequest();
            xhr.upload.onprogress = function(event) {
                Application.prototype.file.upload.progressHandler(event, "" + session + count, onProgress);
            };
            xhr.onload = function() {
                Application.prototype.file.upload.completeHandler(this, "" + session + count, onComplete, name);
            };
            xhr.addEventListener("error", Application.prototype.file.upload.errorHandler, false);
            xhr.addEventListener("abort", Application.prototype.file.upload.abortHandler, false);
            xhr.open("post", "Scripts/files.class.php");
            xhr.send(formdata);
            var file_container = $("<div class='upload_preview'>");
            file_container.attr('id', "" + session + count + "_upload_preview");
            file_container.append(file.name);
            file_container.append("<br />");
            file_upload_container.append(file_container);
            Application.prototype.UI.progress.create(file_container, "" + session + count);
        })(i, session);
    }
};

Application.prototype.file.upload.progressHandler = function(event, id, callback) {
    $('#loading_icon').show();
    var percent = (event.loaded / event.total) * 100;
    percent = Math.round(percent);
    Application.prototype.UI.progress.update(id, percent);
    callback(percent);
};

Application.prototype.file.upload.completeHandler = function(event, id, onComplete) {
    $('#' + id + '_upload_preview').slideUp();
    Application.prototype.UI.progress.remove(id);
    if (this.session > 0) {
        this.session--;
        if (this.session === 0) {
            $('#loading_icon').fadeOut();
            $('.upload_file_container').slideUp(function() {
                $(this).remove();
            });
        }
    }
    if (onComplete == "addToStatus") {
        alert('not adding file to statuc');
    } else {
        if (this.session === 0) {
        }
    }
    onComplete($.parseJSON(event.responseText));
}
Application.prototype.file.upload.errorHandler = function(event) {
    if (this.session > 0) {
        this.session--;
    }
    alert('upload failed');
    $('#loading_icon').fadeOut();
};
Application.prototype.file.upload.abortHandler = function(event) {
    if (this.session > 0) {
        this.session--;
    }
};

/****************************************************
 * 1.3.1 Files - Print                               *
 ****************************************************/

Application.prototype.file.print_folder = function(folder) {
    var string = '';
    for (var file in folder) {
        string += this.print_row(folder[file]);
    }
    return string;
};

Application.prototype.file.print_row = function(file) {
    var string = '';

    if (file.type != "Folder") {
        string += "<div data-file_id='" + file.id + "' class='files'>";
    } else {
        string += "<div data-file_id='" + file.id + "' class='folder'>"; // SAME
    }
    string += "<div class='files_icon_preview' style='background-image:url(\"" + file.type_preview + "\");'></div>";
    string += "<p class='files ellipsis_overflow'>" + file.name + "</p>";

    string += "<div class='files_actions'><table cellspacing='0' cellpadding='0'><tr style='vertical-align:middle;'><td>";

    string += "<a href='" + file.path + "' download><div class='files_actions_item files_actions_download'></div></a></td><td>";
    string += "<hr class='files_actions_seperator'></td><td>";

    string += "<div class='files_actions_item files_actions_delete' "
            + "onclick='deleteFile(this, " + file.id + ");if(event.stopPropagation){event.stopPropagation();}"
            + "event.cancelBubble=true;'></div></td><td>"
            + "<hr class='files_actions_seperator'></td><td>"
            + "<div class='files_actions_item files_actions_share' data-file_id='" + file.id + "'></div></td>";
    string += "</tr></table></div>";

    string += "<div class='file_hidden_container'>";
    string += Application.prototype.file.print(file, "File");
    string += "</div>";

    string += "</div>";
    return string;
};

Application.prototype.file.view = function(id) {
    $.post("Scripts/files.class.php", {file_id: id, action: "view"}, function() {
    });
};

Application.prototype.file.list = function(element, type, callback) {
    callback = typeof callback !== 'undefined' ? callback : function() {
    };
    $.post('Scripts/home.class.php', {type: type, action: "file_list"}, function(response) {
        $(element).html(response);
        $(element).mCustomScrollbar(SCROLL_OPTIONS);
        callback();
    });
};

Application.prototype.file.initializeWaveForm = function() {
    $('[uid]').each(function() {
        createWaveForm($(this).attr('uid'), function() {
        });
    });
};

Application.prototype.file.rename = function(id, text) {
    $.post('Scripts/files.class.php', {action: "rename", file_id: id, name: text}, function() {
    });
};
Application.prototype.file.print = function(file, activity_type) {

    file.name = typeof file.name !== 'undefined' && !isEmpty(file.name) ? file.name : 'Untitled';
    file.description = typeof file.description !== 'undefined' && !isEmpty(file.description) ? file.description : '';
    file.time = typeof file.time !== 'undefined' && !isEmpty(file.time) ? file.time : 'No Date';
    file.type_preview = typeof file.type_preview !== 'undefined' && !isEmpty(file.type_preview) ? file.type_preview : file.thumb_path;
    this.files[file.uid] = file;
    var string = '';
    var post_classes = " class='post_feed_item ";
    var post_styles = " style='";
    var post_content = "";
    var classes = '';
    if (file.type == "Audio") {
        post_classes += "post_media_double";
        post_content += this.printDoc(file);
    } else if (file.type == "Image") {
        post_classes += "post_media_photo";
        post_content += this.printDoc(file);
    } else if (file.type == "Video") {
        post_content += this.printDoc(file);
        post_classes += "post_media_video";
//        post_content += video_player(file, classes, "height:100%;", "home_feed_video_", true);
    } else if (file.type == "WORD Document"
            || file.type == "PDF Document"
            || file.type == "EXCEL Document"
            || file.type == "PPT Document"
            || file.type == "Folder") {
        post_styles += "height:auto;";
        post_classes += "post_media_double";
        post_content += this.printDoc(file);
    } else if (file.type == "Folder") {
        post_styles += "height:auto;";
        post_classes += "post_media_double";
        post_content += this.printDoc(file);
    } else if (file.type == "Webpage") {
        post_classes += "post_media_full";
        post_styles += "height:auto;";
        post_content += "<table style='height:100%;'><tr><td rowspan='3'>";
        post_content += "<div class='post_media_webpage_favicon' style='background-image:url(&quot;";
        post_content += file.web_favicon + "&quot;);'></div></td>" + "<td>";
        post_content += "<a class='user_preview_name' target='_blank' href='";
        post_content += file.URL + "'><span style='font-size:13px;'>";
        post_content += file.web_title + "</span></a></div></td></tr>";
        post_content += "<tr><td><span style='font-size:12px;' class='user_preview_community'>";
        post_content += file.web_description + "</span></td></tr>";
        post_content += "</table>";
    } else {
        post_classes += "post_media_full";
        post_content += this.printDoc(file);
    }

    post_content += "<div class='top_right_actions'>";
    if (file.user_id == USER_ID && activity_type != 'File') {
        post_content += "<div style='background-image: url(\"" + DELETE + "\");' class='delete_cross delete_cross_top remove_event_post'></div>";
    }
    post_content += "</div>";

    if (activity_type == "Text" && typeof file.activity != 'undefined') {
        post_content += '<div class="comment_box">';
        post_content += '<div class="comment_box_comment">';
        post_content += Application.prototype.feed.comment.showComments(file.activity);
        post_content += "</div>"
        post_content += Application.prototype.feed.comment.printInput(file.activity.id);
        post_content += "</div>";
    }
    post_content += "</div></div>"; //CLOSE file_activity_section AND POST_CONTENT;

    if (activity_type == "File" && typeof file.share != 'undefined') {
        //WHO IS FILE SHARED WITH?
    }

    string += "<div data-activity_id='" + file.activity.id + "' data-file_id='" + file.id + "' " + post_classes + "' " + post_styles + "'>";
    string += post_content + "</div>";

    return string;
};

Application.prototype.file.printDoc = function(file) {
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
        link = "files?pd=" + file.enc_parent_folder_id;
    } else if (file.type == "Image") {
        path = file.thumb_path;
        preview_classes += " post_media_photo ";
        preview_styles += " width:auto;height:auto; ";
        preview_content += "<img style='opacity:0;max-width:150px;max-height:150px;' src='" + path + "'></img>";
    } else if (file.type == "Video") {
        path = file.thumbnail;
        preview_classes += " post_media_photo ";
        preview_content += "<img style='position:absolute;top:40%;left:40%;' src='" + VIDEO_BUTTON + "'></img>";
    } else if (file.type == "Audio") {
        preview_styles = 'background-image: none !important;';
        preview_content += "<i class='fa fa-music'></i>";
        preview_content += this.audioPlayer(file, 'button', false);
        post_content_under_title += this.audioPlayer(file, 'timeline');
    } else {
        preview_classes += "";
        preview_styles += "background-image: url(\"" + path + "\")";
        preview_content += "<img style='opacity:0;max-width:150px;max-height:150px;' src='" + path + "'></img>";
    }

    string += "<div class='post_media_preview " + preview_classes + "' style='background-image:url(&quot;";
    string += path + "&quot;); " + preview_styles + "'>" + preview_content + "</div>";
    string += "<div class='file_activity_section'>";
    string += "<a class='user_preview_name' target='_blank' href='" + link + "'>";
    string += "<p style='margin:0px;font-size:13px;'>";
    string += file.name + "</p></a>";
    if (post_content_under_title != "") {
        string += post_content_under_title;
    }
    if (file.description != "") {
        string += "<span style='font-size:12px;' class='post_comment_time'>";
        string += file.description + "</span>";
    }
    string += "";
    string += "<div style='margin-top:5px;'>";
    string += "<i class='heart_like_icon fa fa-heart'></i><span class='post_comment_time post_like_count'>" + file.like.count + "</span>";
    if (typeof file.activity.comment != 'undefined') {
        string += "<i class='fa fa-comment heart_like_icon'></i><span class='post_comment_time post_comment_count'>"
                + (parseInt(file.activity.comment.comment.length) + parseInt(file.activity.comment.hidden)) + "</span>";
    }
    string += "<i class='fa fa-eye heart_like_icon'></i><span class='post_comment_time'>" + file.view.count + "</span><br />";
//    string += "<div class='file_info'><span class='post_comment_time'>Uploaded: " + file.time + "</span><br />";
//    string += "<span class='post_comment_time'>Size: " + file.size + " bits</span><br />";
//    string += "<span class='post_comment_time'>Type: " + file.type + "</span></div>";

    string += "</div>";

    string += "<div style='margin-top:5px;'>";
    string += "<a class='no-ajax' href='download.php?id=" + file.id + "'>" + "<button class='pure-button-green'><i class='fa fa-cloud-download'></i><span></span></button></a>";
    string += "<button has_liked='" + (file.activity.stats.like.has_liked === true ? "true" : "false") + "' class='activity_like_text post_like_activity " 
            + "pure-button-neutral " + (file.activity.stats.like.has_liked === true ? " pure-button-blue" : "") + "'>";
    string += "<i class='fa fa-heart'></i>";
//    string += "<span>" + (file.activity.stats.like.has_liked === true ? COMMENT_UNLIKE_TEXT : COMMENT_LIKE_TEXT) + "</span>";
    string += "</button>";
    string += ""; //LEAVING file_activity_section OPEN!!

    string += post_content;
    return string;
};

Application.prototype.file.audioPlayer = function(file, part, source) {
    var string = '';
    string += '<div data-path="' + file.thumb_path + '" uid="' + file.uid + '" data-file_id="' + file.id + '">';
    if (part == 'all') {
        string += '<div class="audio_container">';
        string += this.audioButton(file, source);
        string += this.audioInfo(file);
        string += '</div>';
    } else if (part == "button") {
        string += this.audioButton(file, source);
    } else if (part == "info") {
        string += this.audioInfo(file);
    } else if (part == 'timeline') {
        string += this.audioTimeline();
    }
    string += "</div>";
    return string;
};

Application.prototype.file.audioButton = function(file, source) {
    var string = '<div class="audio_button">';
    if (source === true) {
        string += '<audio style="display:none;"><source src="' + file.path + '"></source><source src="'
                + file.thumb_path + '"></source></audio>';
    }
    string += '<div class="audio_button_inside"></div><div class="audio_loader"><div class="loader_outside"></div><div class="loader_inside">'
            + '</div><span class="audio_loader_text loader_text"></span></div></div>';
    return string;
};

Application.prototype.file.audioInfo = function(file) {
    return '<div class="audio_info"><div class="ellipsis_overflow audio_title">' + file.name + '</div>'
            + this.audioTimeline() + '<div class="audio_time">0:00</div></div>';
};

Application.prototype.file.audioTimeline = function() {
    return '<div class="audio_progress_container"><div class="audio_progress"></div><div class="audio_buffered">'
            + '</div><div class="audio_line"></div></div>';
};

Application.prototype.file.audioPlay = function(id, start, progress, end, uid) {
    if (!$('[uid="' + uid + '"] .audio_button').hasClass('audio_playing')) {
        this.startAudioInfo(id, start, progress, end, uid);
        $('[uid="' + uid + '"] .audio_button').addClass('audio_playing');
    } else {
        this.files[uid]['element'].pause();
        $('[uid="' + uid + '"] .audio_button').removeClass('audio_playing');
    }
};

Application.prototype.file.startAudioInfo = function(id, start, progress, end, uid) {
    if (uid in this.files) {
        if (typeof this.files[uid]['element'] != 'undefined') {
            this.files[uid]['element'].play();
            return;
        }
    }

    this.view(id);

    var headerControl = $("<div uid='" + uid + "'></div>");
    headerControl.append(this.audioPlayer(this.files[uid], 'all', true));
    $('.global_media_container').html(headerControl);

    $("[uid='" + uid + "'] .audio_loader").fadeIn();
    var audio = headerControl.find('audio');
    this.files[uid]['element'] = audio.get(0);
    this.files[uid]['element'].volume = 1;
    this.files[uid]['element'].play();
    audio.bind('loadedmetadata', function() {
        audio.bind('progress', function() {
            var track_length = audio.get(0).duration;
            var secs = audio.get(0).buffered.end(0);
            var progress = 0;
            if (secs > 0 && track_length > 0) {
                progress = (secs / track_length) * 100;
            }
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
            $('[uid="' + uid + '"] .audio_button').removeClass('audio_playing');
        });

        $('[uid="' + uid + '"] .audio_progress_container').click(function(e) {
            var x = $(this).offset().left;
            var width_click = e.pageX - x;
            var width = $(this).width();
            var percent_width = (width_click / width) * 100;
            $('[uid="' + uid + '"] .audio_progress').css('width', percent_width + "%");
            var secs = audio.get(0).duration;
            var new_secs = secs * (percent_width / 100);
            audio.get(0).currentTime = new_secs;
        });
    });

    start = typeof start !== 'undefined' ? start : function() {
    };
    progress = typeof progress !== 'undefined' ? progress : function() {
    };
    end = typeof end !== 'undefined' ? end : function() {
    };
    start();
};

Application.prototype.file.audioVolume = function(vol) {
    $('audio').each(function() {
        console.log($(this).attr('id'));
        $(this).get(0).volume = vol;
    });
};

Application.prototype.file.createWaveForm = function(uid, progress) {
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
};

Application.prototype.file.removeAudio = function(id) {
    $('#audio_container_' + id).remove();
};

Application.prototype.file.videoFrame = function(uid) {
    return "<div class='video_box'><video id='vid" + uid + "'></video></div>";
};

Application.prototype.file.videoPlayer = function(file, onload, onplay, onend) {
    var string = '';
    var attributes = {
        'id': 'vid' + file.uid,
        'class': 'video-js vjs-default-skin',
        'controls': ' ',
        'preload': 'auto',
    };
    var video = $("#vid" + file.uid);
    string += "<source src='" + file.mp4_path + "' type='video/mp4'></source>"
            + "<source src='" + file.flv_path + "' type='video/x-flv'></source>"
            + "<source src='" + file.webm_path + "' type='video/webm'></source>";
    //. "<source src='" + $original_path + "' type='video/avi'></source>";
    string += "<object data='" + file.mp4_path + "'>"
            + "<embed src='" + file.flv_path + "'>"
            + "</object>";
    video.append(string);
    video.attr(attributes);
    var player = videojs('vid' + file.uid);
    player.ready(function() {
        onload(player);
    });

    return video;
};

Application.prototype.file.videoPlay = function(id) {
    var file_id = id.replace(/[A-Za-z_$-]/g, "");
    this.view(file_id);
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
    }
}

Application.prototype.file.upload.drag = function(element, files) {
    var number = Math.floor(Math.random() * 10);
    element = $(element);
    Application.prototype.UI.progress.create(element, number);
    this.upload(files, function() {
    }, function(pgs) {
        Application.prototype.UI.progress.update(number, pgs);
    }, function(file) {
        removeProgress(number);
        element.css('background', "url('" + file.path + "')");
        element.addClass('upload_done');
    });
}

Application.prototype.file.theater.initiate = function(activity_id, file_id, properties) {
    var self = this;
    Application.prototype.file.view(file_id);
    if (self.active) {
        self.remove();
    }
    self.active = true;
    self.previousUrl = window.location;
    window.history.pushState('File', {}, 'files?f=' + file_id);
    self.background = $("<div class='background-overlay'></div>").click(function() {
        self.remove();
    });
    self.close_theater = $("<div class='close-theater'></div>").click(function() {
        self.remove();
    });
    self.theater_picture_container = $("<div class='theater-picture-container'></div>").click(function(e) {
        e.stopPropagation();
    });
    self.theater_info_container = $("<div class='theater-info-container'></div>");
    self.theater_info_padding = $("<div class='theater-info-padding'></div>");
    self.theater_info_container.append(self.theater_info_padding);
    self.theater_picture = $("<div class='theater-picture'></div>").click(function() {
        self.remove();
    });
    self.toggle_view = $("<div class='theater-info-toggle'></div>").click(function() {
        self.theater_picture.toggleClass('theater-info-container_active');
        self.theater_info_container.slideToggle("fast");
    });
    self.theater_wrapper = $("<div style='position:relative;text-align:center;'></div>").click(function(e) {
        e.stopPropagation();
    });

    self.theater_wrapper.append(self.toggle_view);
    self.theater_wrapper.append(self.close_theater);
    self.theater_wrapper.append(self.theater_picture_container);
    self.theater_wrapper.hover(function() {
        self.toggle_view.fadeIn();
        self.close_theater.fadeIn();
    }, function() {
        self.toggle_view.fadeOut();
        self.close_theater.fadeOut();
    });

    self.theater_picture.append(self.theater_wrapper);
    self.theater_picture_container.append(self.theater_info_container);

    $('body').append(self.background);
    $('body').append(self.theater_picture);
    $("body").css("overflow", "hidden");

    self.loader = $("<table id='load_popup' style='height:100%;width:100%;padding:200px;'><tr style='vertical-align:middle;'><td style='text-align:center;'><div class='loader_outside'></div><div class='loader_inside'></div></td></tr></table>");
    self.theater_picture_container.append(self.loader);

    self.adjust();

    $.post('Scripts/files.class.php', {action: "preview", file_id: file_id, activity_id: activity_id}, function(response) {
        response = $.parseJSON(response);
        var string = Application.prototype.feed.homify(response);
        self.theater_info_padding.append("<div>" + string + "</div>");
        var image = $('<img class="image" />');

        if (response.media[0].type == "Image") {
            $('<img/>').attr('src', response.media[0].path).load(function() {
                var picture_width = $(this).width();
                self.theater_picture_container.css('display', "block");
                self.theater_picture_container.css('background-image', "url('" + response.media[0].path + "')");
                image.attr('src', response.media[0].path);
                image.css('visibility', "hidden");
                self.loaded();
                self.theater_wrapper.css('display', 'inline-block');
            });
        }
        else {
            self.theater_picture_container.append(Application.prototype.file.videoFrame(response.media[0].uid));
            image = Application.prototype.file.videoPlayer(response.media[0], function(video) {
                video.play();
                setTimeout(function() {
                    self.adjust();
                }, 100);
                self.loaded();
            }, function() {
            }, function() {
            });
            self.theater_picture_container.addClass('theater_video');
        }
        self.theater_picture_container.append(image);
        // theater_info_container.mCustomScrollbar(SCROLL_OPTIONS);
    });
};

Application.prototype.file.theater.loaded = function() {
    this.adjust();
    this.loader.remove();
};

Application.prototype.file.theater.adjust = function() {
    //adjustSwitches();

    this.theater_picture.css('margin-top', "-" + this.theater_picture.height() / 2);
    Application.prototype.UI.resizeToMax(this.theater_picture_container.children('img:first'), 690, 85);

    // $('#theater-info-container').mCustomScrollbar("update");
};

Application.prototype.file.theater.remove = function() {
    if (this.active) {
        this.background.fadeOut(function() {
            $(this).remove();
        });
        this.theater_picture.animate({height: '0'}, this.removeTime, function() {
            $(this).remove();
        });
        $('body').css('overflow', 'auto');
        this.active = false;
        window.history.pushState('Close Theater', {}, this.previousUrl);
    }
};


/****************************************************
 * 1.4 User                                          *
 ****************************************************/

Application.prototype.user.setProfilePicture = function(file_id) {
    $.post('Scripts/user.class.php', {action: "profile_picture", file_id: file_id}, function(response) {
        Application.prototype.UI.removeDialog();
        window.location.reload();
    });
};

Application.prototype.user.showPhotoChoose = function() {
    var content = $("<div><table><tr><td><div class='upload_here'></div></td><td><div class='profile_picture_chooser' style='max-height:200px;overflow:auto;margin-left:20px;height:100%;' id='file_container'>Loading...</div></td></tr></table></div>");
    Application.prototype.UI.dialog(
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
    });
    Application.prototype.file.list('div#file_container', 'Image');
    $(document).on('click', '.profile_picture_chooser .file_item', function(event) {
        event.stopPropagation();
        var file = $(this).data('file');
        $('.upload_here').css('background-size', 'cover');
        $('.upload_here').css('background-image', 'url("' + file.thumb_path + '")');
        profile_picture_id = file.id;
    });
};

Application.prototype.user.preview.show = function(element, user_id) {
    if (this.showing === false) {
        this.remove();
        if (element.children('.user_preview_info').length === 0) {
            this.create(element, user_id);
        }
    }
};

Application.prototype.user.preview.create = function(element, user_id) {
    var self = this;
    var bg = "<div id='user_preview_initial_loader' style='width:100%;height:100px;background-image:url(Images/ajax-loader.gif);background-position:center;background-repeat:no-repeat;'></div>";
    var cont = $('<div id="user_preview_' + user_id + '" style="display:none;" class="user_preview_info">' + bg + '</div>');
    element.append(cont);
    setTimeout(function() {
        if ($('*[data-user_id="' + user_id + '"]:hover').length > 0) {
            $.post('Scripts/user.class.php', {action: "get_preview_info", id: user_id}, function(response) {
                self.fill(response);
            });
            cont.fadeIn(200);
            this.showing = true;
            self.align(element);
        }
    }, 500);
};

Application.prototype.user.preview.align = function(element) {
    $(document).off('scroll');
    $(document).on('scroll', function() {
        this.align(element);
    });
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

    $('.user_preview_info').append('<div class="' + arrow + '-border"></div>');
    $('.user_preview_info').append('<div class="' + arrow + '"></div>');
    var top = element.offset().top;
    var element_height = element.outerHeight(true);
    var scrolled_height = $(document).scrollTop();
    var top_total = top - scrolled_height - element_height / 2;
    var arrow_height = $('.' + arrow + "-border").outerHeight(true) / 2 - 2;
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
};

Application.prototype.user.preview.fill = function(response) {
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
            "<button onclick='window.location.assign(\"message?c=" + response[0] + "\");' class='pure-button-green smallest'><span>Message</span></button></div></td></tr>";
    user_string += "</table>";
    $('.user_preview_info').find('#user_preview_initial_loader').remove();
    $('.user_preview_info').append(user_string);
};

Application.prototype.user.preview.remove = function(mode, event) {
    if ($('.user_preview_info').length > 0) {
        this.showing = false;
        if (mode == "check mouse") {
            if (calculateDistance($('.user_preview_info'), event.pageX, event.pageY) > 300) {
                $('.user_preview_info').remove();
            }
        } else {
            $('.user_preview_info').remove();
        }
    }
};

Application.prototype.user.connect = function(element, user_id) {
    $.post('Scripts/user.class.php', {action: "connect", user_id: user_id}, function() {
        element.addClass('connect_button_invited');
    });
};

Application.prototype.user.connectAccept = function(invite_id) {
    $.post('Scripts/user.class.php', {invite_id: invite_id, action: "acceptInvite"}, function() {

    });
};

Application.prototype.user.getOnlineMass = function() {
    var users = new Array();
    $('[data-user_id]').each(function() {
        if ($.inArray($(this).data('user_id'), users) === -1) {
            users.push($(this).data('user_id'));
        }
    });
    if (typeof loggedIn != 'undefined' && users.length > 0) {
        $.post('Scripts/user.class.php', {action: "getOnlineMass", users: users}, function(response) {
            response = $.parseJSON(response);
            for (var prop in response) {
                if (response.hasOwnProperty(prop)) {
                    if (response[prop] == true) {
                        $('.online_status[data-user_id="' + prop + '"]').addClass('profile_online');
                    } else {
                        $('.online_status[data-user_id="' + prop + '"]').addClass('profile_offline');
                    }
                }
            }
        });
    }
};

Application.prototype.user.showInvite = function(group, id, group_id) {
    Application.prototype.UI.dialog(
            content = {
                type: "text",
                content: "Invite <em><?php echo $user->getName($userid); ?></em> to join the group <em>" + group + "</em>.</p>",
            },
            buttons = [{
                    type: "success",
                    text: "Invite",
                    onclick: function() {
                        this.inviteUser(id, group_id);
                        dialogLoad();
                    }
                }],
    properties = {
        modal: false,
        title: "Invite"
    });
};

Application.prototype.user.inviteUser = function(id, group_id) {
    $.post("Scripts/group_actions.php", {action: "invite", user_id: id, group_id: group_id}, function(response) {
        if (("success").indexOf(response))
        {
            removeDialog();
        }
        else
        {
            alert(response);
        }
    });
};

$(function()
{
    $('#about_edit_show').mouseenter(function() {
        $('#profile_about_edit').show();
    }).mouseleave(
            function() {
                $('#profile_about_edit').hide();
            });

    $('.profilepicture').mouseenter(function() {
        $('#profile_picture_edit').show();
    }).mouseleave(
            function() {
                $('#profile_picture_edit').hide();
            });
    $('#about_edit_show').blur(function()
    {
        submitData();
    });

    $("#about_edit_show").focusin(function() {
        $("#about_edit_show").css("background", "white");
    });
    $("#about_edit_show").focusout(function() {
        $("#about_edit_show").css("background", "");
    });
    //createMap('<?php echo $user->getLocation($userid)['country']; ?>', '<?php echo $user->getLocation($userid)['city']; ?>');
});
Application.prototype.user.submitData = function() {
    var about = $('#about_edit_show').html();
    var email = '';
    var school = '';
    var year = '';
    $.post('Scripts/user.class.php', {about: about, email: email, year: year}, function(response) {
        $('#about_saved').fadeIn(function() {
            $('#about_saved').fadeOut(1000);
        });
    });
};

/****************************************************
 * 1.5 Feeds                                         *
 ****************************************************/

Application.prototype.feed.get = function(entity_id, entity_type, min_activity_id, activity_id, callback) {
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
    });
};

Application.prototype.feed.getContent = function(feed_id, min_activity_id, page, callback) {
    var link = page;
    if (page == "user") {
        link = "home"
    }

    else if (page == "user_files") {
        link = 'user';
        page = 'f';
    }

    if (typeof feed_id !== "undefined") {
        var container = $('.container').find('.feed_container');


        var none = $("<div class='post_height_restrictor contentblock'>"
                + "<center><p style='color:grey;'>"
                + "There are no notifications in this Live Feed! <br /> "
                + "You can post up the in the box.</p></center></div>");

        var prev_feed_id = getCookie(page + '_feed');
        if (feed_id != prev_feed_id) {
            min_activity_id = 0;
            container.find('.post_height_restrictor').remove();
        } else {
            none = "";
        }
        setCookie(page + '_feed', feed_id);
        modal(container, properties = {text: "Loading content..."})
        var callbak = function(data) {
            container.prepend(data);
            if (data.length < 100) {
                container.append(none);
            }
            removeModal();

            callback();
            refreshVideoJs();
        }

        if (isNaN(feed_id)) {
            if (feed_id == 'a' || feed_id == 's' || feed_id == 'y') {
                refreshElement('#feed_refresh', link, "f=" + feed_id + "&min_activity_id=" + min_activity_id, "#feed_refresh", callbak);
            }
            else {
                feed_id = feed_id.replace('u_', '');
                refreshElement('#feed_refresh', link, "u=" + feed_id + "&min_activity_id=" + min_activity_id, "#feed_refresh", callbak);
            }
        } else {
            refreshElement('#feed_refresh', link, "fg=" + feed_id + "&min_activity_id=" + min_activity_id, "#feed_refresh", callbak);
        }
    }
};

Application.prototype.feed.changeTheaterFeed = function(view) {
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
};

Application.prototype.feed.post.submit = function() {
        var text = $('#status_text').val();
        if (text != "" || this.files.length != 0) {
            $.post("Scripts/home.class.php", {action:"update_status", status_text: text, group_id: share_group_id, post_media_added_files: this.files}, function(data)
            {
                if (data == "") {
                    removeModal('', function() {
                        Application.prototype.feed.get(share_group_id, null, 0, min_activity_id, activity_id, function(response){
                    		var string = '';
                    		for (var i in response) {
                                    string += Application.prototype.feed.homify(response[i]);
                    		}
                    		$('.feed_container').prepend(string);
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
};

Application.prototype.feed.post.addFile = function(object, activity_id) {
        $('.post_media_wrapper_background').hide();
        var post_media_classes = '';
        var post_media_style = " style=' ";
        var text_to_append = '';
        var additional_close = '';
        var extra_params = " post_file_id='" + object.file_id + "' ";
        //text_to_append += ">"; 
        text_to_append += Application.prototype.file.print(object, 'add_to_status');
        if (object.type == "Image")
        {
            post_media_classes += "post_media_photo post_media_full";
            additional_close += " post_media_single_close_file";
        } else if (object.type == "Audio")
        {
             post_media_classes += " post_media_item post_media_full";
             additional_close += " post_media_single_close_file";

        } else if (object.type == "Video") {
            post_media_classes += " post_media_video";
            additional_close += " post_media_single_close_file";
            //text_to_append += video_player(object, 'classes', 'styles', 'added_to_status_', true);

        } else if (object.type == "Webpage") {
            post_media_classes += " post_media_double";
            post_media_style += "height:auto;";
            additional_close += " post_media_single_close_webpage";
            extra_params = " post_file_id='" + object.path + "' ";
            text_to_append += "><table style='height:100%;'><tr><td rowspan='3'>" +
                    "<div class='post_media_preview' style='background-image:url(&quot;" + object.info.favicon + "&quot;);'></div></td>" +
                    "<td><div class='ellipsis_overflow' style='position:relative;margin-right:30px;'>" +
                    "<a class='user_preview_name' target='_blank' href='" + object.path + "'><span style='font-size:13px;'>" + object.info.title + "</span></a></div></td></tr>" +
                    "<tr><td><span style='font-size:12px;' class='user_preview_community'>" + object.info.description + "</span></td></tr></table>";
        } else if (object.type == "Folder") {
//            text_to_append += documentStatus(object.path, object.name, object.description, FOLDER_THUMB);
        } else if (object.type == "WORD Document") {
        } else if (object.type == "PDF Document") {
        } else if (object.type == "PPT Document") {
        } else {
        }
        if (object.type == "WORD Document" 
                || object.type == "PDF Document" 
                || object.type == "PPT Document" 
                || object.type == "ACCESS Document" 
                || object.type == "EXCEL Document"
                || object.type == "Folder") {
            post_media_classes += " post_media_double";
            post_media_style += "height:auto;";
            additional_close += " post_media_single_close_file";
            extra_params = " post_file_id='" + object.id + "' ";
        }
        var index;
        for (var i = 0; i < this.files.length; i++)
        {
            if (this.files[i].id == object.id || this.files[i] == object.id)
            {
                index = "found";
                Application.prototype.UI.dialog(
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
        if (index != "found")
        {
            if (object.type == "Webpage") {
                this.files.push(object);
            } else {
                this.files.push(object.id);
            }
            $('.post_media_wrapper').append(text_to_append);
            if(object.type == "Video") {
                refreshVideoJs();
            }
        }

        if (this.files.length > 1) {
            $('#status_text').attr('placeholder', 'Write about these files...');
        }
        else {
            $('#status_text').attr('placeholder', 'Write about this file...');
        }
        $('#status_text').focus();
        $('#file_share').mCustomScrollbar("update");
    };
Application.prototype.feed.post.removeFile = function(object)
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
        } else {
            var element = $('.post_media_wrapper [data-file_id="' + object.value + '"]');
            element.remove();
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
    };

Application.prototype.feed.createPost = function(text, files, activity_id) {
    var string = "<div class='home_feed_post_container'><div class='home_feed_post_container_arrow_border'>";
    string += "<div class='home_feed_post_container_arrow'></div>";
    string += "</div>";
    string += "<div class='post_wrapper'>";
    string += "<table style='width:100%;' cellspacing='0' cellpadding='0'>";
    string += "<tr><td><table style='width:100%;' cellspacing='0' cellpadding='0'>";
    string += "<tr style='height:100%;'><td><textarea tabindex='1' placeholder= 'Update Status or Share Files...' class='status_text scroll_thin'>" + text + "</textarea>";
    string += "</td></tr><tr><td class='post_content_wrapper'>";
    string += "<div class='post_media_wrapper'>";
    string += "<div class='post_media_wrapper_background timestamp' style='text-align:left;'><span>Dropbox</span></div>";
    string += "<img class='post_media_loader' src='Images/ajax-loader.gif'></img> </div></td></tr></table></td>";
    string += "<td style='width:00px;height:100%;position: relative;'><div id='file_share'>";
    string += "<table id='file_dialog' style='width:100%;' cellspacing='0' cellpadding='0'>";
    string += "<div class='home_feed_post_container'></table></div>";
    string += "</td></tr></table><div id='post_more_options' class='post_more_options'>";
    string += "<button class='pure-button-green small submit_post'><span>Post</span></button>";
    string += "</div></div></div></div></div>";
};

Application.prototype.feed.homify = function(activity) {
    activity.stats.like.count = parseInt(activity.stats.like.count);
    activity.status_text = typeof activity.status_text !== 'undefined' && !isEmpty(activity.status_text) ? activity.status_text : '';
    var string = "";
    if (activity.view == 'home') {
        string += "<div data-activity_id='" + activity.id + "' class='post_height_restrictor contentblock' id='post_height_restrictor_" + activity.id + "'>";
        string += '<div id="single_post_' + activity.id + '" class="singlepostdiv">';
        string += "<div id='" + activity.id + "'>";
        string += "<div class='top_content'><a class='user_name_post' href='user?id=" + activity.user.encrypted_id + "'>";
        string += "<div class='profile_picture_medium' style='background-image:url(\"" + activity.user.pic + "\");'></div></a>";
        string += "<a class='user_name_post user_preview user_preview_name' user_id='" + activity.user.id
                + "' href='user?id=" + activity.user.encrypted_id + "'>";
        string += activity.user.name + "</a>";
        if (!Application.prototype.user.isMobile) {
            string += this.printStats(activity);
        }
        string += "</div>"; // CLOSE top_content
        if (activity.type == "Text" || activity.type == "File") {
            if (!Application.prototype.user.isMobile) {
                string += "<hr class='post_user_name_underline'>";
            }
        }
        string += "<div class='post_content_wrapper'>";
        string += "<p class='post_text'>" + activity.status_text + '</p>';

        if (activity.media.length > 0) {
            string += "<div class='post_feed_media_wrapper'>";
            for (var i in activity.media) {
                string += Application.prototype.file.print(activity.media[i], activity.type);
            }
            string += "</div>";
        }
        if (Application.prototype.user.isMobile) {
            string += "<div class='activity_stats_mobile'>";
            string += this.printStats(activity);
            string += "</div>";
        }
        string += '<div class="comment_box">';
        string += '<div class="comment_box_comment">';
        string += this.comment.showComments(activity);
        string += "</div>";
        string += this.comment.printInput(activity.id);
        string += "</div>";

        string += "</div></div></div></div>";
    }
//        else if($view == "preview") {
//            string = '';
//            string += "<table class='singleupdate'><tr>"
//            + "<td class='updatepic' style='max-width:50px;'>";
//            string += "<a class='user_name_post' href='user?id=" + urlencode(base64_encode(activity['user_id'])) + "'>";
//            string += "<div class='imagewrap' style='background-image:url(\""
//            + Registry::get('user')->getProfilePicture("thumb", activity['user_id'])
//            + "\");'></div></a></td><td class='update'>";
//            string += "<a class='user_name_post user_preview user_preview_name' user_id='" + activity['user_id']
//            + "' href='user?id=" + urlencode(base64_encode(activity['user_id'])) + "'>";
//            string += Registry::get('user')->getName(activity['user_id']) + "</a>";
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

Application.prototype.feed.comment.printInput = function(activity_id) {
    var string = '';
    string += "<div class='comment_input' style='padding-left:2px;padding-top:2px;'>";
    string += "<table style='width:100%;'>";
    string += "<tr><td style='vertical-align:top;width:40px;'>";
    string += "<div class='profile_picture_medium' style='background-image:url(\"" + USER_PIC + "\");'>";
    string += "</div></td><td cellspacing='0' style='vertical-align:top;'>";
    string += '<textarea placeholder="Write a comment..." ';
    string += 'class="home_comment_input_text inputtext" id="comment_' + activity_id;
    string += '"></textarea>';
    string += "</td></tr></table>";
    string += "</td></tr></table></div>";
    return string;
};

Application.prototype.feed.comment.showComments = function(activity) {
    this.comments[activity.id] = new Array();
    var string = '';

    if (activity.comment.format == 'top') {
        string += "<div class='activity_actions user_preview_name post_comment_user_name' style='font-weight:100;'>Show <span class='num_comments'>" + activity.comment.hidden + "</span> more comments...</div>";
    }
    for (var i in activity.comment.comment) {
        this.comments[activity.id].push(activity.comment.comment[i].id);
        string += this.show(activity.comment.comment[i]);
    }

    this.comments[activity.id]['max'] = Array.max(this.comments[activity.id]);
    this.comments[activity.id]['min'] = Array.min(this.comments[activity.id]);
    return string;
};

Application.prototype.feed.comment.show = function(comment) {
    var string = '';
    comment.like.like_text = (comment.like.has_liked ? "Unlike" : "Like");
    string += "<div class='single_comment_container' data-comment_id='" + comment.id + "'>";
    string += "<table style='font-size: 0.9em;'><tr><td style='vertical-align:top;' rowspan='2'>";
    string += "<div class='profile_picture_medium' style='background-image:url(\"" + comment.user.pic + "\");'></div></td><td style='vertical-align:top;'>";
    string += "<a class='userdatabase_connection' href='user?id=" + comment.user.encrypted_id + "'>";
    string += "<span class='user_preview user_preview_name post_comment_user_name' user_id='" + comment.user.id + "'>" + comment.user.name + " </span></a>";
    string += "";
    string += "<span class='post_comment_text'>" + comment.text + "</span>"
    string += "</td></tr><tr><td colspan=2 style='vertical-align:bottom;' >"
    string += "<span class='post_comment_time'>" + comment.time + " -</span>"
    string += "<span class='post_comment_time post_comment_liked_num'>"
    string += comment.like.count + "</span><i class='fa fa-heart heart_like_icon'></i>";
    string += "<span data-has_liked='" + comment.like.has_liked + "' "
    string += "class='user_preview_name post_comment_time post_comment_vote'>"
    string += comment.like.like_text + "</span>";
    string += "</tr></table>";
    if (comment.user.id == USER_ID) {
        string += "<img height='15px'src='../Images/Icons/Icon_Pacs/typicons.2.0/png-48px/delete-outline.png' class='comment_delete'></img>";
    }
    string += "</div>";
    return string;
};

Application.prototype.feed.comment.append = function(post_id, comment) {
    if ($('[data-comment_id="' + comment.id + '"]').length == 0) {
        var comment_container = $('[data-activity_id="' + post_id + '"]').find('.comment_box_comment').last();
        comment_container.append(this.show(comment));
    }
//    console.log('POST ID:' + post_id + " COMMENT: " + comment);
    // $("[data-activity_id='" + post_id + "'] [data-comment_id]").sort(function(left, right) {
    //     return parseInt($(right).data("comment_id")) - parseInt($(left).data("comment_id"));
    // }).each(function() {
    //     $("[data-activity_id='" + post_id + "']").find('.comment_box_comment').last().append($(this));
    //     //console.log('fe');
    // });
};

Application.prototype.feed.printStats = function(activity) {
    var string = '';
    string += "<div class='activity_stats'>";
    string += print_likes(activity);
    string += "<span class='post_comment_time'> " + activity.time + " |</span>";
    if (activity.type == "File") {
        activity.media[0] = (activity.media[0] || new Object());
        activity.media[0].view = new Object();
        activity.media[0].view.count = (activity.media[0].view.count || 0);
        string += "<span class='post_comment_time'>| <span class='post_view_count'>" + activity.media[0].view.count + "</span> views</span>";
    }

    if (activity.user.id == USER_ID) {
        string += "<div class='default_dropdown_actions' style='display:inline-block;' wrapper_id='activity_options_" + activity.id + "'>";
        string += "<i class='fa fa-angle-down'></i>";
        string += "<div class='default_dropdown_wrapper' id='activity_options_" + activity.id + "'>";
        string += "<ul class='default_dropdown_menu'>";
        string += "<li class='default_dropdown_item delete_activity' controller_id='activity_options_";
        string += activity.id + "'>Delete";
        string += "</li>";
        string += "<li class='default_dropdown_item edit_activity'>Edit</li>";
        string += "</ul>";
        string += "</div>";
        string += "</div>";
    }
    string += "</div>";
    return string;

    function print_likes(activity) {
        var string = '';
        string += '<div class="who_liked_hover" ';
        string += 'style="display:inline;"> ';
        string += '<span class="post_comment_time post_like_count">' + activity.stats.like.count + '</span>';
        string += '<i class="fa fa-heart heart_like_icon"></i>';
        string += '<div style="display:inline;">';
        string += '<span has_liked="';
        string += (activity.stats.like.has_liked === true ? "true" : "false");
        string += '" class="post_comment_time user_preview_name activity_like_text post_like_activity">';
        string += (activity.stats.like.has_liked === true ? COMMENT_UNLIKE_TEXT : COMMENT_LIKE_TEXT) + '</span><span class="post_comment_time">|</span></div>';
        string += "</span>";
        string += '<div class="who_liked" id="who_liked_' + activity.id + '">';
        for (var i = 0; i < activity.stats.like.count; i++) {
            name = activity.stats.like.user[i].name;
            if (i == 1) {
                string += name;
            }
            else {
                string += ",<br>" + name;
            }
        }
        if (activity.stats.like.count == 0) {
            string += "No one has liked this post yet.";
        }
        string += "</div></div>";
        return string;
    }
}

Application.prototype.feed.refreshContent = function(id) {
    return;
    Application.prototype.feed.get(null, null, null, id, function(response) {
        var activity_container = $('[data-activity_id="' + id + '"]');
        var comment_container = activity_container.find('.comment_box_comment');
        for (var i in response) { // FOR THAT ONE ACTIVITY

            for (var key in response[i].comment.comment) {
                append_comment(response[i].id, response[i].comment.comment[key]);
            }

            for (var media_id in response[i].media) {
                for (var key in response[i].media[media_id].activity.comment) {
                    append_comment(response[i].media[media_id].activity.id, response[i].media[media_id].activity.comment.comment[key]);
                }

            }
        }
    });
}

function isEmpty(input) {
    if (input == "null" || input == "" || input == null) {
        return true;
    } else {
        return false;
    }
}

/****************************************************
 * 1.8 Chat                                         *
 ****************************************************/
Application.prototype.feed.comment.submit = function(comment_text, post_id, callback) {
    comment_text = comment_text.replace(/^\s+|\s+$/g, "");
    if (comment_text == "") {
        comment_text = $('div[actual_id="comment_' + post_id + '"]').val();
        comment_text = comment_text.replace(/^\s+|\s+$/g, "");
        if (comment_text == "") {
            return;
        }
    }

    $.post("Scripts/home.class.php", {comment_text: comment_text, post_id: post_id, action: 'submitComment'}, function(data) {
        $('[data-activity_id="' + post_id + '"] .inputtext').each(function() {
            if ($(this).parents('[data-activity_id]').data('activity_id') == post_id) {
                $(this).val("").blur();
            }
        });
        data = $.parseJSON(data);
        callback(data);
    });
};
Application.prototype.chat.iniScroll = function() {
    var self = this;
    $('.chatoutput').on('scroll', function() {
        self.room[$(this).data('chat_room')].bottom = false;
        if ($(this).get(0).scrollTop + 20 >= $(this).get(0).scrollHeight - $(this).get(0).offsetHeight) {
            self.room[$(this).data('chat_room')].bottom = true;
        } else if ($(this).get(0).scrollTop == 0) {
            self.getPrevious($(this).data('chat_room'));
        }
    });
};

Application.prototype.chat.detectChange = function() {
    var key_height = $('.chatinputtext').outerHeight(true);
    var bottom = key_height;
    $('.chatoutput').css('bottom', bottom + "px");
    this.scroll2Bottom(false, chat_room); //USE EVEN IF NOT LOADED
};

Application.prototype.chat.getPrevious = function(chat_index) {
    var self = this;
    if (self.room[chat_index].getting_previous == false) {
        self.room[chat_index].getting_previous = true;

        var new_oldest = Array.min(self.room[chat_index].entry) - 20;
        if (new_oldest < 0) {
            new_oldest = 0;
        }

        if (self.room[chat_index].last_chat != true) {
            var element = $('[data-chat_room="' + chat_index + '"] .single_chat:first');
            $('[data-chat_room="' + chat_index + '"] .chat_loader').slideDown('fast');
            var object = {chat: chat_index, all: "previous", oldest: new_oldest, newest: self.room[chat_index].oldest - 1};
            $.get("Scripts/chat.class.php", object, function(response) {
                response = $.parseJSON(response);
                if (response.length == 0) {
                    self.room[chat_index].last_chat = true;
                    $('[data-chat_room="' + chat_index + '"] .chatreceive').prepend("<div class='timestamp'><span>Start of Conversation</span></div>");
                    $('[data-chat_room="' + chat_index + '"] .chat_loader').slideUp('fast');
                    return;
                }
                $('[data-chat_room="' + chat_index + '"] .chatreceive').prepend(self.styleResponse(response, chat_index));
                $('[data-chat_room="' + chat_index + '"] .chat_loader').slideUp('fast');
                self.room[chat_index].getting_previous = false;
                element = element.offset().top;
                $('[data-chat_room="' + chat_index + '"]').scrollTop(element);
            });
        }
    }
};

Application.prototype.chat.sendRequest = function(all, chat_index) {
    var self = this;
    $.get("Scripts/chat.class.php", {chat: chat_index, all: all, oldest: 0, newest: this.room[chat_index].newest}, function(response) {
        $('[data-chat_room="' + chat_index + '"] .chat_loader').slideUp('fast');
        response = $.parseJSON(response);

        if (all == 'true') {
            $('.chatcomplete').fadeIn("fast");
            $('[data-chat_room="' + chat_index + '"] .chatreceive').append(self.styleResponse(response, chat_index));
            $('[data-chat_room="' + chat_index + '"] .chatoutput').scrollTop($('[data-chat_room="' + chat_index + '"] .chatoutput').get(0).scrollHeight);

        } else {
            $('[data-chat_room="' + chat_index + '"] .chatreceive').append(self.styleResponse(response, chat_index));
        }
        if (all == 'false' && response.length > 0) {
            for (var i = response.length - 1; i >= 0; i--) {
                if (response[i]['user_id'] != USER_ID) {
                    $('#chat_new_message_sound').get(0).play();
                }
            }
            if (chat_index != chat_room) {
                $('.chat_feed_selector[chat_feed="' + chat_index + '"] *').css('color', 'red');
                alert('unread in another chat');
            }
        }
        var timeout = 3000;
        if (chat_index != chat_room) {
            timeout = 10000;
        }
        setTimeout(function() {
            self.sendRequest('false', chat_index);
        }, timeout);
        self.detectChange();
    });
};

Application.prototype.chat.styleResponse = function(response, chat_index) {
    var string = '';
    if (response.length == 0) {

    }
    for (var i = response.length - 1; i >= 0; i--) {
        if (response[i]['type'] != 'event') {
            string += "<li class='single_chat'><div class='chat_wrapper'><table cellspacing='0' cellpadding='0' style='width:100%;'><tr><td style='width:50px;padding-right:5px;'>";
            string += "<div data-user_id='" + response[i]['user_id'] + "' class='profile_picture_medium online_status' style='float:left;";
            string += "background-image:url(\"" + response[i]['pic'] + "\");'></div></td><td>";
            string += "<div class='chatname'><span class='user_preview user_preview_name chatname' style='margin-right:5px;font-size:13px;' user_id='" + response[i]['user_id'] + "'>" + response[i]['name'] + "</span></div>";
            string += "<div class='chattext'>" + response[i]['text'].replaceLinks().replaceEmoticons() + "</div></td></tr><tr><td colspan='2' style='text-align:right;'>";
            string += "<span class='chat_time post_comment_time'>" + response[i]['time'] + "</span></td></tr></table></div></li>";
            if ($.inArray(response[i]['id'], this.room[chat_index].entry) !== -1) {
                return;
            }
            this.room[chat_index].entry.push(response[i]['id']);
        } else {
            if (response[i]['code'] == 0) {
            } else {
                string += "<li class='single_chat'><div class='chat_wrapper'><table cellspacing='0' cellpadding='0' style='width:100%;'><tr><td style='width:50px;padding-right:5px;'>";
                string += "<div class='chattext'>" + response[i]['text'] + "</div></td></tr><tr><td colspan='2' style='text-align:right;'>";
            }
        }
    };

    this.room[chat_index].newest = Array.max(this.room[chat_index].entry);
    this.room[chat_index].oldest = Array.min(this.room[chat_index].entry);
    return string;
};

Application.prototype.chat.scroll2Bottom = function(force, chat_index) {
    if (this.room[chat_index].bottom === true || force === true) {
        $('.chatoutput[data-chat_room="' + chat_index + '"]').scrollTop($('.chatoutput[data-chat_room="' + chat_index + '"]')[0].scrollHeight);
//         console.log("Scrolling to " + $('.chatoutput[data-chat_room="' + chat_index + '"]')[0].scrollHeight + "px");
    }
};

Application.prototype.chat.submit = function(chat_text, chat_index) {
    var self = this;
    if (chat_text != "") {
        $('.chatinputtext').val('');
        $('.chatinputtext').attr('placeholder', "Sending...");
        $('.chatinputtext').attr('readonly', 'readonly');
        $.post("Scripts/chat.class.php", {action: "addchat", aimed: chat_index, chat_text: chat_text}, function(response) {
            response = $.parseJSON(response);
            $('[data-chat_room="' + chat_index + '"] .chatreceive').append(self.styleResponse(response, chat_index));
            $('.chatinputtext').removeAttr('readonly');
            $('.chatinputtext').attr('placeholder', "Press Enter to send...");
            self.room[chat_index].bottom = true;
        });
    }
};

//function change_chat_view(change_view) {
//    $('.chat_feed_selector[chat_feed="' + change_view + '"] *').css('color', 'black');
////            $('.chat_loader').slideDown('fast');
//    $('.chatoutput').hide();
//    $('[data-chat_room="' + change_view + '"]').show();
////            clearTimeout(timer);
//    chat_room = change_view;
//    //current_view = change_view;
//    scroll2Bottom(true, chat_room);
//    setCookie('chat_feed', change_view, 5);
//}

function calculateDistance(elem, mouseX, mouseY)
{
    return Math.floor(Math.sqrt(Math.pow(mouseX - (elem.offset().left + (elem.width() / 2)), 2) + Math.pow(mouseY - (elem.offset().top + (elem.height() / 2)), 2)));
}

function deleteMessage(thread) {
    $.post('Scripts/notifications.class.php', {action: "deleteMessage", thread: thread}, function(response) {
    });
    $('#inbox_message_' + thread).remove();
}


function implode(glue, pieces) {
    var i = '',
            retVal = '',
            tGlue = '';
    if (arguments.length === 1) {
        pieces = glue;
        glue = '';
    }
    if (typeof pieces === 'object') {
        if (Object.prototype.toString.call(pieces) === '[object Array]') {
            return pieces.join(glue);
        }
        for (i in pieces) {
            retVal += tGlue + pieces[i];
            tGlue = glue;
        }
        return retVal;
    }
    return pieces;
}

/****************************************************
 * 1.7 Calendar                                      *
 ****************************************************/

Application.prototype.calendar.print = function(calendar) {
    var string = '';
    string = '<div class="contentblock box_container"><h3><button  style="float:left !important; display:inline-block;" class="navigate_left">'
            + '<i class="fa fa-angle-left"></i></button>'
            + calendar.date + '<button  style="float:left !important; display:inline-block;" class="navigate_right"><i class="fa fa-angle-right"></i></button>'
            + '<a href="event"><button class="pure-button-neutral" id="create_event">Create Event</button></a></h3><table cellpadding="0" cellspacing="0" class="calendar">';

    var headings = new Array('Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat');
    string += '<tr class="calendar-row"><td class="calendar-day-head">' + implode('</td><td class="calendar-day-head">', headings) + '</td></tr>';
	
    var days_in_month = calendar.days_in_month;
    var days_in_this_week = 1;
    var day_counter = 0;
    var d = new Date();
    var dates_array = new Array();
    var curr_date = d.getDate();
    var curr_month = d.getMonth() + 1; //MONTHS ARE 0 BASED
    var curr_year = d.getFullYear();
    var curr_day = curr_year + "-" + curr_month + "-" + curr_date;

    string += '<tr class="calendar-row calendar-events-row">'; //ROW FOR WEEK 1

    //PRINT BLANKS UNTIL FIRST DAY IN MONTH
    for (var x = 0; x < calendar.running_day; x++) {
        string += '<td class="calendar-day-np">&nbsp;</td>';
        days_in_this_week++;
    }

    for (list_day = 1; list_day <= days_in_month; list_day++) {
        event_day = calendar.year + '-' + calendar.month + '-' + ('0' + list_day).slice(-2);
        string += '<td class="calendar-day' + (event_day == curr_day ? "-current" : "") + ' "><div>';
        string += '<div class="day-number">' + list_day + '</div>';
        for (var event in calendar.event) {
            if (calendar.event[event].event_day == event_day) {
                string += this.event.print(calendar.event[event]);
            }
        }
        string += '</div></td>';
        if (calendar.running_day == 6) {
            string += '</tr>';
            if ((day_counter + 1) != days_in_month) {
                string += '<tr class="calendar-row  calendar-events-row">';
            }
            calendar.running_day = -1;
            days_in_this_week = 0;
        }
        days_in_this_week++;
        calendar.running_day++;
        day_counter++;
    }

    if (days_in_this_week < 8) {
        for (var x = 1; x <= (8 - days_in_this_week); x++) {
            string += '<td class="calendar-day-np">&nbsp;';
            string += "</td>";
        }
    }
    string += '</tr>';
    string += '</table></div>';
    return string;
};

Application.prototype.calendar.getEvents = function(limit, callback) {
    $.get('Scripts/calendar.class.php', {action: "get_events", limit: limit}, function(response) {
        response = $.parseJSON(response);
        callback(response);
    });
};

Application.prototype.calendar.event.print = function(event, classes) {
    var string = '';
    var file_string = '<table>';

    for (var file in event.file) {
//        console.log(event.file[file]);
        file_string += '<tr><td>';
        file_string += "<div class='profile_picture_icon' style='vertical-align:top;display:inline-block;background-image: url(\""
                + event.file[file].preview_thumb + "\");'></div>";
        file_string += "</td><td><a href='" + event.file[file].path
                + "' download><p style='margin:0px;padding:0px;max-width:120px;' class='ellipsis_overflow'>"
                + event.file[file].name + "</p></a><br /></td></tr>";
    }
    file_string += "</table>";
    string += '<div class="contentblock event ' + classes + '"><a href="event?e=' + event.id + '"><b> ' //style="background-color:' + event.color + '"
            + event.title + ' </b></a><div class="calendar-event-info"><span>'
            + event.description
            + "</span>";
    if (event.file.length > 0) {
        string += "<div class='calendar-event-info-files'>" + file_string + "</div>";
    }
    string += '<p style="margin:0px;padding:0px;">' + event.start
            + '</p></div></div>';
    return string;
}

/****************************************************
 * 1.8 Stickies                                      *
 ****************************************************/

function doneTyping(text) {
    $.post('Scripts/user.class.php', {action: "update", id: 1, html: text}, function(response) {
    });
}

/****************************************************
 * 2. EventHandlers                                  *
 ****************************************************/

/****************************************************
 * 2.1 Startup                                       *
 ****************************************************/
$(function() {

    /****************************************************
     * 2.1.1 Startup - Generic                           *
     ****************************************************/

    $(document).on('click', 'a[href!="#"][href]:not([download], .no-ajax)', function(e) {
        Application.prototype.generic.relocate(e, $(this));
    });

    window.onpopstate = function(event) {
        var state = event.state;
        if (state) {
            console.log(event);
        }
    };

    Application.prototype.UI.adjustSwitches();

    $(window).on('resize', function() {
        Application.prototype.UI.resizeContainer();
    });

    $(document).on('click', '.headerbar', function() {
        $('.global_container').toggleClass('global_container_menu');
    });

    $(document).on('click', '.delete_receiver', function() {
        var type = $(this).parents('[entity_type]').attr('entity_type');
        var id = $(this).parents('[entity_id]').attr('entity_id');
        var receivers_type = $(this).parents('[search_type]').attr('search_type');
        if (receivers_type == "event") {
            event_receivers = removereceiver(type, id, event_receivers);
        } else if (receivers_type == "group") {
            group_receivers = removereceiver(type, id, group_receivers);
        } else if (receivers_type == "message") {
            message_receivers = removereceiver(type, id, message_receivers);
        }
        $(this).parents('[entity_type]').remove();
    });

    $(document).keypress(function(e) {
        if (e.which === 32) {
        }
    });

    $(document).on('click', '.switch_option', function() { //SWITCH UI
        var container = $(this).parents('.switch_container');
        container.find('.switch_option').removeClass('switch_selected');
        $(this).addClass('switch_selected');
    });

    /****************************************************
     * 2.1.1.1 Startup - Generic - Dropdown                *
     ****************************************************/
    $(document).on('click', ".default_dropdown_selector", function(event) {
            event.stopPropagation();
            $('.default_dropdown_wrapper').hide();
            var wrapper = '#' + $(this).attr('wrapper_id');
            $(wrapper).toggle();
            $(wrapper).mCustomScrollbar(SCROLL_OPTIONS);
            $(this).toggleClass('default_dropdown_active');
    });

    $(document).on('click', "html", function(event) {
            $('.default_dropdown_selector').removeClass('default_dropdown_active');
            $('.default_dropdown_wrapper').hide();
    });

    $(document).on('click', ".default_dropdown_selector .default_dropdown_item", function(event) {
            event.stopPropagation();
            var selection_value = $(this).attr('value');
            var wrapper = $(this).parents('.default_dropdown_wrapper');
            $(this).parents('.default_dropdown_selector').removeClass('default_dropdown_active');
            wrapper.find('.default_dropdown_item').removeClass('default_dropdown_active');
            $(this).addClass('default_dropdown_active');
            $(this).parents('.default_dropdown_selector').attr('value', selection_value).find('.default_dropdown_preview').text($(this).text());
    });

    $('.default_dropdown_selector').on({
        focus: function() {
            if (!$(this).hasClass('default_dropdown_active')) {
                $(this).trigger('click');
            }
        },
        blur: function() {
            $('html').trigger('click');
        }
    });

    $(document).on('click', ".default_dropdown_actions", function(event)
    {
        event.stopPropagation();
        $('.default_dropdown_wrapper').hide();
        var wrapper = '#' + $(this).attr('wrapper_id');
        $(wrapper).toggle();
        $(wrapper).mCustomScrollbar(SCROLL_OPTIONS);
    });

    /****************************************************
     * 2.1.2 Startup - User                              *
     ****************************************************/

    Application.prototype.user.getOnlineMass();
    setInterval(Application.prototype.user.getOnlineMass, 15000);

    $(document).on('click', '.connect_button', function() {
        Application.prototype.user.connect($(this), $(this).parents('[entity_id]').attr('entity_id'));
    });

    $(document).on('click', '.connect_accept', function() {
        Application.prototype.user.connectAccept($(this).data('invite_id'));
    });

    $(document).on('mouseover', '.user_preview', function(event) {
        event.stopPropagation();
        var user_id = $(this).data('user_id');
        Application.prototype.user.preview.show($(this), user_id);
    });

    $(document).on('mouseover', "html", function(event) {
        Application.prototype.user.preview.remove('check mouse', event);
    });

    /****************************************************
     * 2.1.3 Startup - Search                            *
     ****************************************************/

    $(document).on('keyup', '.search', function(e) {
        e.stopPropagation();
        Application.prototype.search($(this).val(), $(this).attr('mode'), $(this).parents('div').children('.search_results'), function() {
        });
    });

    $(document).on('mouseover', '.search_option', function(event) {
        $(this).siblings().removeClass('match');
        $(this).addClass('match');
    });

    $(document).on('mouseup', '#share_event_results .search_option', function(event) {
        var entity = $(this).data('entity');
        event_receivers = addreceiver(entity.type, entity.id, entity.name, event_receivers, "event");
        event.stopPropagation();
    });

    $(document).on('mouseup', '.message_search_results .search_option', function(event) {
        var entity = $(this).data('entity');
        message_receivers = addreceiver(entity.type, entity.id, entity.name, message_receivers, "message");
        event.stopPropagation();
    });

    $(document).on('mouseup', '.group_search_results .search_option', function(event) {
        group_receivers = addreceiver(entity.type, entity.id, entity.name, group_receivers, "group");
        event.stopPropagation();
    });

    $(document).on('mouseup', '.search_input', function() {
        var box = $(this).next('.search_results');
        if (box.find('.search_result, .match').length == 0) {

        }
        else {
            box.slideDown();
        }
    });

    $(document).on('click', '.search_results, .search_input', function(e) {
        e.stopPropagation();
    });

    $(document).on('click', function() {
        $('.search_results').slideUp();
    });

    $(document).on('click', '#names_universal .search_option', function() {
        var entity = $(this).data('entity');
        if (entity.type == 'user') {
            window.location.assign('user?id=' + entity.eid);
        } else if (entity.type == 'group') {
            window.location.assign('group?id=' + entity.eid);
        }
    });
    $(document).on('click', '.search_option', function() { // Hide the search results when the user selects an option.
        alert('e');
        $(this).parents('.search_results').hide();
        $(this).closest('input.search').val('');
    });
    $('.name_selector').hover(function() {
        $('.match').css('background-color', 'transparent');
    }, function()
    {
        //mouseleave
    });

    $('input.search').each(function() {
        if ($(this).attr('placeholder') == "") {
            $(this).attr('placeholder', 'Search');
        }
    });

    $('.match').hover(function() {
        $('.match').css('background-color', '#FAFAFA');
    });

    /****************************************************
     * 2.1.3 Startup - Feed                              *
     ****************************************************/
		/****************************************************
     	* 2.1.3 Startup - Feed - Post                        *
     	****************************************************/
     	
     	$(document).on('click', '.post-button', function() {
     		Application.prototype.feed.post.submit();
     	});
    setInterval(function() {

    });

    $(document).on('click', '.feed_selector', function() {
        var type = $(this).attr('feed_id');
        var action = $(this).attr('action');
        $('.' + type + '_feed_selector').removeClass('active_feed');
        $(this).addClass('active_feed');
        if (type != "chat" && action != 'prevent_activity') {
//            if (action == "user_files") {
//                Application.prototype.feed.getContent(value, 0, 'user_files', function() {
//                });
//            }
//            else {
//                var value = $(this).attr('filter_id');
//                Application.prototype.feed.getContent(value, min_activity_id, type, function() {
//                });
//            }
        }
    });

    $(document).on('click', '.delete_activity', function() {
        var activity_id = $(this).parents('[data-activity_id]').data('activity_id');
        Application.prototype.UI.dialog(
                content = {
                    type: "html",
                    content: "Are you sure you want to delete this Post?"
                },
        buttons = [{
                type: "success",
                text: "Delete",
                onclick: function() {
                    delete_post(activity_id);
                    Application.prototype.UI.removeDialog();
                }
            }],
        properties = {
            modal: false,
            title: "Delete Post"
        });
    });

    $(document).on('click', '.edit_activity', function() {
        var activity_id = $(this).parents('[data-activity_id]').data('activity_id');

        var text = $('[data-activity_id="' + activity_id + '"]').find('.post_text').text();
        var edit_element = $("<div class='post_text_editor'></div>");
        var post_text_editor = $('<textarea class="autoresize"></textarea>');
        post_text_editor.val(text);
        edit_element.append(post_text_editor);
        var options = $("<div class='post_more_options' style='display:block;'></div>");
        options.append($("<button class='pure-button-green small edit_activity_save'>Save</button>"));
        edit_element.append(options);

        $('[data-activity_id="' + activity_id + '"]').find('.post_text').hide().after(edit_element);

        var file_container = $("<div class='file_box' style='max-height:200px;'></div>");
        $('[data-activity_id="' + activity_id + '"] .post_feed_media_wrapper').prepend(file_container);
        Application.prototype.file.list(file_container, '');
    });

    $(document).on('click', '.edit_activity_save', function() {
        var activity_id = $(this).parents('[data-activity_id]').data('activity_id');
        var text = $('[data-activity_id="' + activity_id + '"]').find('textarea.autoresize').val();
        $('[data-activity_id="' + activity_id + '"]').find('.edit_activity_save').attr('disabled', 'disabled').addClass('pure-buton-disabled');

        $.post('Scripts/home.class.php', {activity_id: activity_id, action: "updatePost", text: text}, function() {
            $('[data-activity_id="' + activity_id + '"]').find('.post_text').show().html(text);
            $('[data-activity_id="' + activity_id + '"]').find('.post_text_editor').remove();
        });
    });

    $(document).on('keyup', '.inputtext', function(event) {
        var id = $(this).attr('id');
        var clone = $('#' + id + "_clone")
        if (event.keyCode == 13) {
            var post_id = $(this).parents('[data-activity_id]').data('activity_id');
            Application.prototype.feed.comment.submit($(this).val(), post_id, function(comment) {
                Application.prototype.feed.comment.append(post_id, comment);
            });
            return false;
        }
    });

    $(document).on('click', '.activity_actions', function(event) {
        var activity = {};
        var post_id = $(this).parents('[data-activity_id]').data('activity_id');
        activity.id = post_id;
        var data = {
            action: 'get_comments',
            activity_id: post_id,
            min: comments[post_id]['min'],
            max: comments[post_id]['max']
        }
        $.post('Scripts/home.class.php', data, function(response) {
            reponse = $.parseJSON(response);
            activity.comment = response;
            for (var i in activity.comment.comment) {
                comments[activity.id].push(activity.comment.comment[i].id);
                append_comment(post_id, activity.comment.comment[i]);
            }
        });
    });

    $(document).on('click', '.post_media_wrapper .delete_cross', function(event) {
        event.stopPropagation();
        Application.prototype.feed.post.removeFile(object = {type: "File", value: $(this).parents('[data-file_id]').data('file_id')});
    });

    $(document).on('click', '.post_feed_media_wrapper .remove_event_post', function() { //DELETE FILES FROM POSTED CONTENT
        var file_id = $(this).parents('[data-file_id]').data('file_id');
        var activity_id = $(this).parents('[data-activity_id]').not('[data-file_id]').data('activity_id');
        var post_text = $('[data-activity_id="' + activity_id + '"] .post_text').text();

        if ($(this).parents('.post_feed_item').siblings('.post_feed_item').length == 0 && post_text == "") {
            Application.prototype.UI.dialog(
                    content = {
                        type: "html",
                        content: "If you delete this file, your post will be removed, as it does not contain any content. Continue?"
                    },
            buttons = [
                {
                    type: "success",
                    text: "OK",
                    onclick: function() {
                        $.post('Scripts/files.class.php', {action: "removePostFile", file_id: file_id, activity_id: activity_id}, function() {
                        });
                        $(this).parents('.post_feed_item').remove();
                        delete_post(activity_id);
                        removeDialog();
                    }
                },
                {
                    type: "neutral",
                    text: "Cancel",
                    onclick: function() {
                        removeDialog();
                    }
                }],
            properties = {
                modal: false,
                title: "Remove File from Post"
            });
        } else {
            $(this).parents('.post_feed_item').remove();
            $.post('Scripts/files.class.php', {action: "removePostFile", file_id: file_id, activity_id: activity_id}, function() {
            });
        }
    });

    $(document).on('click', '.post_media_photo.post_media_preview', function() {
        var file_id = $(this).parents('[data-file_id]').data('file_id');
        var activity_id = $(this).parents('[data-activity_id]').data('activity_id');
        Application.prototype.file.theater.initiate(activity_id, file_id, {});
    });

    $(document).on('click', '.comment_delete', function() {
        var comment_id = $(this).parents('[data-comment_id]').data('comment_id');
        $(this).parents('.single_comment_container').next('hr.post_comment_seperator').remove();
        $(this).parents('.single_comment_container').hide();
        $.post('Scripts/home.class.php', {action: "deleteComment", comment_id: comment_id}, function(response) {
        });
    })
    $(document).on('click', '.post_like_activity', function(event) {
        var post_id = $(this).parents('[data-activity_id]').data('activity_id');
        var has_liked = $(this).attr('has_liked');
        var like_count = parseInt($('[data-activity_id="' + post_id + '"] .post_like_count:first').text());
        
        if (has_liked === "false") {
            $(this).attr('has_liked', "true");
            like_count++;
        } else {
            $(this).attr('has_liked', "false");
            like_count--;
        }
        if($(this).is('button')) {
            $(this).toggleClass('pure-button-blue');
        } else {
            if (has_liked === "false") {
                $(this).text(COMMENT_UNLIKE_TEXT);
            } else {
                $(this).text(COMMENT_LIKE_TEXT);
            }
        }
        $('[data-activity_id="' + post_id + '"] .post_like_count:first').text(like_count);

        $.post("Scripts/home.class.php", {type: 1, activity_id: post_id, action: "like"}, function(data)
        {
            $('[data-activity_id="' + post_id + '"] .post_like_count:first').text(data);
        });
    });
    $(document).on('click', '.post_comment_vote', function(event) {
        // var post_id = $(this).parents('[data-activity_id]').data('activity_id');
        var comment_id = $(this).parents('[data-comment_id]').data('comment_id');
        var has_liked = String($(this).data('has_liked'));
        var like_count = parseInt($('[data-comment_id="' + comment_id + '"] .post_comment_liked_num').text());

        if (has_liked == "true") {
            like_count--;
            $(this).data('has_liked', "false");
            $(this).text(COMMENT_LIKE_TEXT);
        }
        else {
            like_count++;
            $(this).data('has_liked', "true");
            $(this).text(COMMENT_UNLIKE_TEXT);
        }

        $('[data-comment_id="' + comment_id + '"] .post_comment_liked_num').text(like_count);
        $.post('Scripts/home.class.php', {action: "comment_vote", comment_id: comment_id}, function(response) {
            $('[data-comment_id="' + comment_id + '"] .post_comment_liked_num').text(response);
        });
    });

    $(document).on('mouseenter', '.post_height_restrictor', function() {
        //refreshContent($(this).data('activity_id'));
    });

    /****************************************************
     * 2.1.3 Startup - Files                             *
     ****************************************************/

    $(document).on('click', '#file_share div.file_item', function() {
        var file = $(this).data('file');
        Application.prototype.feed.post.addFile(file, 'create');
    });

    $(document).on('click', '.audio_hidden_container, .files_actions, p.files, div.files input, .post_feed_item', function(event) {
        event.stopPropagation();
    });

    $(document).on('click', '.audio_button', function(event) {
        var id = $(this).parents('[data-file_id]').data('file_id');
        var uid = $(this).parents('[uid]').attr('uid');
        Application.prototype.file.audioPlay(id, function() {
        }, function(progress) {
        }, function() {
        }, uid);
        event.stopPropagation();
    });

    $(document).on('click', 'p.files', function(event) {
        event.stopPropagation();
        event.preventDefault();
        $(this).hide();
        var input = $('<input class="files" type="text"></input>');
        input.val($(this).text());
        $(this).after(input);
        input.focus();
    });

    $(document).on('focusout', 'div.files input', function() {
        var file_id = $(this).parents("[data-file_id]").data('file_id');
        $.post('Scripts/files.class.php', {action: "rename", file_id: file_id, text: $(this).val()}, function() {

        });
        $(this).prev('p.files').text($(this).val()).show();
        $(this).hide();
    });

    var entered = 0;
    $(document).on('click', '.upload_here', function(event) {
        var element = $(this);
        var input = $("<input id='file_picture' type='file' />");
        input.trigger('click');
        input.on('change', function() {
            uploadDragDrop(element, $(this).get(0).files);
        });
    });

    $(document).on('dragover', '.upload_here', function(event) {
        $(this).addClass('upload_hover');
        entered++;
    });

    $(document).on('dragleave', '.upload_here', function(event) {
        $(this).removeClass('upload_hover');
        entered--;
    });

    $(document).on('drop', '.upload_here', function(event) {
        var element = $(this);
        $(this).removeClass('upload_hover');
        entered--;
        var files = event.target.files || (event.dataTransfer && event.dataTransfer.files);
        if (!files) {
            files = event.dataTransfer || (event.originalEvent && event.originalEvent.dataTransfer);
        }
        uploadDragDrop(element, files.files);
    });

    $(document).on("dragenter dragstart dragend dragleave dragover drag drop", '.upload_here', function(event) {
        event.preventDefault();
    });

    $(document).on('click', '#create_folder', function() {
        Application.prototype.UI.dialog(
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
        $('.files, .folder').removeClass('file_hidden_container_active');
        $(this).toggleClass('file_hidden_container_active');
    });

    refreshVideoJs();

    $(document).on('change', '#file', function() {
        var input = $(this).get(0).files;
        var index = 0;
        var files = getInputFiles('file');
        var uploadCount = 0;
        Application.prototype.file.upload.upload(files,
                function() {
                },
                function(percent) {
                }, function() {
            refreshFileContainer(encrypted_folder);
        },
                properties = {
                    type: "File",
                });
    });
    $('#loading_icon').fadeOut();

    $(document).on('mouseleave', "div.files, div.folder", function() {
        $('.audio_hidden_container').fadeOut("fast");
    });

    $('.audio_hidden_container').hover(function() {
    }, function() {
    });

    //setRecentFileScroller();


    /****************************************************
     * 2.1.3 Startup - Stickies                          *
     ****************************************************/
    var typingTimer;
    var doneTypingInterval = 3000;

    $(document).on('keyup', '.paste_pad', function() {
        clearTimeout(typingTimer);
        var text = $(this).html();
        typingTimer = setTimeout(function() {
            doneTyping(text);
        }, doneTypingInterval);
    });

    $(document).on('keydown', '.paste_pad', function() {
        clearTimeout(typingTimer);
    });

    /****************************************************
     * 2.1.3 Startup - Scrollbars                        *
     ****************************************************/

    $('.scroll_thin').each(function() {
        $(this).mCustomScrollbar(SCROLL_OPTIONS);
    });
    $('.scroll_thin_left').each(function() {
        $(this).mCustomScrollbar(SCROLL_OPTIONS);
    });

    $('.scroll_thin_horizontal').each(function() {
        $(this).mCustomScrollbar({
            scrollButtons: {
                enable: false
            },
            advanced: {
                updateOnContentResize: true,
                autoScrollOnFocus: true,
            },
            scrollInertia: 10,
            autoHideScrollbar: true,
            horizontalScroll: true,
        });
    });

    /****************************************************
     * 2.1.3 Startup - Settings                          *
     ****************************************************/

    $(document).on('click', '.edit_hidden', function() {
        $(this).hide();
        $(this).next('.hidden_section').show();
    });

    /****************************************************
     * 2.1.3 Startup - Resize                            *
     ****************************************************/

    $(document).on('keydown', '.autoresize, .inputtext', function(e) {
        $(this).css('height', '0px');
        $(this).css('height', $(this)[0].scrollHeight + 10 + "px");
    });

    $(window).resize(function() {
        Application.prototype.chat.detectChange();
        if (Application.prototype.file.theater.active) {
            Application.prototype.file.theater.adjust();
        }
    });

    /****************************************************
     * 2.1.3 Startup - Calendar                          *
     ****************************************************/
    if ($('.calendar-container').length > 0) {
        Application.prototype.calendar.getEvents(5, function(events) {
            for (var i = 0; i < events.length; i++) {
                $('.calendar-container').append(Application.prototype.calendar.event.print(events[i]));
            }
        });
    }

    $(document).on('click', '.box_container .navigate_right', function() {
        console.log('naviagte cal');
    });
    $(document).on('click', '.box_container .navigate_left', function() {
        console.log('naviagte cal');
    });

    /****************************************************
     * 2.1.3 Startup - Chat                              *
     ****************************************************/

    Application.prototype.chat.iniScroll();
    var cookie = 0; //getCookie('chat_feed');
    if (cookie == 0) {
        $('#chat').hide();
    }
    $("#chat_toggle").click(function() {
        var cookie = getCookie("chat_feed");
        if (cookie > 0 || isNaN(cookie)) {
            setCookie('chat_feed', 0, 5);
            $('#chat_toggle').html("OFF");
            $('#chat').hide('slide', {direction: 'right'}, 500);
        } else {
            $('#chat_toggle').html("ON");
            $('#chat').show('slide', {direction: 'right', duration: 0}, 500);
        }
    });

    $(document).on('click', '.chat_feed_selector', function() {
        //change_chat_view($(this).attr("chat_feed"));
    });

    $(document).on("propertychange keydown input change", '.chatinputtext', function(e) {
        if (e.keyCode == 13) {
            if (e.shiftKey !== true) {
                e.preventDefault();
                Application.prototype.chat.submit($(this).val(), chat_room);
            }
        }
        Application.prototype.chat.detectChange();
    });

    $(document).on('click', '.delete_message', function(event) {
        event.stopPropagation();
        event.preventDefault();
        deleteMessage($(this).parents('[thread_id]').attr('thread_id'));
    });
    $(document).on('click', '.message_inbox_item', function() {
        window.location.assign('message?thread=' + $(this).attr('thread_id') + '');
    });

    /****************************************************
     * 2.1.4 Startup - Notification                      *
     ****************************************************/

    refreshVideoJs();
    Application.prototype.notification.getNotificationNumber();
    var table = "<table style='min-height:100px;height:100px;width:100%;'><tr style='vertical-align:middle;'><td style='width:100%;text-align:center;'><div class='loader_outside_small'></div><div class='loader_inside_small'></div></td></tr></table>";
    $('ul.message, ul.network, ul.notify').prepend(table);

    $('#popup_message').mCustomScrollbar(SCROLL_OPTIONS);
    $('#popup_network').mCustomScrollbar(SCROLL_OPTIONS);
    $('#popup_notify').mCustomScrollbar(SCROLL_OPTIONS);

    $(document).on('click', "img.message", function(event)
    {
        $('img.message').removeClass('message_active');
        $(this).addClass('message_active');
    });
    $(document).on('click', "#home_icon", function(event)
    {
        event.stopPropagation();
        window.location.replace("home");
        $("#notificationdiv").hide();
        $("#networkdiv").hide();
        $("#geardiv").hide();
    });
    $(document).on('click', "#personal", function(event)
    {
        event.stopPropagation();
        $(".personal").show();
        $(".general").hide();
        $("#messagediv").hide();
        $("#notificationdiv").hide();
        $("#networkdiv").hide();
        $("#geardiv").hide();
    });
    $(document).on('click', "#message_click", function(event)
    {
        Application.prototype.notification.getMessageBox();
        markAllSeen('message');
        event.stopPropagation();
        $("#messagediv").show()
        $(".personal").hide();
        $(".general").hide();
        $("#notificationdiv").hide();
        $("#networkdiv").hide();
        $("#geardiv").hide();
        $('.message_notification').hide();
    });
    $(document).on('click', "#notification_click", function(event)
    {
        Application.prototype.notification.getNotificationBox();
        markAllSeen('notification');
        event.stopPropagation();
        $("#notificationdiv").show();
        $(".personal").hide();
        $(".general").hide();
        $("#messagediv").hide();
        $("#networkdiv").hide();
        $("#geardiv").hide();
        $('#notification_counter').hide();
    });
    $(document).on('click', "#network_click", function(event)
    {
        Application.prototype.notification.getNetworkBox();
        markAllSeen('network');
        event.stopPropagation();
        $("#notificationdiv").hide();
        $(".personal").hide();
        $(".general").hide();
        $("#messagediv").hide();
        $("#networkdiv").show();
        $("#geardiv").hide();
        $('#network_counter').hide();
    });
    $(document).on('click', "#gear_click", function(event)
    {
        event.stopPropagation();
        $("#notificationdiv").hide();
        $(".personal").hide();
        $(".general").hide();
        $("#messagediv").hide();
        $("#geardiv").show();
    });
    $(document).on('click', "html", function()
    {
        $(".general").hide();
        $(".personal").hide();
        $("#messagediv").hide();
        $("#notificationdiv").hide();
        $("#networkdiv").hide();
        $("#geardiv").hide();
        $('img.message').removeClass('message_active');
    });

    /****************************************************
     * 2.1.4 Startup - NavTog                            *
     ****************************************************/

    var items = document.querySelectorAll('.menuItem');

    for (var i = 0, l = items.length; i < l; i++) {
        items[i].style.left = (50 - 35 * Math.cos(-0.5 * Math.PI - 2 * (1 / l) * i * Math.PI)).toFixed(4) + "%";
        items[i].style.top = (50 + 35 * Math.sin(-0.5 * Math.PI - 2 * (1 / l) * i * Math.PI)).toFixed(4) + "%";
    }

    $(document).on('click', '.center', function(e) {
        e.preventDefault();
        $('.circle').toggleClass('open');
    });

    $(document).on('click', '.menuItem', function() {
        $(this).siblings().removeClass('current_page');
        $(this).addClass('current_page');
        $('.circle').toggleClass('open');
    });

});
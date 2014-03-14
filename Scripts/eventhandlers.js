var profile_picture_id;
$(function() {
    
    //STICKIES
    if(1 == 1) {
        $(document).on('click', '.headerbar', function() {
            
            $('.global_container').toggleClass('global_container_menu');
                        
        });
    }
    var typingTimer;
    var doneTypingInterval = 3000;

    $(document).on('keyup','.paste_pad', function(){
        clearTimeout(typingTimer);
        var text = $(this).html();
        typingTimer = setTimeout(function(){doneTyping(text);}, doneTypingInterval);
    });

    //on keydown, clear the countdown 
    $(document).on('keydown', '.paste_pad', function(){
        clearTimeout(typingTimer);
    });

    //user is "finished typing," do something
    function doneTyping (text) {
        //do something
        $.post('Scripts/user.class.php', {action:"update", id:1, html:text}, function(response) {
            
        });
    }
    
    //END STICKIES
    
    $(document).on('click', '.delete_receiver', function() {
       var type = $(this).parents('[entity_type]').attr('entity_type');
       var id = $(this).parents('[entity_id]').attr('entity_id');
       var receivers_type = $(this).parents('[search_type]').attr('search_type');
       if(receivers_type == "event") {
           event_receivers = removereceiver(type, id, event_receivers);
       } else if(receivers_type == "group") {
           group_receivers = removereceiver(type, id, group_receivers);
       } else if(receivers_type == "message") {
           message_receivers = removereceiver(type, id, message_receivers);
       }
       
       $(this).parents('[entity_type]').remove();
       //console.log(event_receivers);
    });
    
    $(document).on('keyup', '.search', function() {
        console.log();
        search($(this).val(), $(this).attr('mode'), $(this).parents('div, table').children('.search_results'), function(){});
    });
    
    $(document).on('click', '.delete_activity', function() {
        var activity_id = $(this).parents('[data-activity_id]').data('activity_id');
        dialog(
                content = {
                    type: "html",
                    content: "Are you sure you want to delete this Post?"
                },
        buttons = [{
                type: "success",
                text: "Delete",
                onclick: function() {
                    delete_post(activity_id);
                    removeDialog();
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
            options.append($("<button class='pure-button-success small edit_activity_save'>Save</button>"));
            edit_element.append(options);
            
        $('[data-activity_id="' + activity_id + '"]').find('.post_text').hide().after(edit_element);
        post_text_editor.after($("<div class='textarea_clone'></div>"));
        autoresize(post_text_editor);
        
        var file_container = $("<div class='file_box' style='max-height:200px;'></div>");
        $('[data-activity_id="' + activity_id + '"] .post_feed_media_wrapper').prepend(file_container);
        fileList(file_container, '');
        
        
    });
    $(document).on('click', '.edit_activity_save', function() {
        var activity_id = $(this).parents('[data-activity_id]').data('activity_id');
        var text = $('[data-activity_id="' + activity_id + '"]').find('textarea.autoresize').val();
        $('[data-activity_id="' + activity_id + '"]').find('.edit_activity_save').attr('disabled', 'disabled').addClass('pure-buton-disabled');

        $.post('Scripts/home.class.php', {activity_id : activity_id, action: "updatePost", text: text}, function() {
            $('[data-activity_id="' + activity_id + '"]').find('.post_text').show().html(text);
            $('[data-activity_id="' + activity_id + '"]').find('.post_text_editor').remove();
        });
    });
    
    $(document).on('mouseover', '.search_option', function(event) {
        $(this).siblings().removeClass('match');
        $(this).addClass('match');
    })

    //FILES
    
    $(document).on('click', '#file_share div.file_item', function() {
        var file = $(this).data('file');
        addToStatus(file, 'create');
    });
    
    $(document).on('click', '.audio_hidden_container, .files_actions, p.files, div.files input', function(event) {
        event.stopPropagation();
    });

    $(document).on('click', '.audio_button', function(event) {
        var id = $(this).parents('[data-file_id]').data('file_id');
        var uid = $(this).parents('[uid]').attr('uid');
        audioPlay(id, function(){}, function(progress){ }, function() {}, uid);
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
        var file_id = $(this).parents("[file_id]").attr('file_id');
        $.post('Scripts/files.class.php', {action:"rename", file_id: file_id, text: $(this).val()}, function() {
            
        });
        $(this).prev('p.files').text($(this).val()).show();
        $(this).hide();
    });

    adjustSwitches();

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
        if(!files) {
            files = event.dataTransfer || (event.originalEvent && event.originalEvent.dataTransfer);
        }
        uploadDragDrop(element, files.files);
    });

    function uploadDragDrop(element, files) {
        var number = Math.floor(Math.random() * 10);
        element = $(element);
        progressBar(element, number);
        uploadFile(files, function(){}, function(pgs){
            updateProgress(number, pgs);
        }, function(file){
            removeProgress(number);
            element.css('background', "url('" + file.path + "')");
            element.addClass('upload_done');
            profile_picture_id = file.id;
            console.log(file);
        });
    }

    $(document).on("dragenter dragstart dragend dragleave dragover drag drop", '.upload_here', function (event) {
        event.preventDefault();
    });
    
    
    $(document).on('click', '.switch_option', function() {
        var container = $(this).parents('.switch_container');
        container.find('.switch_option').removeClass('switch_selected');
        $(this).addClass('switch_selected');
    });

    $(document).on('keyup', '.inputtext', function(event) {
        var id = $(this).attr('id');
        var clone = $('#' + id + "_clone")
        if (event.keyCode == 13) {
            var post_id = $(this).parents('[data-activity_id]').data('activity_id');
            submitcomment($(this).val(), post_id, function(comment) {
                append_comment(post_id, comment);
                emptyText(id);
            });
            return false;
        }
        resizeTextarea($(this), clone);
    });
    $(document).on('click', '.post_media_wrapper .delete_cross', function(event) {
    	event.stopPropagation();
    	removeFromStatus(object = {type: "File", value: $(this).parents('[file_id]').attr('file_id')});
    });
    $(document).on('click', '.post_feed_media_wrapper .remove_event_post', function() { //DELETE FILES FROM POST
        var file_id = $(this).parents('[file_id]').attr('file_id'); 
        var activity_id = $(this).parents('[data-activity_id]').data('activity_id');
        var post_text = $('[data-activity_id="' + activity_id + '"] .post_text').text();
        
        if($(this).parents('.post_feed_item').siblings('.post_feed_item').length == 0 && post_text == "") {
        	dialog(
                content = {
                    type: "html",
                    content: "If you delete this file, your post will be removed, as it does not contain any content. Continue?"
                },
        		buttons = [
        		{
                	type: "success",
                	text: "OK",
                	onclick: function() {
                            $.post('Scripts/files.class.php', {action: "removePostFile", file_id: file_id, activity_id: activity_id}, function() {});
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
        	$.post('Scripts/files.class.php', {action: "removePostFile", file_id: file_id, activity_id: activity_id}, function() {});
        }
    });
    
     $(document).on('click', '.post_media_photo.post_media_preview', function() {
         var file_id = $(this).parents('[file_id]').attr('file_id');
         var activity_id = $(this).parents('[data-activity_id]').data('activity_id');
         initiateTheater(activity_id, file_id, {});
     });
    
    $(document).on('click', '.comment_delete', function() {
        var comment_id = $(this).parents('[data-comment_id]').data('comment_id');
        $(this).parents('.single_comment_container').next('hr.post_comment_seperator').remove();
        $(this).parents('.single_comment_container').hide();
        $.post('Scripts/home.class.php', {action: "deleteComment", comment_id: comment_id}, function(response) {});
    })
    $(document).on('click', '.post_like_activity', function(event) {
    	var post_id = $(this).parents('[data-activity_id]').data('activity_id');
        var has_liked = $(this).attr('has_liked');
    	var like_count = parseInt($('[data-activity_id="' + post_id + '"] .post_like_count').text());
            
        if(has_liked === "false") {
            $(this).attr('has_liked', "true");
            $(this).text(COMMENT_UNLIKE_TEXT);
            like_count++;
        }
        else {
            $(this).attr('has_liked', "false");
            $(this).text(COMMENT_LIKE_TEXT);
            like_count--;
        }
        $('[data-activity_id="' + post_id + '"] .post_like_count').text(like_count);
        
         $.post("Scripts/home.class.php", {type: 1, activity_id: post_id, action: "like"}, function(data)
         {
              $('[data-activity_id="' + post_id + '"] .post_like_count').text(data);
         });
    });
    $(document).on('click', '.post_comment_vote', function(event) {
        // var post_id = $(this).parents('[data-activity_id]').data('activity_id');
        var comment_id = $(this).parents('[data-comment_id]').data('comment_id');
        var has_liked = String($(this).data('has_liked'));
        var like_count = parseInt($('[data-comment_id="' + comment_id + '"] .post_comment_liked_num').text());
        
        if(has_liked == "true") {
            like_count--;
            $(this).data('has_liked', "false");
            $(this).text(COMMENT_LIKE_TEXT);
            //console.log('This comment is LIKED, new COUNT: ' + like_count);
        }
        else {
            like_count++;
            $(this).data('has_liked', "true");
            $(this).text(COMMENT_UNLIKE_TEXT);
            //console.log('This comment has NOT BEEN LIKED, new COUNT: ' + like_count);
        }

        $('[data-comment_id="' + comment_id + '"] .post_comment_liked_num').text(like_count);
        $.post('Scripts/home.class.php', {action: "comment_vote", comment_id: comment_id}, function(response) {
            $('[data-comment_id="' + comment_id + '"] .post_comment_liked_num').text(response);
        });
    });
    
    $(document).on('mouseenter', '.post_height_restrictor', function() {
        refreshContent($(this).data('activity_id'));
    });

    $(window).on('resize', function() {
        resizeContainer();
//        $('.global_container').css('min-height', getViewPortHeight() + "px")
    });
    resizeContainer();
});
function resizeContainer() {
   var width = getViewPortWidth();
        if(width < 1400) {
            //$('.right_bar_container').addClass('right_bar_shift');
            $('.container').addClass('container_small');
        } else {
            $('.right_bar_container').removeClass('right_bar_shift');
            $('.container').removeClass('container_small');
        } 
}
function adjustSwitches() {
    $('.switch_container').find('.switch_option').each(function(){ 
        var siblings = $(this).parents('.switch_container').find('.switch_option').length;
        var width = $(this).parents('.switch_container').width() / siblings;
        $(this).width(width);
    });
}

function resizeTextarea(element, clone) {
    var text = $(element).val();
    $(clone).text(text);
    $(element).height($(clone).height());
}
function emptyText(id) {
    $('#' + id).val('');
    $('[actual_id="' + id + '"]').val('');
}

//FEED SELECTORs
function getFeedContent(feed_id, min_activity_id, page, callback)
{
    var link = page;
    if (page == "user") {
        link = "home"
    }

    else if (page == "user_files") {
        link = 'user';
        page = 'f';
    }

    if (typeof feed_id !== "undefined")
    {
        var container = $('.container').find('.feed_container');


        var none = $("<div class='post_height_restrictor'>"
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
            //console.log("feed=" + feed_id + " && min_activity_id=" + min_activity_id + " && response=" + data);
            //console.log('refreshed feed');
            removeModal();

            callback();
            refreshVideoJs();
            //initializeWaveForm();
        }

        if (isNaN(feed_id))
        {
            if (feed_id == 'a' || feed_id == 's' || feed_id == 'y') {
                refreshElement('#feed_refresh', link, "f=" + feed_id + "&min_activity_id=" + min_activity_id, "#feed_refresh", callbak);
            }
            else {
                feed_id = feed_id.replace('u_', '');
                refreshElement('#feed_refresh', link, "u=" + feed_id + "&min_activity_id=" + min_activity_id, "#feed_refresh", callbak);
            }
        }
        else
        {
            refreshElement('#feed_refresh', link, "fg=" + feed_id + "&min_activity_id=" + min_activity_id, "#feed_refresh", callbak);
        }
    }
}

$(function() {
    $('.feed_selector').click(function()
    {
        var type = $(this).attr('feed_id');
        var action = $(this).attr('action');
        $('.' + type + '_feed_selector').removeClass('active_feed');
        $(this).addClass('active_feed');
        if (type != "chat" && action != 'prevent_activity') {
            if (action == "user_files") {
                getFeedContent(value, 0, 'user_files', function() {
                });
            }
            else {
                var value = $(this).attr('filter_id');
                getFeedContent(value, min_activity_id, type, function() {
                });
            }
        }
        else {
            //console.log('feed loading prohibited (chat or action_prevented property)!');
        }
    });
});

//END FEED SELECTORS

// SEARCH & SETUP SCROLL
$(document).on('click', '#names_universal .search_option', function() {
    var entity = $(this).data('entity');
    if(entity.type == 'user') {
        window.location.assign('user?id=' + entity.eid);
    } else if (entity.type == 'community') {
        window.location.assign('community?id=' + entity.eid);
    }
});
$(document).on('click', '.search_option', function() { // Hide the search results when the user selects an option.
    $(this).parents('.search_results').hide();
    $(this).closest('input.search').val('');
});
$(function() {
    $('.name_selector').hover(function() {
        $('.match').css('background-color', 'transparent');
    }, function()
    {
        //mouseleave
    });
    $('input.search').each(function() {
        if($(this).attr('placeholder') == "") {
            $(this).attr('placeholder', 'Search');
        }
    });
    $('.match').hover(function() {
        $('.match').css('background-color', '#FAFAFA');
    });

    $('.scroll_thin').each(function(){
        $(this).mCustomScrollbar(SCROLL_OPTIONS);
    });
    $('.scroll_thin_left').each(function(){
        $(this).mCustomScrollbar(SCROLL_OPTIONS);
    });

    $('.scroll_thin_horizontal').each(function(){
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
    
    $(document).on('click', '#share_event_results .name_selector, #share_event_results .match', function(event) {
        var entity = $(this).data('entity');
        event_receivers = addreceiver(entity.type, entity.id, entity.name, event_receivers, "event");            
        event.stopPropagation();
    });
    
    $(document).on('click', '.message_search_results .name_selector, .message_search_results .match', function(event) {
        var entity = $(this).data('entity');
            message_receivers = addreceiver(entity.type, entity.id, entity.name, message_receivers, "message");            
            event.stopPropagation();
            //console.log(message_receivers);
    });

    $(document).on('click', '.group_search_results .name_selector,  .group_search_results .match', function(event) {
            group_receivers = addreceiver(entity.type, entity.id, entity.name, group_receivers, "group");            
            event.stopPropagation();
            //console.log(group_receivers);
    });
    
    $(document).on('click', '#share_event_results .name_selector, #share_event_results .match', function(event) {
        var entity = $(this).data('entity');
            event_receivers = addreceiver(entity.type, entity.id, entity.name, event_receivers, "event");            
            event.stopPropagation();
    });

    $(document).on('click', '#share_event_results .name_selector, #share_event_results .match', function(event) {
        var entity = $(this).data('entity');
            event_receivers = addreceiver(entity.type, entity.id, entity.name, event_receivers, "event");            
            event.stopPropagation();
    });

    $(document).on('click', '.search_input', function() {
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
});

//END SEARCH
var alignment;
var needs_loading = true;
// setInterval(function() {
//     $.post('Scripts/user.class.php', {action: 'setOnline'}, function() {

//     });
// }, 30000);
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

function search(text, mode, element, callback) {
    if (text == "") {
        $(element).hide();
    } else {
        var loader = $("<img class='loader' src='Images/ajax-loader.gif'></img>");
        $(element).children('img.loader').remove();
        $(element).prepend(loader);
        $(element).show();
    }
    
    $.post("Scripts/searchbar.php", {search: mode, input_text: text}, function(response) {
        $(element).empty();
        var slider = $("<div class='search_slider'></div>").html(response);
        $(element).html(slider);
        var height = slider.height();
        $(element).mCustomScrollbar(SCROLL_OPTIONS);
        $(element).height(height);
        setTimeout(function() {
            $(element).mCustomScrollbar("update");
            $(element).mCustomScrollbar("scrollTo", "top");
        }, 100);
    });
    //$(element).off('click');
    //$(element).on('click', function(e) {
        //e.preventDefault();
       // e.stopPropagation();
    //});
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

//DROPDOWN selectors
$(document).on('click', ".default_dropdown_selector", function(event)
{
    event.stopPropagation();
    $('.default_dropdown_wrapper').hide();
    var wrapper = '#' + $(this).attr('wrapper_id');
    $(wrapper).toggle();
    $(wrapper).mCustomScrollbar(SCROLL_OPTIONS);
    $(this).toggleClass('default_dropdown_active');
});
$(document).on('click', "html", function(event)
{
    $('.default_dropdown_selector').removeClass('default_dropdown_active');
    $('.default_dropdown_wrapper').hide();
});

$(document).on('click', ".default_dropdown_selector .default_dropdown_item", function(event)
{
    event.stopPropagation();
    var selection_value = $(this).attr('value');
    var wrapper = $(this).parents('.default_dropdown_wrapper');
    wrapper.hide();
    $(this).parents('.default_dropdown_selector').removeClass('default_dropdown_active');
    wrapper.find('.default_dropdown_item').removeClass('default_dropdown_active');
    $(this).addClass('default_dropdown_active');
    $(this).parents('.default_dropdown_selector').attr('value', selection_value).find('.default_dropdown_preview').text($(this).text());
});
$(function() {
    $('.default_dropdown_selector').on({
        focus : function() {
            //$(this).css('outline','1px dotted #000');
            $(this).trigger('click');
        },
        blur : function() {
            $('html').trigger('click');
        }
    });
});
//small
$(document).on('click', ".default_dropdown_actions", function(event)
{
    event.stopPropagation();
    $('.default_dropdown_wrapper').hide();
    var wrapper = '#' + $(this).attr('wrapper_id');
    $(wrapper).toggle();
    $(wrapper).mCustomScrollbar(SCROLL_OPTIONS);
});

//END DROPDOWN


//SETTINGS
$(document).on('click', '.edit_hidden', function() {
    $(this).hide();
    $(this).next('.hidden_section').show();
});
//END SETTINGS

//USER
$(document).on('click', '.connect_button', function() {
    connect($(this), $(this).parents('[entity_id]').attr('entity_id'));
});
$(document).on('click', '.connect_accept', function() {
    connectAccept($(this).data('invite_id'));
});
//END USER
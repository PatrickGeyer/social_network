// Requires JQuery to be included before

$(function() {
    $(document).on('keyup', '.inputtext', function(event) {
        var id = $(this).attr('id');
        var clone = $('#' + id + "_clone")
        if (event.keyCode == 13) {
            submitcomment($(this).val(), $(this).data('activity_id'), function() {
                emptyText(id);
            });
            return false;
        }
        resizeTextarea($(this), clone);
    });
    $(document).on('click', '.post_comment_vote', function(event) {
        var id = $(this).attr('comment_id');
        var has_liked = $(this).attr('has_liked');
        
        if(has_liked === "false") {
            $(this).attr('has_liked', "true");
            $(this).text(UNLIKE_TEXT);
        }
        else {
            $(this).attr('has_liked', "false");
            $(this).text(LIKE_TEXT);
        }
        $.post('Scripts/home.class.php', {action: "comment_vote", comment_id: id}, function(response) {
            $('.post_comment_liked_num[comment_id="' + id + '"]').text("- " + response + " likes");
        });
    });
});
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
                + "You can post files and shizzle up the in the box.</p></center></div>");

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
    $('.search_results').each(function(){
        $(this).mCustomScrollbar(SCROLL_OPTIONS);
    });
    $('.default_dropdown_wrapper').mCustomScrollbar(SCROLL_OPTIONS);
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
setInterval(function() {
    $.post('Scripts/user.class.php', {action: 'setOnline'}, function() {

    });
}, 30000);
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
        receiver_id: receiver_id,
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
    if (text == "") {
        $(element).hide();
    } else {
        var loader = $("<img src='Images/ajax-loader.gif'></img>");
        $(element).prepend(loader);
        $(element).show();
    }
    $.post("Scripts/searchbar.php", {search: mode, input_text: text}, function(response) {
        $(element).find('.search_slider').slideUp("fast", function() {
            $(this).remove();
        });
        var slider = $("<div class='search_slider'></div>").hide();
        slider.append(response);
        $(element).append(slider);
        loader.remove();
        var scrollElement = document.querySelector(element);
        slider.slideDown('fast',function() {
            if (scrollElement.offsetHeight < scrollElement.scrollHeight || scrollElement.offsetWidth < scrollElement.scrollWidth) {
                $(element).mCustomScrollbar();
                $(element).find('.mCustomScrollBox ').height('');
                $(element).mCustomScrollbar("update");
                $(element).find('.mCS_container').css('top', '0px');
            }
            callback();
        });
    });
    $(element).off('click');
    $(element).on('click', function(e) {
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

//DROPDOWN selectors
$(document).on('click', ".default_dropdown_selector", function(event)
{
    $('.default_dropdown_wrapper').hide();
    var wrapper = '#' + $(this).attr('wrapper_id');
    $(wrapper).toggle();
    event.stopPropagation();
    $(this).toggleClass('default_dropdown_active');
});
$(document).on('click', "html", function(event)
{
    $('.default_dropdown_selector').removeClass('default_dropdown_active');
    $('.default_dropdown_wrapper').hide();
});

$(document).on('click', ".default_dropdown_item", function(event)
{
    event.stopPropagation();
    var selection_value = $(this).attr('value');
    var wrapper = $(this).parents('.default_dropdown_wrapper');
    wrapper.hide();
    $(this).parents('.default_dropdown_selector').removeClass('default_dropdown_active');
    wrapper.find('.default_dropdown_item').removeClass('default_dropdown_active');
    $(this).addClass('default_dropdown_active');
    $(this).parents('.default_dropdown_selector').attr('value', selection_value).find('.default_dropdown_text').text($(this).text());
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

//END DROPDOWN
<?php
include("lock.php");


    if(!isset($_COOKIE['showchat']))
    {
        setcookie('showchat', 'y');
    }
?>
<script src="Scripts/jquery-1.10.2.js"></script>
<script src='Scripts/jquery.scrollTo-1.4.3.1.js'></script>
<script src="Scripts/jquery.cookie.js"></script>
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBtmd6SX8JrdtTWhuVqIA37XJPO2nwtM6g&sensor=true"></script>
<script type="text/javascript">
    function createMap(city, country) {
        var geocoder =  new google.maps.Geocoder();
        geocoder.geocode( { 'address': city + ", " + country}, function(results, status) {
        if (status == google.maps.GeocoderStatus.OK) {
           //alert("location : " + results[0].geometry.location.lat() + " " +results[0].geometry.location.lng()); 
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
            google.maps.event.addListener(marker, "dragend", function(){
                dialog(
                    'Location',
                    {
                        type:"html",
                        content:"Are you sure you want to update your location?",
                    },
                    {
                        type:"primary",
                        text:"Yes",
                        onclick: "updateLocation(" + marker.position.lat() + ", " + marker.position.lng() + ");"
                    },
                    {
                        modal:false
                    }
                    );
                //alert(marker.position.toUrlValue());
            });
        } else {
            alert("Something got wrong " + status);
        }
        });
    }
    function updateLocation(lat, lng) 
    {
        $.post('Scripts/user.class.php', {lat:lat, lng:lng}, function(response) {
            alert(response);
        });
    }
    </script>

<script>
    
    // SEARCH
    $(function(){
        	$('.name_selector').hover(function(){
		$('.match').css('background-color', 'transparent');
	}, function()
	{
		//mouseleave
	});
	$('.match').hover(function(){
		$('.match').css('background-color', '#FAFAFA');
	});
    });
    //END SEARCH
var alignment;
var needs_loading = true;
setInterval(function() {
	$.post('Scripts/user.class.php', {action : 'setOnline'}, function() {

	});
}, 5000);
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
		$('#loading_icon').fadeOut();

		$(document).on('mouseover', "div.files",function()
		{
			$('.audio_hidden_container').not($(this).prev('.audio_hidden_container')).hide();
			$(this).prev('.audio_hidden_container').show();
		});

		$(document).on('mouseover', "div.folder",function()
		{
			$('.audio_hidden_container').hide();
		});

		$('.audio_hidden_container').hover(function(){},function(){ });

		$('.image_placeholder').load(function(){
			resizeDiv($(this).attr('f_id'));
		});

		$('#upload_file').click(function(){
			$('#upload_file').hide();
			$('#upload_file_dialog').fadeIn();
		});

		$('#create_folder').click(function(){
			$('#create_folder_dialog').dialog(
				{ buttons: [ { text: "Create Folder", click: function() { $( this ).dialog( "close" ); createFolder(parent_folder);} } ] });
		});
	});

	function _(el)
	{
		return document.getElementById(el);
	}
	var q = 0;
	function uploadFile(type)
	{
		if(type == "folder")
		{
			var files;
			files = _("folder").files;
		}
		else if(type == "file")
		{
			var files = _("file").files;
		}
		var length = files.length;
		for(var count = 0; count < length; count++)
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
			xhr.upload.onprogress = function(event) {progressHandler(event, count);};
			xhr.onload = function () {completeHandler(this, count-1);};
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
		if(q > 1)
		{
			$("#status").text(q+" items uploading...");
		}
		else
		{
			$("#status").text(q+" item uploading... " + percent +"%");
		}
	}
	function completeHandler(event, id)
	{
		if(q > 1)
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
		refreshCurrentDiv();
	}
	function errorHandler(event)
	{
		if(q > 1)
		{
			q--;
		}
		_("status").innerHTML = "Upload Failed!";
		$('#loading_icon').fadeOut();
	}
	function abortHandler(event)
	{
		if(q > 1)
		{
			q--;
		}
		_("status").innerHTML = "Upload Aborted!";
		$('#loading_icon').fadeOut();
	}
	function resizeDiv(element)
	{
		if($(element).height() > $(element).width())
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
            if($(element).scrollTop() - $(element).scrollHeight < 10) {
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
            if(element == "force") {
                $('.user_preview_info').show();
            }
            else
            {
                $('.user_preview_info').not(element.children('.user_preview_info').first()).remove();
                if(element.children('.user_preview_info').length == 0)
                {
                    createUserPreview(element,user_id);
                    $.post('Scripts/user.class.php', {action : "get_preview_info", id: user_id}, function(response){
                        fillUserPreview(response);
                    });
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
            var createTimeout = setTimeout(function(){
            	if($('*[user_id="'+user_id+'"]:hover').length > 0)
            	{
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
            $(document).on('scroll',function(){alignUserPreview(element);});
            console.log('align');
            var left = element.offset().left;
            var width = element.width();
            var left_total = left + width + 20;
            if(left_total > getViewPortWidth()/2) {
                left_total = left - 40 - $('.user_preview_info').width();
                alignment = "left";
            } else {
                alignment = "right";
            }
            var arrow;
            if(alignment == "left")
            {
               arrow = 'user_preview_arrow-right';
            } else if(alignment == "top") {
                
            } else if(alignment == "bottom") {
            }
            else
            {
                arrow = 'user_preview_arrow-left';
            }

            $('.user_preview_info').append('<div class="'+arrow+'-border"></div>');
            $('.user_preview_info').append('<div class="'+arrow+'"></div>');
            
            var top = element.offset().top;
            var element_height = element.outerHeight(true);
            var scrolled_height = $( document ).scrollTop();
            var top_total = top - scrolled_height - element_height/2;
            var arrow_height = $('.' + arrow + "-border").outerHeight(true)/2 -2;
            //top_total = top_total + arrow_height;
            
            $('.user_preview_info').css('left', left_total + "px");
            $('.user_preview_info').css('top', top_total + "px");
        }
        function fillUserPreview(response)
        {
            response = $.parseJSON(response);
            var user_string;
            user_string = ("<table style='height:100%;width:100%;' cellspacing='0'><tr><td rowspan='3' style='width:80px;'><div style='width:70px;height:70px;background-image:url(" +
                 response[2]   + ");background-size:cover;background-repeat:no-repeat;'></div></td>");
            user_string += ("<td><a style='padding:0px;' href='user?id=" + response[0] + 
            	"'><span class='user_preview_name'>" + response[1] + "</span></a></td></tr>");
               
            user_string += "<tr><td><a style='padding:0px;display:inline;' href='community?id=" + response[6] + 
                    "'><span class='user_preview_community'>"+response[4]+"</span></a><span class='user_preview_position'> &bull; "+response[5]+"</span></td></tr>";
            user_string += "<tr><td><span class='user_preview_about'>"+response[3]+"</span></td></tr>";
            
            user_string += "<tr><td></td><td><div class='user_preview_buttons'>" + 
                    "<button class='pure-button-primary smallest'>Chat</button><button class='pure-button-success smallest'>Message</button></div></td></tr>";
            
            user_string += "</table>";
            
            $('.user_preview_info').find('*').not('.user_preview_arrow-right, .user_preview_arrow-right-border, .user_preview_arrow-left, .user_preview_arrow-left-border').remove();
            $('.user_preview_info').append(user_string);
        }
        
        function removeUserPreview(mode, event)
        {
        	if($('.user_preview_info').length > 0)
        	{
	            if(mode == "check mouse")
	            {
	                if(calculateDistance($('.user_preview_info'), event.pageX, event.pageY) > 300)
	                {
	                    $('.user_preview_info').fadeOut('fast', function(){$(this).remove();});
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
            return Math.floor(Math.sqrt(Math.pow(mouseX - (elem.offset().left+(elem.width()/2)), 2) + Math.pow(mouseY - (elem.offset().top+(elem.height()/2)), 2)));
        }
$(function(){
	var id;
	$('.who_liked_hover').on({
		mouseenter: function(){
			id = $(this).attr("activity_id");
			$('.theater-info-container').find('*').each(function(){
				alert('hi');
			});
			$('#who_liked_'+id).show();
		}, 
		
		mouseleave: function(){
			$('#who_liked_'+id).hide();
		}
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

	$(document).click(function()
	{
		$('#names_universal').hide();
                removeUserPreview("force");
	});
        
        $(document).on('mouseover', '.user_preview', function(event){
            event.stopPropagation();
            var user_id = $(this).attr('user_id');
            showUserPreview($(this), user_id);
        });
        
        $(document).on('mouseover', "html", function(event){
            removeUserPreview('check mouse', event);
        });
        
	alignNavFriend();
});

$( window ).resize(function() {
	alignNavFriend();
	adjustTheater();
});

var currentPage = getCookie('current_feed');


setInterval(function(){alignNavFriend();}, 200);
function alignNavFriend()
{
	var container_left = $('.container_headerbar').offset().left;
	container_left = container_left - $('.navigation').outerWidth();

	$('#logo').css('left', container_left);

	$('.navigation').css('left', container_left - 1);

	$('#friends_container').css('left', container_left);
	var top_height = $('.navigation').position().top + $('.navigation').height();

	$('#friends_container').css('top', top_height + 22);

	var height = $('#friend_load').height();
	$('#friends_bar').css('max-height', height);

	$('.messagecomplete').css('top', top_height + 22);
	$('.messagecomplete').css('left', container_left - 2);
}

function scrollH(element_id, wrapper_id, speed)
{
	speed = typeof speed !== 'undefined' ? speed : 400;
	var offset = ($(wrapper_id).width()/2) - $(element_id).width()/2;
	$(wrapper_id).scrollTo(element_id, speed, {offset: 0 - offset});
}

function submitPost()
{
	$.each($('audio'), function () {
    	this.pause();
	});
	$('.audio_progress').width(0);
	$('.audio_buffered').width(0);
	$('.audio_remove').remove();
	$('.audio_button').each(function(){
		$(this).css('background-image', 'url(../Images/play-button.png)');
	});
	$('#status_text').children('img').attr('onclick', "initiateTheater(this.src);");
	var text = $('#status_text').html();
	if(text != "")
	{
		$.post("Scripts/update_status.php", { status_text: text, group_id: share_group_id}, function(data)
		{
			if(data == "")
			{
				scrollH('#' + share_group_id, '#feed_wrapper_scroller', 0);
				$('#'+share_group_id).click();
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
	scrollH("#"+value, "#feed_wrapper_scroller", 0);
}


// POPUP

function dialog(title, content, button, properties)
{	
	if(properties.modal == true)
	{
		$('body').append("<div class='background-overlay'></div>");
	}
	else
	{
		$('body').append("<div onclick='removeDialog()' style='opacity:0.1' class='background-overlay'></div>");
	}
	$('body').append("<div class='dialog_container'></div>");
	$('.dialog_container').append("<div class='dialog_title'>" + title + "<span onclick='removeDialog();' class='dialog_close_button'>x</span></div>");

	if(content.type == "text")
	{
		$('.dialog_container').append(content.content);
	}
	else if(content.type == "html")
	{
		$('.dialog_container').append(content.content);
	}
        $('dialog_container').width(properties.width);
	$('.dialog_container').append("<div class='dialog_buttons'><img class='dialog_loading' src='Images/ajax-loader.gif'></img>"+
		"<button onclick='" + button.onclick + "' style='float:right;' class='pure-button-"+button.type+" small'>" + button.text + "</button></div>");
	alignDialog();
}	

function alignDialog()
{
	var width = $('.dialog_container').width();
	var height = $('.dialog_container').height();
	$('.dialog_container').css({
		'margin-left' 	: '-' + width  / 2 	+ "px",
		'margin-top' 	: '-' + height / 2 	+ "px",
	});
}

function removeDialog()
{
	$('.background-overlay').remove();
	$('.dialog_container').fadeOut(function(){$(this).remove();});
}
// #POPUP
// #HOME
$(function() {
    $('body').addClass('scroll_thin');
});
function dialogLoad(start)
{
	if(start == "stop")
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
	if(comment_text == "")
	{
		comment_text = $('div[actual_id="comment_' + post_id + '"]').html();
	}
	$.post("Scripts/extends.class.php", { comment_text: comment_text, post_id: post_id, action : 'submitComment'}, function(data)
	{
		$('#comment_' + post_id).html("");
		$('#comment_' + post_id).blur();
		$('div[actual_id="comment_' + post_id + '"]').blur();
		refreshContent(post_id);
	});
}

function refreshContent(id)
{
	var number_of_updates = 10;
	var updates_done = 0;
	refreshPure(id); //Refresh immediately after comment
	var refresh_interval = setInterval(function(){refreshPure(id)},10000);
	if(++updates_done >= number_of_updates)
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
		$.post('Scripts/home.class.php', { activity_id : id}, function(response)
		{
			var activity_id = $("#theater-info-container").attr("activity_id");
			if(typeof activity_id !== "undefined")
			{
				$('.theater-info-container').children('.comments').find('.comment_box').children('div.single_comment_container, hr.post_comment_seperator').remove();
				$('.theater-info-container').children('.comments').find('.comment_box').prepend(response);
			}
			else
			{
				$('#comment_div_' + id).children('div.single_comment_container, hr.post_comment_seperator').remove();
				$('#comment_div_' + id).prepend(response);
			}
		});
	}
}

function submitlike(id, receiver_id, type)
{
	$.post("Scripts/home.class.php", { id: id, type: type, receiver_id : receiver_id, action : "like"}, function(data)
	{
		if(type == 1)
		{
			$('#'+id+'likes').text(data);
		}
		else
		{
			$('#'+id+'dislikes').text(data);
		}
	});
}

function show_Confirm(post_id)
{
	$('#delete1_post_'+post_id).hide();
	$('#delete_post_'+post_id).css('visibility', 'visible').hide().slideDown('slow');
}

function delete_post(post_id)
{
	$.post("Scripts/home.class.php", { action : "deletePost", post_id: post_id}, function(){
		$('#post_height_restrictor_'+post_id).slideUp(function(){$(this).remove();});
	});
}

function initiateTheater(src, id, no_text)
{
	var checker = $('.theater-picture-container');
	if(checker.length != 0)
	{
		$('.theater-picture').remove();
		$(".background-overlay").remove();
	}
	$('body').append("<div hidden onclick='hideTheater();' class='background-overlay'></div>");
	$('body').append("<div id='theater-picture' class='theater-picture'></div>");
	$('.theater-picture').append("<div onclick='hideTheater();' class='close-theater'></div>");
	$('.theater-picture').append("<div id='theater-picture-container' style='background-image: url(&apos;"+src+"&apos;);' class='theater-picture-container'></div>");
	if(id != "no_text")
	{
		$('.theater-picture').append("<div id='theater-info-container' class='theater-info-container scroll_medium'></div>");

		var info_html = $('#single_post_' + id).clone();
		info_html = info_html.find("*").each(function()
		{
			var previous_id = $(this).attr("id");
			if(previous_id != "")
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
	$('.theater-picture').show();
	$("body").css("overflow", "hidden");
	adjustTheater(no_text, src);
}
// END HOME
function adjustTheater(no_text, src)
{
	var theater = $('.theater-picture');
	theater.css('margin-top', getViewPortHeight()/2 - $(theater).height()/2 - 20);	
	if(no_text == 'no_text')
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
	$.post("Scripts/group_actions.php", {action : "join", group_id : group_id, invite_id : invite_id}, function(response)
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
	$.post("Scripts/group_actions.php", {action: "reject", group_id:group_id, invite_id: invite_id}, function(response)
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
	if(action == "remove")
	{
		$(element).children('div').slideUp( function(){});
		var new_onclick = $(element).attr('previous_onclick');
		$(element).attr('onclick', new_onclick);
		$.post('Scripts/current_directory_cookie.php', {dir:"../"}, function(response)
		{	
			current_directory = response;
		});
		$('#loading_icon').fadeOut();
	}
	else
	{
		$.post('Scripts/files.class.php', {parent_folder:parent_folder, action:"getContents", actions:actions}, function(response)
		{	
			var previous_onclick = $(element).attr("onclick");
			$(element).attr("previous_onclick", previous_onclick);
			$(element).attr("onclick", "if(event.stopPropagation){event.stopPropagation();}event.cancelBubble=true;get_folder_contents(this, 'remove');");
			if(animation != "none")
			{
				$("<div style='padding:6px;'></div>" + response).appendTo($(element)).hide().slideToggle();
			}
			else
			{
				$("<div style='padding:6px;'></div>" + response).appendTo($(element));
			}
			$('#loading_icon').fadeOut();
		});
		$.post('Scripts/current_directory_cookie.php', {dir:dir}, function(response)
		{
			current_directory = response;
		});
	}
}
function deleteFile(element, id)
{
	$('#loading_icon').show();
	$.post('Scripts/files.class.php', {action:"delete", id: id}, function(response)
	{	
		$('#file_div_hidden_container_' + id).remove();
		$('#file_div_' + id).slideUp();
		$('#loading_icon').fadeOut();
	});
}

function createFolder(parent_folder)
{
	$('#loading_icon').show();
	var folder_name = $('#creat_folder_name').val();
	$.post("Scripts/files.class.php", {action : "createFolder", parent_folder: parent_folder, folder_name: folder_name}, function(response)
	{
		if("error".indexOf(response) > 0)
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
	});
}
function audioPlay(id)
{
	var src = $('#image_' + id).css('background-image');
	if(src.indexOf("play") >= 0)
	{
		startAudioInfo(id);
		$("#audio_info_"+id).slideDown();
		$('#audio_' + id).get(0).play();
		$('#image_' + id).css('background-image', "url('../Images/pause-button.png')");
		$('#audio_play_icon_' + id).show();
		$('#audio_play_icon_seperator_' + id).show();
	}
	else
	{
		$('#audio_' + id).get(0).pause();
		$('#image_' + id).css('background-image', "url('../Images/play-button.png')");
		$('#audio_play_icon_' + id).hide();
		$('#audio_play_icon_seperator_' + id).hide();
	}
	//console.log($('#audio_play_icon_' + id).length + "/File ID = " + id);
}
function startAudioInfo(id)
{
	$("#audio_" + id).bind('progress', function(){
		var track_length = $("#audio_" + id).get(0).duration;
		var secs = $("#audio_" + id).get(0).buffered.end(0);
		var progress = (secs/track_length) * 100;
		$("#audio_buffered_" + id).css('width', progress + "%");
	});
	$("#audio_" + id).bind('timeupdate', function(){
		var track_length = $("#audio_" + id).get(0).duration;
		var secs = $("#audio_" + id).get(0).currentTime;
		var progress = (secs/track_length) * 100;
		$("#audio_progress_" + id).css('width', progress + "%");
		var track_length = $("#audio_" + id).get(0).duration;
		var secs = $("#audio_" + id).get(0).buffered.end(0);
		var progress = (secs/track_length) * 100;
		$("#audio_buffered_" + id).css('width', progress + "%");

		var minutes = Math.floor(track_length / 60);
		var seconds = Math.floor(track_length - minutes * 60);

		var done_secs = $("#audio_" + id).get(0).currentTime;
		var done_minutes = Math.floor(done_secs / 60);
		var done_remaining_secons = Math.floor(done_secs - done_minutes * 60);
		$("#audio_time_" + id).html(done_minutes + ":" + pad(done_remaining_secons) + " - " + minutes + ":" +seconds);
	});

	$("#audio_" + id).bind('canplaythrough', function(){
		$('#audio_buffered_'+id).css('background-color', 'grey');
	});

	$("#audio_progress_container_" + id).click(function(e)
	{
		var x = $(this).offset().left;
		var width_click = e.pageX - x;
		var width = $(this).width();
		var percent_width = (width_click / width) * 100;
		$("#audio_progress_" + id).css('width', percent_width + "%");

		var secs = $("#audio_" + id).get(0).duration;
		var new_secs = secs * (percent_width/100);

		$("#audio_" + id).get(0).currentTime = new_secs;
	});
}

function removeAudio(id)
{
	$('#audio_container_' + id).remove();
}

function showUpload(type)
{
	if(type == "folder")
	{
		$('#folder').trigger('click');
		$('#folder_upload_option').hide();
		$('#folder_upload_dialog').show();

		$('#file_upload_option').show();
		$('#file_upload_dialog').hide();
	}
	else if(type == "file")
	{
		$('#file').trigger('click');
		$('#file_upload_option').hide();
		$('#file_upload_dialog').show();

		$('#folder_upload_option').show();
		$('#folder_upload_dialog').hide();
	}
}

function addToStatus(type, path, id)
{	
	if(type == "Folder")
	{
		$('#status_text').append("<a href='files?pd=" + path + "&u=" + id + 
                        "'><div style='background-repeat:no-repeat;background-size:contain;background-image: url(&quot;Images/yellow-folder-icon.jpg&quot;); height:80px;width:80px;cursor:pointer;'></div></a>");
	}
	else if(type == "Image")
	{
		$('#status_text').append("<br><img onclick='initiateTheater(this.src);adjustTheater(\"no_text\")' src='" + path + "'></img><br>");
	}
	else if(type == "Audio")
	{
		var rndm = Math.floor(Math.random() * 30);
		var n = path.lastIndexOf("/");
		var title = path.substring(n + 1);
		var text = '<?php echo $system->audioPlayer(null,null,true, "blank"); ?>';
		text = text.replace(/:::path:::/g, path);
		text = text.replace(/:::name:::/g, title);
                text = text.replace(/:::uid:::/g, rndm);
		$('#status_text').append(text);
		
	}
	else if(type == "Video")
	{

	}
	else
	{

	}
	$('#status_text').focus();

}
function pad(number) 
{   
     return (number < 10 ? '0' : '') + number;
}
</script>
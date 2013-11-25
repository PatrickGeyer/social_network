$(function(){
	alignNavFriend();
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


	$('body').delegate('div[data-placeholder]', 'keydown keypress input', function() 
	{
		if ($(this).html() == "") 
		{
			//this.dataset.divPlaceholderContent = 'true';
		}
		else 
		{
			$(this).attr('data-placeholder', '');
		}
	});

	$(document).click(function()
	{
		$('#names_universal').hide();
	});
});

$( window ).resize(function() {
	alignNavFriend();
	adjustTheater();
});

function alignNavFriend()
{
	var container_left = $('.container_headerbar').offset().left;
	$('.navigation').css('width', 200);
	container_left = container_left - $('.navigation').width();

	$('#logo').css('left', container_left);

	$('.navigation').css('left', container_left);

	$('#friends_container').css('left', container_left);
	var top_height = $('.navigation').offset().top + $('.navigation').height();
	$('#friends_container').css('top', top_height + 22);
	$('#friends_container').css('width', 197);

	var height = $('#friend_load').height();
	$('#friends_bar').css('max-height', height);

	$('.messagecomplete').css('top', top_height + 22);
	$('.messagecomplete').css('left', container_left + 1);
}

function submitPost()
{
	var text = $('#status_text').html();
	if(text != "")
	{
		if(typeof group_id !== "undefined")
		{
			$.post("Scripts/update_status.php", { status_text: text, share: share_with, group_id: group_id}, function(data)
			{
				if(data == "")
				{
					location.reload();
				}
				else
				{
					alert(data);
				}
			});
		}
		else
		{
			$.post("Scripts/update_status.php", { status_text: text, share: share_with}, function(data)
			{
				if(data == "")
				{
					location.reload();
				}
				else
				{
					alert(data);
				}
			});
		}	
	}
}

// POPUP

function dialog(html, buttons, close_enabled)
{
	$('body').append("<div onclick='removeDialog()' class='background-overlay'></div>");
	$('body').append("<div class='dialog_container'></div>");
	$('.dialog_container').append(html);
//	buttons.forEach(function(button)
//	{

	//});

}	
function removeDialog()
{
	$('.background-overlay').remove();
	$('.dialog_container').remove();
}
// #POPUP

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
				$.post('Scripts/home.class.php', { activity_id : activity_id }, function(response)
				{
					$('.theater-info-container').children().each(function()
					{
						if(this.className == "comments")
						{
							$(this).children("div").each(function()
							{
								$(this).children('table, hr').remove();
								$(this).prepend(response);
							});
						}
					});
				});
			}

			$('#comment_div_' + id).children('table, hr').remove();
			$('#comment_div_' + id).prepend(response);
		});
	}
}

function submitlike(id, receiver_id, type)
{
	$.post("Scripts/extends.class.php", { id: id, type: type, receiver_id : receiver_id, action : "like"}, function(data)
	{
		if(type==1)
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
	$('#delete_post_'+post_id).css('visibility','visible').hide().fadeIn('slow');
}

function delete_post(post_id)
{
	$.post("Scripts/delete_post.php", { post_id: post_id}, function(){
		$('#single_post_'+post_id).slideUp();
		$('#commentcomplete_'+post_id).slideUp();
		$('#comment_time_'+post_id).slideUp();
		$('#delete_post_'+post_id).slideUp();
	});
}

function initiateTheater(src, id)
{
	var checker = $('.theater-picture-container');
	if(checker.length != 0)
	{
		$('.theater-picture').remove();
		$(".background-overlay").remove();
	}
	$('body').append("<div hidden onclick='hideTheater();' class='background-overlay'></div>");
	$('body').append("<div id='theater-picture' class='theater-picture'></div>");
	$('.theater-picture').append("<div style='background-color:black;' class='close-theater'>x</div>");
	$('.theater-picture').append("<div id='theater-picture-container' style='background-image: url("+src+");' class='theater-picture-container'></div>");
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
	adjustTheater("no_text", src);
}

function adjustTheater(no_text, src)
{
	var theater = $('.theater-picture');

	theater.css('margin-top', $(window).height()/2 - $(theater).height()/2 - 20);	
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
		
	});
}
function rejectGroup(group_id, invite_id)
{
	$.post("Scripts/group_actions.php", {action: "reject", group_id:group_id, invite_id: invite_id}, function(response)
	{
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
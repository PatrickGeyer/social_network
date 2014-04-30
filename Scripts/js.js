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
Application.prototype.date = function(time) {
    this.time = time;
    this.date = new Date(this.time * 1000);
    this.tokens = [
        {time: 1, unit: 'second'},
        {time: 60, unit: 'minute'},
        {time: 3600, unit: 'hour'},
        {time: 86400, unit: 'day'},
        {time: 604000, unit: 'week'},
        {time: 2592000, unit: 'month'},
        {time: 31536000, unit: 'year'},
    ];
    this.short = {
        'second': 'sec',
        'minute': 'min',
        'hour': 'hr',
        'day': 'd',
        'week': 'wk',
        'month': 'mo',
        'year': 'yr',
    };

    this.timeAgo = function(length) {
        this.timeAgo = Math.floor((new Date().getTime() / 1000 - this.time));
        var string = '';

        for (var i = this.tokens.length - 1; i > 0; i--) {
            if (this.timeAgo < this.tokens[i].time) {
                continue;
            }
            var numberOfUnits = Math.floor(this.timeAgo / this.tokens[i].time);

            if (numberOfUnits < 5 && this.tokens[i].unit == "second") {
                string = "Just Now";
            } else {
                if (numberOfUnits == 1) {
                    string += 'a ' + this.tokens[i].unit + ((numberOfUnits > 1) ? 's' : '');
                } else {
                    string += numberOfUnits + ' ' + this.tokens[i].unit + ((numberOfUnits > 1) ? 's' : '');
                }
            }
            if (this.tokens[i].unit === "week") {
                return string + " ago";
            }
            this.timeAgo -= numberOfUnits * this.tokens[i].time;
//            console.log("Num:" + numberOfUnits + " time: " + this.timeAgo + " unit:" + this.tokens[i].unit);
            if (length === "short") {
//            console.log(this.tokens[i].unit);
                return numberOfUnits + " " + this.short[this.tokens[i].unit];
            }
        }

        return string + " ago";
    };
};

/****************************************************
 * 0. Upload                                        *
 ****************************************************/

Application.prototype.upload = function(files) {
    this.files = files;

    this.start = function() {
    };
    this.progress = function() {
    };
    this.end = function() {
    };

    this.push = function() {
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
        this.start();

        for (var i = 0; i < this.files.length; i++) {
            (function(count, session, Upload) {
                var file = Upload.files[count].file;
                var formdata = new FormData();
                formdata.append("file", file);
                formdata.append("action", 'upload');
                formdata.append("parent_folder", parent_folder);
                var xhr = new XMLHttpRequest();
                xhr.upload.onprogress = function(event) {
                    Application.prototype.upload.progressHandler(event, "" + session + count, Upload.progress());
                };
                xhr.onload = function() {
                    Application.prototype.upload.completeHandler(Upload, "" + session + count, Upload.end(), name);
                };
                xhr.addEventListener("error", Application.prototype.upload.errorHandler, false);
                xhr.addEventListener("abort", Application.prototype.upload.abortHandler, false);
                xhr.open("post", "Scripts/files.class.php");
                xhr.send(formdata);
                var file_container = $("<div class='upload_preview'>");
                file_container.attr('id', "" + session + count + "_upload_preview");
                file_container.append(file.name);
                file_container.append("<br />");
                file_upload_container.append(file_container);
                Application.prototype.UI.progress.create(file_container, "" + session + count);
            })(i, session, this);
        }
    };

    this.progress = function(event, id, callback) {
        $('#loading_icon').show();
        var percent = (event.loaded / event.total) * 100;
        percent = Math.round(percent);
        Application.prototype.UI.progress.update(id, percent);
        callback(percent);
    };

    this.complete = function(event, id, callback) {
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
        callback($.parseJSON(event.responseText));
    };

    this.error = function(event) {
        if (this.session > 0) {
            this.session--;
        }
        alert('upload failed');
        $('#loading_icon').fadeOut();
    };

    this.abort = function(event) {
        if (this.session > 0) {
            this.session--;
        }
    };
};

/****************************************************
 * 1.3 Files                                         *
 ****************************************************/
Application.prototype.file = function(file) {
    this.getById = function(file_id) {
        return this.items[file_id] || null;
    };

    if (file !== false) {
        this.playing = false;
        this.file = file;
        this.items[this.file.id] = this;

        this.file.comment = new Application.prototype.Comment(this.file.activity);
        /****************************************************
         * 1.3.1 Files - Print                               *
         ****************************************************/

        this.view = function() {
            $.post("Scripts/files.class.php", {file_id: this.file.id, action: "view"}, function() {
            });
        };

        this.list = function(element, type, callback) {
            callback = typeof callback !== 'undefined' ? callback : function() {
            };
            $.post('Scripts/home.class.php', {type: type, action: "file_list"}, function(response) {
                $(element).html(response);
                $(element).mCustomScrollbar(SCROLL_OPTIONS);
                callback();
            });
        };

        this.initializeWaveForm = function() {
            $('[uid]').each(function() {
                createWaveForm($(this).attr('uid'), function() {
                });
            });
        };

        this.rename = function(id, text) {
            $.post('Scripts/files.class.php', {action: "rename", file_id: this.file.id, name: text}, function() {
            });
        };
        this.print = function(activity_type) {
            this.file.name = typeof this.file.name !== 'undefined' && !isEmpty(this.file.name) ? this.file.name : 'Untitled';
            this.file.description = typeof this.file.description !== 'undefined' && !isEmpty(this.file.description) ? this.file.description : '';
            this.file.time = typeof this.file.time !== 'undefined' && !isEmpty(this.file.time) ? this.file.time : 'No Date';
            this.file.type_preview = typeof this.file.type_preview !== 'undefined' && !isEmpty(this.file.type_preview) ? this.file.type_preview : file.thumb_path;

            var string = $('<div class="post_feed_item"></div>');
            if (this.file.type == "Audio") {
                string.addClass("post_media_double");
                string.append(this.printDoc());
            } else if (this.file.type == "Image") {
                string.addClass("post_media_photo");
                string.append(this.printDoc());
            } else if (this.file.type == "Video") {
                string.append(this.printDoc());
                string.addClass("post_media_video");
//        post_content += video_player(file, classes, "height:100%;", "home_feed_video_", true);
            } else if (this.file.type === "WORD Document"
                    || this.file.type === "PDF Document"
                    || this.file.type === "EXCEL Document"
                    || this.file.type === "PPT Document"
                    || this.file.type === "Folder") {
                string.css('height', 'auto');
                string.addClass("post_media_double");
                string.append(this.printDoc());
            } else if (this.file.type == "Folder") {
                string.css('height', 'auto');
                string.addClass("post_media_double");
                string.append(this.printDoc());
            } else if (this.file.type == "Webpage") {
//            post_classes += "post_media_full";
//            post_styles += "height:auto;";
//            post_content += "<table style='height:100%;'><tr><td rowspan='3'>";
//            post_content += "<div class='post_media_webpage_favicon' style='background-image:url(&quot;";
//            post_content += this.file.web_favicon + "&quot;);'></div></td>" + "<td>";
//            post_content += "<a class='user_preview_name' target='_blank' href='";
//            post_content += this.file.URL + "'><span style='font-size:13px;'>";
//            post_content += this.file.web_title + "</span></a></div></td></tr>";
//            post_content += "<tr><td><span style='font-size:12px;' class='user_preview_community'>";
//            post_content += this.file.web_description + "</span></td></tr>";
//            post_content += "</table>";
            } else {
                string.addClass("post_media_full");
                string.append(this.printDoc());
            }
            var actions = $("<div class='top_right_actions'></div>");
            if (this.file.user_id == USER_ID && activity_type != 'File') {
                actions.append("<i class='fa fa-times delete_cross delete_cross_top remove_event_post'></i>");
            }
            string.append(actions);

            if (activity_type == "Text" && typeof this.file.activity != 'undefined') {
                string.append(this.file.comment.print());
            }

            if (activity_type == "File" && typeof file.share != 'undefined') {
                //WHO IS FILE SHARED WITH?
            }

            string.data("activity_id", this.file.activity.id).data("file_id", this.file.id);

            return string;
        }
    }
    ;

    this.printDoc = function() {
        var string = $('<div></div>');
        var link = "files?f=" + this.file.id;
        this.file_activity_section = $("<div class='file_activity_section'></div>");
        this.preview = $("<div class='post_media_preview'></div>");
        this.actions = $("<div style='margin-top:5px;'></div>");
        this.stats = $("<div style='margin-top:5px;'></div>");

        if (this.file.type == "Folder") {
            this.file.type_preview = FOLDER_THUMB;
            link = "files?pd=" + this.file.enc_parent_folder_id;
        } else if (this.file.type == "Image") {
            this.preview.addClass("post_media_photo");
//            preview_styles += " width:auto;height:auto; ";
            this.preview.append("<img style='max-width:150px;max-height:150px;' src='" + this.file.thumb_path + "'></img>");
        } else if (this.file.type == "Video") {
            this.preview.addClass("post_media_photo");
            this.preview.append("<img style='position:absolute;top:40%;left:40%;' src='" + VIDEO_BUTTON + "'></img>");
            this.preview.css("background-image", "url(\"" + this.file.thumbnail + "\")");
        } else if (this.file.type == "Audio") {
            this.preview.css('background-image', 'none !important');
            this.preview.append("<i class='fa fa-music'></i>");
            this.preview.append(this.audioPlayer('button'));
            this.file_activity_section.append(this.audioPlayer('timeline'));
        } else {
            this.preview.css('background-image', 'url(' + this.file.type_preview + ')');
        }

        this.file_activity_section
                .append(this.user_link = $("<a class='user_preview_name' target='_blank' href='" + link + "'></a>")
                        .append($("<p class='ellipsis_overflow' style='word-break:break-word; '>" + this.file.name + "</p>")))
                .append("<span style='font-size:12px;' class='post_comment_time'>" + this.file.description + "</span>")
                .append(this.stats)
                .append(this.actions);

        this.stats.append("<i class='heart_like_icon fa fa-heart'></i><span class='post_comment_time post_like_count'>" + this.file.like.count + "</span>");

        if (typeof this.file.activity.comment != 'undefined') {
            this.stats.append("<i class='fa fa-comment heart_like_icon'></i><span class='post_comment_time post_comment_count'>"
                    + (parseInt(this.file.activity.comment.comment.length) + parseInt(this.file.activity.comment.hidden)) + "</span>");
        }
        this.stats.append("<i class='fa fa-eye heart_like_icon'></i><span class='post_comment_time'>" + this.file.view.count + "</span><br />");

        this.actions.append("<a class='no-ajax' href='download.php?id=" + this.file.id + "'>" + "<button class='pure-button-green'><i class='fa fa-cloud-download'></i><span></span></button></a>");
        this.actions.append("<button has_liked='" + (this.file.activity.stats.like.has_liked === true ? "true" : "false") + "' class='activity_like_text post_like_activity "
                + "pure-button-neutral " + (this.file.activity.stats.like.has_liked === true ? " pure-button-blue" : "") + "'>" + "<i class='fa fa-heart'></i></button>");

        string.append(this.preview).append(this.file_activity_section);
        return string;
    };

    this.print_row = function() {
        var string = $("<div data-file_id='" + this.file.id + "' class='contentblock'></div>");

        if (this.file.type != "Folder") {
            string.addClass('files');
        } else {
            string.addClass('folder');
        }

        string.append("<div class='files_icon_preview' style='background-image:url(\"" + this.file.type_preview + "\");'></div>");
        string.append("<p class='files ellipsis_overflow'>" + this.file.name + "</p>");
        var actions = $("<div class='files_actions'></div>");

        actions.append("<a href='download.php?id=" + this.file.id + "' download><div class='files_actions_item files_actions_download'></div></a>");
        actions.append("<hr class='files_actions_seperator'>");

        actions.append("<div class='files_actions_item files_actions_delete' "
                + "onclick='deleteFile(this, " + this.file.id + ");if(event.stopPropagation){event.stopPropagation();}"
                + "event.cancelBubble=true;'></div></td><td>"
                + "<hr class='files_actions_seperator'></td><td>"
                + "<div class='files_actions_item files_actions_share' data-file_id='" + this.file.id + "'></div></td>");

        var file = $("<div class='file_hidden_container'></div>");
        file.append(this.print("File"));
        string.append(file);
        return string;
    };

    this.audioPlayer = function(part) {
        var string = $('<div data-path="' + this.file.thumb_path + '" uid="' + this.file.uid + '" data-file_id="' + this.file.id + '"></div>');
        if (part == 'all') {
            string.append($('<div class="audio_container"></div>').append(this.audioButton()).append(this.audioInfo()));
        } else if (part == "button") {
            string.append(this.audioButton());
        } else if (part == "info") {
            string.append(this.audioInfo());
        } else if (part == 'timeline') {
            string.append(this.audioTimeline());
        }
        return string;
    };

    this.audioButton = function() {
        var self = this;
        self.audio_button = $('<div class="audio_button"></div>').on('click', function() {
            self.audioPlay();
        });
        self.audio_button.append(self.audio = $('<audio style="display:none;"><source src="' + self.file.path + '"></source><source src="'
                + self.file.thumb_path + '"></source></audio>'));
        self.audio_button.append('<div class="audio_button_inside"></div>').append(self.loader = $('<div class="audio_loader"></div>'));
        return self.audio_button;
    };

    this.audioInfo = function(file) {
        return self.audio_info = $('<div class="audio_info"></div>')
                .append('<div class="ellipsis_overflow audio_title">' + this.file.name + '</div>')
                .append(this.audioTimeline())
                .append(this.audio_time = $('<div class="audio_time">0:00</div></div>'));
    };

    this.audioTimeline = function() {
        var self = this;
        return self.progress_container = $('<div class="audio_progress_container"></div>')
                .append(self.progress = $('<div class="audio_progress"></div>'))
                .append(self.buffered = $('<div class="audio_buffered"></div>'))
                .append('<div class="audio_line"></div></div>');
    };

    this.audioPlay = function() {
        if (!this.playing) {
            this.startAudioInfo();
            this.audio_button.addClass('audio_playing');
            this.playing = true;
        } else {
            this.audio[0].pause();
            this.audio_button.removeClass('audio_playing');
            this.playing = false;
        }
    };

    this.startAudioInfo = function(start, progress, end) {
        this.view();

        var headerControl = $("<div></div>");
        headerControl.append(this.audioPlayer('all'));
        $('.global_media_container').html(headerControl);

        this.loader.fadeIn();
        this.audio.get(0).play();
        this.audio.volume = 1;
        var self = this;

        this.audio.bind('loadedmetadata', function() {
            self.audio.bind('progress', function() {
                var track_length = self.audio.get(0).duration;
                var secs = self.audio.get(0).buffered.end(0);
                var progress = 0;
                if (secs > 0 && track_length > 0) {
                    progress = (secs / track_length) * 100;
                }
                self.buffered.css('width', progress + "%");
            });

            self.audio.bind('timeupdate', function() {
                var track_length = self.audio.get(0).duration;
                var secs = self.audio.get(0).currentTime;
                var progress = (secs / track_length) * 100;
                self.progress.css('width', progress + "%");
                var minutes = Math.floor(track_length / 60);
                var seconds = Math.floor(track_length - minutes * 60);
                var done_secs = self.audio.get(0).currentTime;
                var done_minutes = Math.floor(done_secs / 60);
                var done_remaining_secons = Math.floor(done_secs - done_minutes * 60);
                self.audio_time.html(done_minutes + ":" + pad(done_remaining_secons) + " - " + minutes + ":" + seconds);
                self.loader.fadeOut();
            });

            self.audio.bind('canplaythrough', function() {
                self.buffered.css('background-color', 'grey');
            });

            self.audio.bind('ended', function() {
                self.audio.get(0).currentTime = 0;
                self.audio_button.removeClass('audio_playing');
            });

            $(self.progress_container).click(function(e) {
                var x = $(this).offset().left;
                var width_click = e.pageX - x;
                var width = $(this).width();
                var percent_width = (width_click / width) * 100;
                self.progress.css('width', percent_width + "%");
                var secs = self.audio.get(0).duration;
                var new_secs = secs * (percent_width / 100);
                self.audio.get(0).currentTime = new_secs;
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

    this.audioVolume = function(vol) {
        $('audio').each(function() {
//        console.log($(this).attr('id'));
            $(this).get(0).volume = vol;
        });
    };

    this.createWaveForm = function(uid, progress) {
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

    this.removeAudio = function(id) {
        $('#audio_container_' + id).remove();
    };

    this.videoFrame = function(uid) {
        return "<div class='video_box'><video id='vid" + uid + "'></video></div>";
    };

    this.videoPlayer = function(file, onload, onplay, onend) {
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

    this.videoPlay = function(id) {
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
    };
};
Application.prototype.file.prototype.items = {};

Application.prototype.FileList = function(type) {
    this.type = type;
    $.get('Scripts/files.class.php', {action: 'list', type: this.type}, function(response) {
        response = $.parseJSON(response);
    });
    this.print = function() {
    };
}
;

Application.prototype.Folder = function(list) {
    this.files = list;

    this.print = function() {
        var string = $('<div></div>');
        for (var file in this.files) {
            string.append(new Application.prototype.file(this.files[file]).print_row());
        }
        return string;
    };
};

Application.prototype.theater = function() {
    this.active = false;
    this.removeTime = 0;
    this.previousUrl = '';

    this.initiate = function() {
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
            var string = Application.prototype.feed.item.homify(response);
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
    this.adjust = function() {
        //adjustSwitches();

        this.theater_picture.css('margin-top', "-" + this.theater_picture.height() / 2);
        Application.prototype.UI.resizeToMax(this.theater_picture_container.children('img:first'), 690, 85);

        // $('#theater-info-container').mCustomScrollbar("update");
    };
    this.loaded = function() {
        this.adjust();
        this.loader.remove();
    };
    this.remove = function() {
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
};
//    <div class="chatcomplete contentblock" data-chat_room="<?php echo $single_group['id']; ?>">
//        <div class='chatheader'>
//            <div class="chat-head"></div>
//            <div class='chat-info'>
//                <div class='chat_feed_selector <?php echo ($chat_feed == $single_group['id'] ? "active_feed" : "") ?>'>
//                    <p class='chat_header_text ellipsis_overflow'><?php echo $single_group['name']; ?></p>
//                </div><br />
//                <div class='chat-preview'></div>
//            </div>
//        </div>
//        <div class="chat-container">
//            <div></div>
//            <div class="chatoutput" data-chat_room="<?php echo $single_group['id']; ?>" <?php echo($chat_feed != $single_group['id'] ? "style='display:none'" : ""); ?>>
//                <div class='chat_loader' style='display:none;'><div class='loader_outside_small'></div><div class='loader_inside_small'></div></div>
//                <ul style='max-width:225px;' class='chatreceive'>
//                </ul>
//            </div>
//            <div class='text_input_container'>
//                <textarea id="text" class="chatinputtext autoresize"  placeholder="Press Enter to send..."></textarea>
//            </div>
//        </div>
//    </div>
Application.prototype.Chat = function(chat_id, name) {
    this.id = chat_id;
    this.entry = new Array();
    this.oldest = 0;
    this.newest = 998999999;
    this.getting_previous = false;
    this.last = false;
    this.name = name;
    this.items = new Array();

    this.all.push(this);


    this.container = $("<div class='chatcomplete contentblock'></div>")
            .append(this.header = $("<div class='chatheader'></div>")
                    .append(this.chathead = $("<div class='chat-head'></div>"))
                    .append(this.chatinfo = $("<div class='chat-info'></div>")
                            .append(this.chat_feed_selector = $("<div class='chat_feed_selector'></div>")
                                    .append(this.chat_header_text = $("<p class='chat_header_text ellipsis_overflow'></p>"))
                                    .append(this.name))
                            .append(this.preview = $("<div class='chat-preview'></div>"))))
            .append(this.chatcontainer = $("<div class='chat-container'></div")
                    .append("<div></div>")
                    .append(this.chatoutput = $("<div class='chatoutput'></div>")
                            .append(this.loader = $("<div class='chat_loader'></div>")
                                    .append("<div class='loader_inside_small'></div>"))
                            .append(this.list = $("<ul class='chatreceive'></ul>")))
                    .append($("<div class='text_input_container'></div>")
                            .append(this.input = $("<textarea class='chatinputtext autoresize' placeholder='Press Enter to send...'></textarea>"))));


    $('.right_bar_container').append(this.container);
    this.iniScroll();
    this.sendRequest('true');

    var self = this;
    this.input.on("propertychange keydown input change", function(e) {
        if (e.keyCode == 13) {
            if (e.shiftKey !== true) {
                e.preventDefault();
                self.submit($(this).val());
            }
        }
//        self.detectChange();
    });
};
Application.prototype.Chat.prototype.all = new Array();
Application.prototype.Chat.prototype.get = function() {
    return this.all;
};

Application.prototype.Chat.prototype.iniScroll = function() {
    var self = this;
    this.chatoutput.on('scroll', function() {
        if ($(this)[0].scrollTop === 0) {
            self.getPrevious();
        }
    });
};

Application.prototype.Chat.prototype.getPrevious = function() {
    var self = this;
    if (self.getting_previous == false) {
        self.getting_previous = true;

        var new_oldest = Array.min(self.entry) - 20;
        if (new_oldest < 0) {
            new_oldest = 0;
        }

        if (self.last_chat != true) {
            var element = self.chatoutput.find('.single_chat:first');
            self.loader.slideDown('fast');
            var object = {chat: self.id, all: "previous", oldest: new_oldest, newest: self.oldest - 1};
            $.get("Scripts/chat.class.php", object, function(response) {
                response = $.parseJSON(response);
                if (response.length == 0) {
                    self.last_chat = true;
                    self.list.prepend("<div class='timestamp'><span>Start of Conversation</span></div>");
                    self.loader.slideUp('fast');
                    return;
                }
                self.list.prepend(self.styleResponse(response, self.id));
                self.loader.slideUp('fast');
                self.getting_previous = false;
                element = element.offset().top;
                self.chatoutput.scrollTop(element);
            });
        }
    }
};

Application.prototype.Chat.prototype.sendRequest = function(all) {
    var self = this;
    $.get("Scripts/chat.class.php", {chat: self.id, all: all, oldest: 0, newest: self.newest}, function(response) {
        $('[data-chat_room="' + self.id + '"] .chat_loader').slideUp('fast');
        response = $.parseJSON(response);

        if (all == 'true') {
            $('.chatcomplete').fadeIn("fast");
            $(self.list).append(self.styleResponse(response));
            $(self.chatoutput).scrollTop(self.chatoutput.get(0).scrollHeight);

        } else {
            $(self.list).append(self.styleResponse(response));
        }
        if (all == 'false' && response.length > 0) {
            for (var i = response.length - 1; i >= 0; i--) {
                if (response[i]['user_id'] != USER_ID) {
                    $('#chat_new_message_sound').get(0).play();
                }
            }
            if (self.id != chat_room) {
                $('.chat_feed_selector[chat_feed="' + self.id + '"] *').css('color', 'red');
                alert('unread in another chat');
            }
        }
        var timeout = 3000;
        if (self.id != chat_room) {
            timeout = 10000;
        }
        setTimeout(function() {
            self.sendRequest('false');
        }, timeout);
        self.detectChange();
    });
};

Application.prototype.Chat.prototype.detectChange = function(self) {
    this.scroll2Bottom(false);
};

Application.prototype.Chat.prototype.styleResponse = function(response) {

    var final = $('<div></div>');
    if (response.length == 0) {

    }
    for (var i = response.length - 1; i >= 0; i--) {
        if ($.inArray(response[i]['id'], this.entry !== -1)) {
            var ChatItem = new Application.prototype.Chat.prototype.ChatItem(response[i]);
            this.preview.html(ChatItem.preview);
            final.append(ChatItem.print());
            this.entry.push(ChatItem.id);
            this.items.push(ChatItem);
        }
// 		console.log(this.items);
    }
    ;

    this.newest = Array.max(this.entry);
    this.oldest = Array.min(this.entry);
    return final.html();
};

Application.prototype.Chat.prototype.ChatItem = function(item) {
    this.time = {};
    this.time.time = item['time'];
    this.pic = item['pic'];
    this.preview = $('<span><img style="width:20px;" src="' + this.pic + '"/> ' + item['text'] + '</span>');
    this.id = item['id'];

    this.print = function() {
        var final = $('<div></div>');

        var string = $("<li class='single_chat'></li>")
        var chat_wrapper = $("<div class='chat_wrapper " + (USER_ID == item['user_id'] ? 'self-chat' : 'other-chat') + "'>");
        var profile_picture = $("<div data-user_id='" + item['user_id'] + "' class='profile_picture_medium online_status'>");
        profile_picture.css('background-image', 'url("' + item['pic'] + '")');
        var chat_bubble = $("<div class='chat-content'></div>");
        var chat_name = $("<div class='chatname'></div>").append("<span class='user_preview user_preview_name chatname' user_id='" + item['user_id'] + "'>" + item['name'] + "</span>");
        var chat_text = $("<div class='chattext'>").append(item['text'].replaceLinks().replaceEmoticons());
        chat_bubble.append(chat_name);
        chat_bubble.append(chat_text);
        chat_bubble.append(this.time.element = $("<span class='chat_time post_comment_time'>" + new Application.prototype.date(item['time']).timeAgo('short') + "</span>"));

        if (USER_ID == item['user_id']) {
            chat_wrapper.append(chat_bubble).append(profile_picture);
        } else {
            chat_wrapper.append(profile_picture).append(chat_bubble);
        }
        final.append(string.append(chat_wrapper));
        return final.html();
    }
};

Application.prototype.Chat.prototype.scroll2Bottom = function(force) {
    if (this.bottom === true || force === true) {
        this.chatoutput.scrollTop(this.chatoutput[0].scrollHeight);
    }
};

Application.prototype.Chat.prototype.submit = function(chat_text) {
    var self = this;
    if (chat_text != "") {
        $('.chatinputtext').val('');
        $('.chatinputtext').attr('placeholder', "Sending...");
        $('.chatinputtext').attr('readonly', 'readonly');
        $.post("Scripts/chat.class.php", {action: "addchat", aimed: self.id, chat_text: chat_text}, function(response) {
            response = $.parseJSON(response);
            self.list.append(self.styleResponse(response));
            $('.chatinputtext').removeAttr('readonly');
            $('.chatinputtext').attr('placeholder', "Press Enter to send...");
            self.bottom = true;
            self.scroll2Bottom(true);
        });
    }
};

Application.prototype.generic = {
};
Application.prototype.navigation = {
    initial: true
};
Application.prototype.Feed = function(entity_id, entity_type) {
    this.entity_id = entity_id;
    this.entity_type = entity_type;
    this.items = new Array();
    this.min = 0;
    this.max = 9999999999;
    this.onfetch = function() {
    };

    this.get = function() {
        this.active = true;
        var data = {
            action: "get_feed",
            min: this.min,
            max: this.max,
            entity_id: this.entity_id,
            entity_type: this.entity_type,
        };
        var self = this;
        $.post('Scripts/home.class.php', data, function(response) {
            response = $.parseJSON(response);
            for (var i = 0; i < response.length; i++) {
                self.items.push(new Feed.Item(response[i]));
            }
            self.onfetch();
        });
    };

    this.print = function() {
        var object = $('<div></div>');
        for (var i = 0; i < this.items.length; i++) {
            object.append(this.items[i].print());
        }
        return object;
    };

    this.Item = function(item) {
        this.item = item;
        this.print = function() {
            return this.homify();
        };
        this.homify = function() {
            this.item.stats.like.count = parseInt(this.item.stats.like.count);
            this.item.status_text = typeof this.item.status_text !== 'undefined' && !isEmpty(this.item.status_text) ? this.item.status_text : '';
            var string = $("<div data-this.item_id='" + this.item.id + "' class='post_height_restrictor contentblock' id='post_height_restrictor_" + this.item.id + "'></div>");
            if (this.item.view == 'home') {
                var user_link = $("<a class='user_name_post' href='user?id=" + this.item.user.id + "'></a>");
                user_link.append("<div class='profile_picture_medium' style='background-image:url(\"" + this.item.user.pic + "\");'></div>");
                var user_name = $("<a class='user_name_post user_preview user_preview_name' user_id='" + this.item.user.id + "' href='user?id=" + this.item.user.id + "'>" + this.item.user.name + "</a>");
                var top_content = $("<div class='top_content'>").append(user_link).append(user_name);
                var single_post = $('<div id="single_post_' + this.item.id + '" class="singlepostdiv"></div').append(top_content);
                string.append(single_post);
                if (!Application.prototype.user.isMobile) {
                    top_content.append(this.printStats(this.item));
                }
                if (this.item.type == "Text" || this.item.type == "File") {
                    if (!Application.prototype.user.isMobile) {
//                        single_post.append("<hr class='post_user_name_underline'>");
                    }
                }
                var content_wrapper = $("<div class='post_content_wrapper'></div>");
                content_wrapper.append("<p class='post_text'>" + this.item.status_text + '</p>');

                if (this.item.media.length > 0) {
                    var media_wrapper = $("<div class='post_feed_media_wrapper'></div>");
                    content_wrapper.append(media_wrapper);
                    for (var i in this.item.media) {
                        media_wrapper.append(new Application.prototype.file(this.item.media[i]).print(this.item.type));
                    }
                }
                single_post.append(content_wrapper);
                if (Application.prototype.user.isMobile) {
                    var stats = $("<div class='this.item_stats_mobile'></div>");
                    stats.append(this.printStats(this.item));
                    top_content.append(stats);
                }
                content_wrapper.append(new Application.prototype.Comment(this.item).print());
            }
            return string;
        };

        this.printStats = function(activity) {
            var string = $("<div class='activity_stats'></div>");
            string.append(print_likes(activity));
            var time = $("<span class='post_comment_time'></span>").text(Application.prototype.calendar.datetime.format(activity.time) + " |");
            string.append(time);
            var subarray = [{'element': time, 'time': activity.time}];
            Application.prototype.calendar.datetime.entry.push(subarray);
            if (activity.type == "File") {
                activity.media[0] = (activity.media[0] || new Object());
                activity.media[0].view = new Object();
                activity.media[0].view.count = (activity.media[0].view.count || 0);
                //        string += "<span class='post_comment_time'>| <span class='post_view_count'>" + activity.media[0].view.count + "</span> views</span>";
            }

//            if (activity.user.id == USER_ID) {
            var Dropdown = new Application.prototype.UI.Dropdown("activity_options");
            Dropdown.addOptions([{
                    class: "delete_activity",
                    text: "Delete"
                }, {
                    class: "edit_activity",
                    text: "Edit"
                }]);
            string.append(Dropdown.print());
//            }
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
        };

        this.refreshContent = function(id) {
            return;
            Application.prototype.feed.get(null, null, id, function(response) {
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
        };
    };
    this.createPost = function(text, files, activity_id) {
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
};

Application.prototype.Post = function(options, element) {
    this.element = element;
    var self = this;
    this.post_button = $('<button class="post-button pure-button-green small">Post</button>').on('click', function() {
        self.submit();
    });

    this.dropdown = new Application.prototype.UI.Dropdown();
    this.dropdown.addOptions([
        {
            value: 'a',
            text: "Public",
            class: "",
        },
        {
            value: '',
            text: "Private",
            class: "",
        }]);

    element
            .append($("<div class='home_feed_post_container_arrow_border'></div>")
                    .append("<div class='home_feed_post_container_arrow'></div>"))
            .append(this.post_wrapper = $("<div class='post_wrapper'></div>")
                    .append($("<div class='post_content_wrapper'></div>")
                            .append('<textarea tabindex="1" id="status_text" placeholder= "Update Status or Share Files..." class="status_text autoresize"></textarea>')
                            .append("<div class='post_media_wrapper'><div class='post_media_wrapper_background timestamp' style='text-align:left;'><span>Dropbox</span></div><img class='post_media_loader' src='Images/ajax-loader.gif'></img></div>"))
                    .append('<div class="file_container"></div>')
                    .append($("<div id='post_more_options' class='post_more_options'></div>")
                            .append(this.post_button)
                            .append(this.dropdown)));

    this.files = new Array();
    this.submit = function() {
        var text = $('#status_text').val();
        if (text != "" || this.files.length != 0) {
            $.post("Scripts/home.class.php", {action: "update_status", status_text: text, group_id: share_group_id, post_media_added_files: this.files}, function(data)
            {
                return;
                if (data == "") {
                    removeModal('', function() {
                        Application.prototype.feed.prototype.get(share_group_id, null, 0, activity_id, function(response) {
                            var string = $('');
                            for (var i in response) {
                                string.append(Application.prototype.feed.prototype.homify(response[i]));
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
    this.addFile = function(object, activity_id) {
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
            if (object.type == "Video") {
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
    this.removeFile = function(object) {
        var id;
        if (object.type == "Webpage") {
            addedURLs = removeFromArray(addedURLs, object.value);
            id = '#post_media_single_' + formatToID(object.value);
            for (var i = 0; i < post_media_added_files.length; i++) {
                if (object.value == post_media_added_files[i].path) {
                    post_media_added_files.splice(i, 1);
                } else {
                }
            }
        } else {
            var element = $('.post_media_wrapper [data-file_id="' + object.value + '"]');
            element.remove();
            for (var i = 0; i < post_media_added_files.length; i++) {
                if (object.value == post_media_added_files[i]) {
                    post_media_added_files.splice(i, 1);
                } else {
                }
            }
        }
        $(id).remove();

        if (post_media_added_files.length == 0) {
            $('#status_text').attr('placeholder', 'Update Status or Share Files...');
            $('.post_media_wrapper_background').show();
        }
        $('#status_text').focus();
        resizeScrollers();
    };
};
Application.prototype.Comment = function(item) {
    this.item = item;
    this.comments = new Array();
    this.print = function() {
        var object = $('<div class="comment_box"></div>');
        var comment = $('<div class="comment_box_comment"></div>');
        comment.append(this.showComments());
        object.append(comment);
        object.append(this.printInput());
        return object;
    };
    this.printInput = function() {
        var string = '';
        string += "<div class='comment_input' style='padding-left:2px;padding-top:2px;'>";
        string += "<table style='width:100%;'>";
        string += "<tr><td style='vertical-align:top;width:40px;'>";
        string += "<div class='profile_picture_medium' style='background-image:url(\"" + USER_PIC + "\");'>";
        string += "</div></td><td cellspacing='0' style='vertical-align:top;'>";
        string += '<textarea placeholder="Write a comment..." ';
        string += 'class="home_comment_input_text inputtext" id="comment_' + this.item.id;
        string += '"></textarea>';
        string += "</td></tr></table>";
        string += "</td></tr></table></div>";
        return string;
    };
    this.submit = function(comment_text, post_id, callback) {
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
    this.showComments = function() {
        this.comments[this.item.id] = new Array();
        var string = '';

        if (this.item.comment.format == 'top') {
            string += "<div class='activity_actions user_preview_name post_comment_user_name' style='font-weight:100;'>Show <span class='num_comments'>" + this.item.item.comment.hidden + "</span> more comments...</div>";
        }
        for (var i in this.item.comment.comment) {
            this.comments[this.item.id].push(this.item.comment.comment[i].id);
            string += this.show(this.item.comment.comment[i]);
        }

        this.comments[this.item.id]['max'] = Array.max(this.comments[this.item.id]);
        this.comments[this.item.id]['min'] = Array.min(this.comments[this.item.id]);
        return string;
    };
    this.show = function(comment) {
        var string = '';
        comment.like.like_text = (comment.like.has_liked ? "Unlike" : "Like");
        string += "<div class='single_comment_container' data-comment_id='" + comment.id + "'>";
        string += "<table style='font-size: 0.9em;'><tr><td style='vertical-align:top;' rowspan='2'>";
        string += "<div class='profile_picture_medium' style='background-image:url(\"" + comment.user.pic + "\");'></div></td><td style='vertical-align:top;'>";
        string += "<a class='userdatabase_connection' href='user?id=" + comment.user.id + "'>";
        string += "<span class='user_preview user_preview_name post_comment_user_name' user_id='" + comment.user.id + "'>" + comment.user.name + " </span></a>";
        string += "";
        string += "<span class='post_comment_text'>" + comment.text + "</span>"
        string += "</td></tr><tr><td colspan=2 style='vertical-align:bottom;' >"
        string += "<span class='post_comment_time'>" + new Application.prototype.date(comment.time).timeAgo() + " -</span>"
        string += "<span class='post_comment_time post_comment_liked_num'>"
        string += comment.like.count + "</span><i class='fa fa-heart heart_like_icon'></i>";
        string += "<span data-has_liked='" + comment.like.has_liked + "' "
        string += "class='user_preview_name post_comment_time post_comment_vote'>"
        string += comment.like.like_text + "</span>";
        string += "</tr></table>";
        if (comment.user.id == USER_ID) {
            string += "<i class='fa fa-times delete_cross delete_cross_top comment_delete'></i>";
        }
        string += "</div>";
        return string;
    };
    this.append = function(post_id, comment) {
        if ($('[data-comment_id="' + comment.id + '"]').length == 0) {
            var comment_container = $('[data-activity_id="' + post_id + '"]').find('.comment_box_comment').last();
            comment_container.append(this.show(comment));
        }
        // $("[data-activity_id='" + post_id + "'] [data-comment_id]").sort(function(left, right) {
        //     return parseInt($(right).data("comment_id")) - parseInt($(left).data("comment_id"));
        // }).each(function() {
        //     $("[data-activity_id='" + post_id + "']").find('.comment_box_comment').last().append($(this));
        // });
    };
};



Application.prototype.calendar = {
    event: {},
    datetime: {
        entry: new Array()
    }
};
Application.prototype.UI = {
    dropArrow: $("<i class='fa fa-angle-down'></i>"),
    progress: {
        instance: new Array()
    },
    Dropdown: function(controller) {
        var object = $("<div class='default_dropdown_actions' style='display:inline-block;' wrapper_id='" + controller + "'></div>");
        var wrapper = $("<div class='default_dropdown_wrapper'></div>");
        var list = $("<ul class='default_dropdown_menu'></ul>");

        this.print = function() {
            wrapper.append(list);
            object.append($("<i class='fa fa-angle-down'></i>"));
            object.append(wrapper);
            return object;
        };
        this.addOptions = function(options) {
            for (var i = 0; i < options.length; i++) {
                options[i]['value'] = options[i]['value'] || options[i]['text'];
                list.append("<li value='" + options[i]['value'] + "' class='" + options[i].class + "'>" + options[i].text + "</li>");
            }
        };
    },
};
Application.prototype.notification = {
};
Application.prototype.search = {
};

function isEmpty(input) {
    if (input == "null" || input == "" || input == null) {
        return true;
    } else {
        return false;
    }
}

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

Application.prototype.search.get = function(text, mode, element, callback) {
    if (text == "") {
        $(element).hide();
    } else {
//        var loader = $("<img src='Images/ajax-loader.gif'></img>");
//        $(element).prepend(loader);
        $(element).show();
    }
    $.post("Scripts/searchbar.php", {search: mode, input_text: text}, function(response) {
        response = Application.prototype.search.style($.parseJSON(response));
        $(element).find('.search_slider').remove();
        var slider = $("<div class='search_slider'></div>");
        slider.append(response);
        $(element).append(slider);
//        loader.remove();
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
};

Application.prototype.search.style = function(items) {
    var wrapper = $('<div></div>');
    for (var i = 0; i < items.length; i++) {
        var item = Application.prototype.search.styleSingle(items[i]);
        if (i == 0) {
            item.addClass('match');
        }
        wrapper.append(item);
    }
    return wrapper;
};

Application.prototype.search.styleSingle = function(item) {
    var div = $("<div></div>").addClass('search_option').data('entity', item);
    var string = "<table style='width:100%;'><tr><td rowspan='2' style='width:35px;'>"
            + "<img height='40px' width='40px' src='" + item['img'] + "'/>"
            + "</td><td>"
            + "<p class='search_option_name ellipsis_overflow'>" + item['name'] + "</p></td>";
    if (item['type'] == "user") {
        string += "<td><div class='connect_button'></div></td>";
    }
    string += "</tr>"
            + "<tr><td><span class='search_option_info'>" + item['info'] + "</span></td></tr></table>";
    div.append(string);
    return div;
};

Application.prototype.navigation.relocate = function(event, element) {
    this.initial = false;
    event.preventDefault();
    $('.container').html("<div class='loader_outside'></div><div class='loader_inside'></div>");
    $.get($(element).attr('href'), {ajax: 'ajax'}, function(response) {
        var container = $(response);
//        container.hide();
        $('.container').replaceWith(container);
//        container.fadeIn('fast');
        window.history.pushState({}, 'WhatTheHellDoesThisDo?!', '/' + $(element).attr('href'));
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
                '<img src="Images/' + emoticons[match] + '"/>' : //removed onload
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

Application.prototype.calendar.datetime.format = function(time) {
    time *= 1000;
    return 'time';
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
        ''
        file_string += '<tr><td>';
        file_string += "<div class='profile_picture_icon' style='vertical-align:top;display:inline-block;background-image: url(\""
                + event.file[file].type_preview + "\");'></div>";
        file_string += "</td><td><a href='download.php?id=" + event.file[file].id
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
        Application.prototype.navigation.relocate(e, $(this));
    });

    window.onpopstate = function(event) {
        if (Application.prototype.navigation.initial === false) {
            Application.prototype.navigation.relocate(event, $("<a></a>").attr('href', window.location.pathname + window.location.search));
        }
    };

    Application.prototype.UI.adjustSwitches();

    $(window).on('resize', function() {
//        Application.prototype.UI.resizeContainer();
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
     * 2.1.1.1 Startup - Generic - Dropdown              *
     ****************************************************/
    $(document).on('click', ".default_dropdown_selector .default_dropdown_actions", function(event) {
        event.stopPropagation();
        $('.default_dropdown_wrapper').hide();
        //$(this) // PARENT;
        var wrapper = '#' + $(this).parents('wrapper_id');
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

//    $(document).on('click', ".default_dropdown_actions", function(event) {
//        event.stopPropagation();
//        $('.default_dropdown_wrapper').hide();
//        var wrapper = '#' + $(this).attr('wrapper_id');
//        $(wrapper).toggle();
//        $(wrapper).mCustomScrollbar(SCROLL_OPTIONS);
//    });

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
        var user_id = $(this).data('user_id');
        Application.prototype.user.preview.show($(this), user_id);
    });

    $(document).on('mouseleave', ".user_preview", function(event) {
        Application.prototype.user.preview.remove('force', event);
    });

    /****************************************************
     * 2.1.3 Startup - Search                            *
     ****************************************************/

    $(document).on('keyup', '.search', function(e) {
//         e.stopPropagation();
        Application.prototype.search.get($(this).val(), $(this).attr('mode'), $(this).parents('div').children('.search_results'), function() {
        });
    });

    $(document).on('mouseover', '.search_option', function(event) {
        $(this).siblings().removeClass('match');
        $(this).addClass('match');
    });

    $(document).on('click', '#share_event_results .search_option', function(event) {
        var entity = $(this).data('entity');
        event_receivers = addreceiver(entity.type, entity.id, entity.name, event_receivers, "event");
//         event.stopPropagation();
    });

    $(document).on('click', '.message_search_results .search_option', function(event) {
        var entity = $(this).data('entity');
        message_receivers = addreceiver(entity.type, entity.id, entity.name, message_receivers, "message");
//         event.stopPropagation();
    });

    $(document).on('click', '.group_search_results .search_option', function(event) {
        group_receivers = addreceiver(entity.type, entity.id, entity.name, group_receivers, "group");
//         event.stopPropagation();
    });

    $(document).on('click', '.search_input', function() {
        var box = $(this).next('.search_results');
        if (box.find('.search_result, .match').length == 0) {

        }
        else {
            box.slideDown();
        }
    });

    $(document).on('click', '.search_input', function(e) {
//         e.stopPropagation();
    });

    // $(document).on('click', function() {
//         $('.search_results').slideUp();
//     });

    $(document).on('click', '.global_header_container .search_option', function(e) {
        var entity = $(this).data('entity');
        var link;
        if (entity.entity_type == 'user') {
            link = $('<a></a>').attr('href', 'user?id=' + entity.id);
        } else if (entity.entity_type == 'group') {
            link = $('<a></a>').attr('href', 'group?id=' + entity.id);
        } else {
            link = $('<a></a>').attr('href', 'files?f=' + entity.id);
        }
        Application.prototype.navigation.relocate(e, link);
    });

    $(document).on('click', '.search_option', function() { // Hide the search results when the user selects an option.
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

    $('.createPost').each(function() {
        new Application.prototype.Post({}, $(this));
    });

    $(window).on('scroll', function() {
        if ($(document).height() == $(window).scrollTop() + Application.prototype.UI.getViewPortHeight()) {
//             Application.prototype.feed.get();
        }
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

    $(document).on('click', '.comment_delete', function() {
        var comment_id = $(this).parents('[data-comment_id]').data('comment_id');
        $(this).parents('.single_comment_container').next('hr.post_comment_seperator').remove();
        $(this).parents('.single_comment_container').hide();
        $.post('Scripts/home.class.php', {action: "deleteComment", comment_id: comment_id}, function(response) {
        });
    });

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
        if ($(this).is('button')) {
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
        var Upload = new Application.prototype.upload(files);
        Upload.end = function() {
            refreshFileContainer(encrypted_folder);
        };
        Upload.push();
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
    $(document).on('ready', function() {
        $('.autoresize').trigger('keydown');
    });

    $(document).on('keydown', '.autoresize, .inputtext', function(e) {
        $(this).css('height', '0px');
        $(this).css('height', $(this)[0].scrollHeight + "px");
    });

    $(window).resize(function() {
        for (var chat in Application.prototype.Chat.prototype.get()) {
//            chat.detectChange();
        }
//         if (Application.prototype.file.theater.active) {
//             Application.prototype.file.theater.adjust();
//         }
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

    });

    $(document).on('click', '.box_container .navigate_left', function() {

    });

    /****************************************************
     * 2.1.3 Startup - Chat                              *
     ****************************************************/
    var cookie = 0; //getCookie('chat_feed');
    if (cookie == 0) {
        $('#chat').hide();
    }

    $(document).on('click', '.chat-head, .chat_feed_selector', function() {
        $(this).parents('.chatcomplete').toggleClass('active');
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

    $(document).on('click', "img.message", function(event) {
        $('img.message').removeClass('message_active');
        $(this).addClass('message_active');
    });
    $(document).on('click', "#home_icon", function(event) {
        event.stopPropagation();
        window.location.replace("home");
        $("#notificationdiv").hide();
        $("#networkdiv").hide();
        $("#geardiv").hide();
    });
    $(document).on('click', "#personal", function(event) {
        event.stopPropagation();
        $(".personal").show();
        $(".general").hide();
        $("#messagediv").hide();
        $("#notificationdiv").hide();
        $("#networkdiv").hide();
        $("#geardiv").hide();
    });
    $(document).on('click', "#message_click", function(event) {
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
    $(document).on('click', "#notification_click", function(event) {
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
    $(document).on('click', "#network_click", function(event) {
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
    $(document).on('click', "#gear_click", function(event) {
        event.stopPropagation();
        $("#notificationdiv").hide();
        $(".personal").hide();
        $(".general").hide();
        $("#messagediv").hide();
        $("#geardiv").show();
    });
    $(document).on('click', "html", function() {
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
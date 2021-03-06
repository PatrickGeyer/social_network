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

};
Application.prototype.Environment = Environment;
Application.prototype.Language = Language;
delete Environment;
delete Language;
Application.prototype.default = {
    pic: {
        icon: "/Images/male-default-icon.jpg",
        thumb: "/Images/male-default-chat.jpg",
        large: "/Images/male-default-icon.jpg"
    },
    file: {
        type: ['aac', 'ai', 'aiff', 'asp', 'avi', 'bmp', 'c', 'cpp', 'css', 'dat', 'dmg', 'doc', 'docx', 'dot', 'dotx', 'dwg', 'dxf', 'eps', 'exe',
        'flv', 'gif', 'h', 'html', 'ics', 'iso', 'java', 'jpg', 'key', 'm4v', 'mid', 'mov', 'mp3', 'mp4', 'mpg', 'odp', 'ods', 'odt', 'otp', 'ots', 
        'ott', 'pdf', 'php', 'png', 'pps', 'ppt', 'pptx', 'psd', 'py', 'qt', 'rar', 'rb', 'rtf', 'sql', 'tga', 'tgz', 'tiff', 'txt', 'wav', 'xls',
        'xlsx', 'xml', 'yml', 'zip']
    },
    emptyFeed: $('<div class="contentblock post_height_restrictor">This feed contains no posts.</div>')
};
Application.prototype.App = function() {
    };
    Application.prototype.App.prototype.Application = function(options) {
    var self = this;
    this.attr = options;
    this.attr.info.path = "/Leo/Freezetag.html";
    this.temp = {};
    this.create = function() {
        $.post('/Scripts/app.class.php', {action: 'create', name: this.attr.name}, function() { //this.name!
            alert('done');
        });
    };
    this.printTag = function(options) {
        var cont = $("<div class='contentblock'></div>")
        .append($("<a href='/app/" + this.attr.info.id + "/'></a>")
                .append('<img class="profile_picture_medium" src="' + this.attr.pic.thumb + '"/>')
                .append(""  + this.attr.info.name +  ""));
        options.container.append(cont);
    };
    this.print = function() {
        this.container = $('.app[data-game_id="' + this.attr.info.id + '"]');
        this.container.append(this.controls = $('<div class="contentblock"></div>'));
        this.frame = $('<iframe allowFullScreen="true" seamless sandbox="allow-same-origin allow-scripts" src="' + this.attr.info.path + '"></iframe>');
        this.container.prepend($("<div class='contentblock'></div>").append(this.frame));
        this.enlarge = this.container[0];
        this.frame[0].contentWindow.App = self;
        this.frame.height(this.frame.width() * 0.7142857 + 35);
//        this.frame[0].height = "0px";
//        this.frame[0].height = this.frame[0].contentWindow.document.body.scrollHeight + "px";

        this.controls.append($("<i class='fa fa-expand'></i>").on('click', function() {
            Application.prototype.UI.launchFullscreen(self.enlarge);
        }));
    };
    this.game = {
        getHighscore: function(callback) {
            $.get('/Scripts/app.class.php', {action: "getHighscore", game_id: self.attr.info.id}, function(response) {
                response = $.parseJSON(response);
                callback(response);
            });
        },
        getHighscores: function(min, max, callback) {
            min = min || 0;
            max = max || 10;
            $.get('/Scripts/app.class.php', {action: "getHighscores", game_id: self.attr.info.id, min: min, max: max}, function(response) {
                response = $.parseJSON(response);
                callback(response);
            });
        },
        setHighscore: function(score, callback) {
            callback = callback || function() {
            };
            $.post('/Scripts/app.class.php', {action: "setHighscore", game_id: self.attr.info.id, score: score}, function(response) {
            	console.log(response);
                response = $.parseJSON(response);
                callback(response);
            });
        },
        block: function(options) {
            self.container.append($("<div class='contentblock'></div>").append("<h3>" + options.header + "</h3>").append(options.text));
        },
    };
};

Application.prototype.App.prototype.AppList = function(options) {
    var self = this;
    this.attr = options || {};
    this.attr.header = this.attr.header || "Popular Apps";
    this.attr.box = $("<div class='contentblock'></div>").append("<h3>" + this.attr.header + "</h3>");
    this.get = function() {
        $.get('/Scripts/app.class.php', {action: "getPopularApps"}, function(response) {
            response = $.parseJSON(response);
            for(var i = 0; i < response.length; i++) {
                var app = new Application.prototype.App.prototype.Application(response[i]);
                app.printTag({container: self.attr.box});
            }
        })
    };
    this.attr.container.append(this.attr.box)
    this.get();
};

Date.mysql = function(string) {
    if (typeof string === 'string') {
        var t = string.split(/[- :]/);
        //when t[3], t[4] and t[5] are missing they defaults to zero
        return new Date(t[0], t[1] - 1, t[2], t[3] || 0, t[4] || 0, t[5] || 0);
    }
};
Date.prototype.getMonthName = function(lang) {
    lang = lang && (lang in Date.locale) ? lang : 'en';
    return Date.locale[lang].month_names[this.getMonth()];
};

Date.prototype.getMonthNameShort = function(lang) {
    lang = lang && (lang in Date.locale) ? lang : 'en';
    return Date.locale[lang].month_names_short[this.getMonth()];
};

Date.prototype.getDayName = function(lang) {
    lang = lang && (lang in Date.locale) ? lang : 'en';
    return Date.locale[lang].week_names[this.getDay()];
};

Date.locale = {
    en: {
        month_names: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
        month_names_short: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        week_names: ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday']
    }
};

Date.prototype.timeAgo = function(length) {
    var now = new Date();
    if (now.getFullYear() - this.getFullYear() >= 1) {
        return this.formatYear();
    } else if (now.getMonth() - this.getMonth() >= 1 || now.getDay() - this.getDay() > 7) {
        return this.formatMonth();
    } else {
        return this.formatWeek();
    }
};

Date.prototype.formatYear = function() {
    return this.getDay() + " " + this.getMonthName() + ", " + this.getFullYear();
};

Date.prototype.formatYear = function() {
    return this.getDay() + " " + this.getMonthName() + ", " + this.getFullYear();
};

Date.prototype.formatMonth = function() {
    return this.getDay() + " " + this.getMonthName() + " at " + this.formatTime();
};

Date.prototype.formatWeek = function() {
    var now = new Date();
    return (now.getDate() - this.getDate() < 2 ? (now.getDate() - this.getDate() < 1 ? "Today, " : "Yesterday, ") : (this.getDayName() + ", "))
            + this.formatTime();
};
Date.prototype.formatTime = function() {
    return (this.getHours() > 9 ? this.getHours() : "0" + this.getHours()) + ":" + (this.getMinutes() > 9 ? this.getMinutes() : "0" + this.getMinutes());
};

//    this.tokens = [
//        {time: 1, unit: 'second'},
//        {time: 60, unit: 'minute'},
//        {time: 3600, unit: 'hour'},
//        {time: 86400, unit: 'day'},
//        {time: 604000, unit: 'week'},
//        {time: 2592000, unit: 'month'},
//        {time: 31536000, unit: 'year'},
//    ];
//    this.short = {
//        'second': 'sec',
//        'minute': 'min',
//        'hour': 'hr',
//        'day': 'd',
//        'week': 'wk',
//        'month': 'mo',
//        'year': 'yr',
//    };


/****************************************************
 * 0. Upload                                        *
 ****************************************************/

Application.prototype.Upload = function(files) {
    var self = this;
    this.files = files || new Array();
    this.sessions.push(this);
    this.progressElement = new Array();
    this.onstart = function() {
    };

    this.onprogress = function() {
    };

    this.onend = function() {
    };

    this.addFiles = function(files) {
        if (files.length) {
            for (var i = 0; i < files.length; i++) {
                this.files.push(files[i]);
            }
        } else {
            this.files.push(files);
        }
    };

    this.push = function(type) {
        var file_upload_container = $('<div></div>');
        this.container.append(file_upload_container);

        var session = this.session++;
        this.start(this.onstart);
        if (type !== 'folder') {
            for (var i = 0; i < this.files.length; i++) {
                this.files[i].parent_folder_id = this.files[i].parent_folder_id || 0;
                this.count++;
                (function(count, session, Upload) {
                    var file = Upload.files[count];
                    var file_container = $("<div class='upload_preview'>");
                    file_container.append(file.name);
                    file_container.append("<br />");
                    file_upload_container.append(file_container);
                    Upload.progressElement[count] = new Application.prototype.UI.progress(count);
                    Upload.progressElement[count].element = file_container;
                    file_container.append(Upload.progressElement[count].print());
                    var formdata = new FormData();
                    formdata.append("file", file);
                    formdata.append("action", 'upload');
                    formdata.append("parent_folder", file.parent_folder_id);

                    var xhr = new XMLHttpRequest();
                    xhr.upload.onprogress = function(event) {
                        Upload.progress(event, count, Upload.onprogress);
                    };
                    xhr.onreadystatechange = function() {
                        if (xhr.readyState == 4) {
                            Upload.complete(xhr, count, Upload.onend);
                        }
                    };
                    xhr.addEventListener("error", Application.prototype.Upload.error, false);
                    xhr.addEventListener("abort", Application.prototype.Upload.abort, false);
                    xhr.open("post", "/Scripts/files.class.php");
                    xhr.send(formdata);
                })(i, session, this);
            }
        } else {
            items = {};
            for (var i = 0; i < this.files.length; i++) {
                items[i] = this.files[i].webkitRelativePath;
            }
            $.post("/Scripts/files.class.php", {action: "createFolders", files: items}, function(response) {
                response = $.parseJSON(response);
                self.pushFolder(response);
                self.push();
            });
        }
    };
    this.pushFolder = function(tree) {
        for (var i = 0; i < tree.length; i++) {
            if (tree[i].children) {
                this.pushFolder(tree[i].children);
            } else {
                for (var n = 0; n < this.files.length; n++) {
                    if (this.files[n].webkitRelativePath === tree[i].href) {
                        if (this.files[n].name !== ".") {
                            this.files[n].parent_folder_id = tree[i].parent_folder_id;
                        } else {
                            this.files.splice(n, 1);
                        }
                    }
                }

            }
        }
    };

    this.start = function(callback) {
        callback();
    };

    this.progress = function(event, id, callback) {
        var percent = (event.loaded / event.total) * 100;
        percent = Math.round(percent);
        this.progressElement[id].update(percent);
//        callback(percent);
    };

    this.complete = function(event, id, callback) {
        this.progressElement[id].element.slideUp();
        this.progressElement[id].remove();
        this.count--;
        if (this.count === 0) {
            this.reset();
        }
        var file = $.parseJSON(event.responseText);
        Application.prototype.FileList.prototype.addFile(file);
        Application.prototype.Folder.prototype.addFile(file);
        callback(file);
    };

    this.error = function(event) {
        if (this.session > 0) {
            this.session--;
        }
        alert('upload failed');
    };

    this.abort = function(event) {
        if (this.session > 0) {
            this.session--;
        }
    };
    this.reset = function() {
        this.files = new Array();
    };
};

Application.prototype.Upload.prototype.sessions = new Array();
Application.prototype.Upload.prototype.count = new Array();

/****************************************************
 * 1.3 Files                                         *
 ****************************************************/
Application.prototype.File = function(file) {
    var self = this;
    this.getById = function(file_id) {
        return this.items[file_id] || null;
    };

    if (file !== false) {
        if (this.items[file.id]) {
            return this.items[file.id];
        } else {
            this.items[file.id] = this;
        }
        this.init = false;
        this.playing = false;
        this.file = file;
        
        this.file.name = typeof this.file.name !== 'undefined' && !isEmpty(this.file.name) ? this.file.name : 'Untitled';
        this.file.description = typeof this.file.description !== 'undefined' && !isEmpty(this.file.description) ? this.file.description : '';
        this.file.time = typeof this.file.time !== 'undefined' && !isEmpty(this.file.time) ? this.file.time : 'No Date';

        this.items[this.file.id] = this;
        
        this.file.path = '/Files/' + this.file.user_id + "/" + this.file.url_name + "." + this.file.ext;
        this.file.icon_path = '/Files/' + this.file.user_id + "/icon_" + this.file.url_name + "." + this.file.ext;
        this.file.thumb_path = '/Files/' + this.file.user_id + "/thumb_" + this.file.url_name + "." + this.file.ext;
        this.file.thumbnail = '/Files/' + this.file.user_id + "/thumbnail_" + this.file.url_name + ".jpg";
        this.file.flv_path = '/Files/' + this.file.user_id + "/" + this.file.url_name + ".flv";
        this.file.mp4_path = '/Files/' + this.file.user_id + "/" + this.file.url_name + ".mp4";
        this.file.ogg_path = '/Files/' + this.file.user_id + "/" + this.file.url_name + ".ogg";
//        
//        Configure file icon
        var ext = false;
        for(var i = 0; i < Application.prototype.default.file.type.length; i++) {
            if(this.file.ext === Application.prototype.default.file.type[i]) {
                ext = true;
            }
        }
        if (ext && this.file.type !== "Folder") {
            this.file.type_preview = "/images/icons/file_type/png/" + this.file.ext + '.png';
        } else if (this.file.type !== 'Folder') {
            this.file.type_preview = "/images/icons/file_type/png/blank.png";
        } else {
            this.file.type_preview = FOLDER_THUMB;
        }

        this.file.item = new Application.prototype.Comment(this.file.activity);
        /****************************************************
         * 1.3.1 Files - Print                               *
         ****************************************************/

        this.view = function() {
            $.post("/Scripts/files.class.php", {file_id: this.file.id, action: "view"}, function() {
            });
        };

        this.list = function(element, type, callback) {
            callback = typeof callback !== 'undefined' ? callback : function() {
            };
            $.post('/Scripts/home.class.php', {type: type, action: "file_list"}, function(response) {
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
            $.post('/Scripts/files.class.php', {action: "rename", file_id: this.file.id, name: text}, function() {
            });
        };
        this.printTag = function() {
            var object = $("<div></div>");
            object.append(this.file.name);
            return object;
        };
        this.print = function(activity_type) {
            var string = $('<div class="post_feed_item contentblock"></div>');
            if (this.file.type == "Audio") {
                string.addClass("post_media_double");
                string.append(this.printDoc());
            } else if (this.file.type == "Image") {
                string.addClass("post_media_photo");
                string.append(this.printDoc());
            } else if (this.file.type == "Video") {
                string.append(this.printDoc());
                string.addClass("post_media_video");
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
            } else {
                string.addClass("post_media_full");
                string.append(this.printDoc());
            }
            var actions = $("<div class='top_right_actions'></div>");
            if (this.file.user_id == MyUser.attr.id && activity_type != 'File') {
                actions.append("<i class='fa fa-times delete_cross delete_cross_top remove_event_post'></i>");
            }
            string.append(actions);

            if (activity_type == "Text" && typeof this.file.activity != 'undefined') {
                string.append(this.file.item.print());
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
        var link = "/files/" + this.file.id + "/";
        this.file_activity_section = $("<div class='file_activity_section'></div>");
        this.preview = $("<div class='post_media_preview'></div>");
        this.actions = $("<div style='margin-top:5px;'></div>");
        this.stats = $("<div style='margin-top:5px;'></div>");
        
        if (this.file.type == "Folder") {
            
//            link = "files?pd=" + this.file.enc_parent_folder_id;
            
        } else if (this.file.type == "Image") {
            
            this.preview.addClass("post_media_photo");
            this.preview.css("background-image", "url(\"" + this.file.thumb_path + "\")");
            
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
            
            this.preview.css('background-image', 'url("' + this.file.type_preview + '")');

        }

        this.file_activity_section
                .append(this.user_link = $("<a class='user_preview_name' target='_blank' href='" + link + "'></a>")
                        .append($("<p class='ellipsis_overflow' style='word-break:break-word; '>" + this.file.name + "</p>")))
                .append("<span style='font-size:12px;' class='post_comment_time'>" + this.file.description + "</span>")
                .append(this.stats)
                .append(this.actions);
        this.stats.append("<i class='heart_like_icon fa fa-heart'></i><span class='post_comment_time post_like_count'>" + this.file.like.count + "</span>");

        if (typeof this.file.activity.item != 'undefined') {
            this.stats.append("<i class='fa fa-item heart_like_icon'></i><span class='post_comment_time post_comment_count'>"
                    + (parseInt(this.file.activity.item.item.length) + parseInt(this.file.activity.item.hidden)) + "</span>");
        }
        this.stats.append("<i class='fa fa-eye heart_like_icon'></i><span class='post_comment_time'>" + this.file.view.count + "</span><br />");

        this.actions.append("<a class='no-ajax' href='download.php?id=" + this.file.id + "'>" + "Download<span></span></a>");
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

        actions.append("<a href='/download.php?id=" + this.file.id + "' download><div class='files_actions_item files_actions_download'></div></a>");
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
        var string = $('<div data-file_id="' + this.file.id + '"></div>');
        if (part == 'all') {
            string.append($('<div class="audio_container"></div>').append(this.audioButton()).append(this.audioInfo()));
        } else if (part == "button") {
            string.append(this.audioButton());
        } else if (part == "info") {
            string.append(this.audioInfo());
        } else if (part == 'timeline') {
            string.append(this.audioTimeline());
        }
        string.append(this.audio = $('<audio style="display:none;"><source src="' + this.file.path + '"></source><source src="'
                + this.file.thumb_path + '"></source></audio>'));
        return string;
    };
    var self = this;

    this.audioButtons = new Array();
    this.timelines = new Array();
    this.times = new Array();
    this.audioInfos = new Array();
    this.titles = new Array();
    this.progressContainer = new Array();
    this.audioProgresses = new Array();
    this.audioBuffered = new Array();


    this.audio_button = $('<div class="audio_button"></div>').on('click', function() {
        self.audioPlay();
    });
    self.progress_item = $('<div class="audio_progress"></div>');
    self.buffered = $('<div class="audio_buffered"></div>');

    this.audio_button.append('<div class="audio_button_inside"></div>');
    this.audioButton = function() {
        var a = self.audio_button.clone(true);
        self.audioButtons.push(a);
        return a;
    };

    this.audioInfo = function(file) {
        var a = $('<div class="audio_info"></div>')
                .append(this.audioTitle())
                .append(this.audioTimeline())
                .append(this.audioTime());
        self.audioInfos.push(a);
        return a;
    };

    this.audioTimeline = function() {
        var a = $('<div class="audio_progress_container"></div>')
                .append(self.audioProgress())
                .append(self.audioBuffer())
                .append('<div class="audio_line"></div>').on('click', function(e) {
            var x = $(this).offset().left;
            var width_click = e.pageX - x;
            var width = $(this).width();
            var percent_width = (width_click / width) * 100;
            var secs = self.audio.get(0).duration;
            var new_secs = secs * (percent_width / 100);
            self.audio.get(0).currentTime = new_secs;
            self.updateTimeline();
        });
        self.timelines.push(a);
        return a;
    };
    this.audioProgress = function() {
        var a = self.progress_item.clone(true);
        self.audioProgresses.push(a);
        return a;
    };
    this.audioBuffer = function() {
        var a = self.buffered.clone(true);
        self.audioBuffered.push(a);
        return a;
    };

    this.audioTitle = function() {
        var a = self.audio_title.clone(true);
        self.titles.push(a);
        return a;
    };
    this.audioTime = function() {
        var a = self.audio_time.clone(true);
        self.times.push(a);
        return a;
    };
    self.audio_time = $('<div class="audio_time">0:00</div></div>');
    self.audio_title = $('<div class="ellipsis_overflow audio_title">' + this.file.name + '</div>');
    self.loader = new Application.prototype.UI.Loader({class: ['audio_loader']});

    this.updateTimeline = function() {
        var track_length = self.audio.get(0).duration;
        var secs = self.audio.get(0).currentTime;
        var progress = (secs / track_length) * 100;
        for (var i = 0; i < self.audioProgresses.length; i++) {
            self.audioProgresses[i].css('width', progress + "%");
        }
//                 self.progress.css('width', progress + "%");
        var minutes = Math.floor(track_length / 60);
        var seconds = Math.floor(track_length - minutes * 60);
        var done_secs = self.audio.get(0).currentTime;
        var done_minutes = Math.floor(done_secs / 60);
        var done_remaining_secons = Math.floor(done_secs - done_minutes * 60);
        for (var i = 0; i < self.times.length; i++) {
            self.times[i].html(done_minutes + ":" + pad(done_remaining_secons) + " - " + minutes + ":" + seconds);
        }
//                 self.audio_time.html(done_minutes + ":" + pad(done_remaining_secons) + " - " + minutes + ":" + seconds);
        self.loader.fadeOut();

        var track_length = self.audio.get(0).duration;
        var secs = 0;
        if (self.audio.get(0).buffered.length > 0) {
            secs = self.audio.get(0).buffered.end(0);
        }
        var progress = 0;
        if (secs > 0 && track_length > 0) {
            progress = (secs / track_length) * 100;
        }
        for (var i = 0; i < self.audioBuffered.length; i++) {
            self.audioBuffered[i].css('width', progress + "%");
        }

        if (self.audio[0].paused) {
            self.loader.show();
        }
    };

    this.audioPlay = function() {
        if (!this.playing) {
            this.startAudioInfo();
            for (var i = 0; i < this.audioButtons.length; i++) {
                this.audioButtons[i].addClass('audio_playing');
            }
            this.playing = true;
        } else {
            this.audio[0].pause();
            for (var i = 0; i < this.audioButtons.length; i++) {
                this.audioButtons[i].removeClass('audio_playing');
            }
            this.playing = false;
        }
    };

    this.startAudioInfo = function(start, progress, end) {
        if (this.init === true) {
            this.audio.get(0).play();
            return;
        }
        this.init = true;
        this.view();

        var headerControl = $("<div></div>");
        headerControl.append(this.audioPlayer('all'));

        $('.global_media_container').html(headerControl);

        this.loader.fadeIn();
        this.audio.get(0).play();
        this.audio.volume = 1;
        var self = this;
        for (var i = 0; i < self.audioButtons.length; i++) {
            self.audioButtons[i].append(self.loader);
        }

        this.audio.bind('loadedmetadata', function() {
            self.updateTimeline();
            self.audio.bind('progress', function() {
                self.updateTimeline();
            });

            self.audio.bind('timeupdate', function() {
                self.updateTimeline();
            });

            self.audio.bind('canplaythrough', function() {
                self.buffered.css('background-color', 'grey');
            });

            self.audio.bind('ended', function() {
                self.audio.get(0).currentTime = 0;
                self.audio_button.removeClass('audio_playing');
            });
            self.audio.bind('loadeddata', function() {
                // console.log('new buffer');
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
Application.prototype.File.prototype.items = {};

Application.prototype.FileList = function(type, f_id, user_id, prop) {
    var self = this;
    this.prop = prop || {};
    this.prop.class = this.prop.class || ['file_list_done'];
    this.user_id = user_id || MyUser.attr.id;
    this.items.push(this);
    this.type = type || 'all';
    this.f_id = f_id || 0;
    this.paths = new Array({name: 'Files', id: 0});
    this.element = $("<div></div>");
    for (var i = 0; i < this.prop.class.length; i++) {
        this.element.addClass(this.prop.class[i]);
    }
    this.onclick = function(file) {
        if (file.file.type === "Folder") {
            self.relocate(file.file.folder_id, file.file.name);
        }
    };
    this.printItem = function(file) {
        return file.printTag('none');
    };

    this.relocate = function(id, name) {
        id = parseInt(id);
        for (var i = self.paths.length - 1; i >= 0; i--) {
            if (parseInt(self.paths[i].id) >= id) {
                self.paths.splice(i, 1);
            }
        }
        self.f_id = id;
        self.paths.push({name: name, id: id});
        self.get();
    };

    if (this.prop.container) {
        this.prop.container.replaceWith(this.element.addClass('box_container contentblock'));
    }
    this.print = function() {
        return this.container;
    };
    this.get();
    return this;
};

Application.prototype.FileList.prototype.get = function() {
    var self = this;
    self.element.html('');
    var path = $('<h3></h3>');
    for (var i = 0; i < self.paths.length; i++) {
        (function(i) {
            path.append($("<span>" + self.paths[i].name + " / </span>").on('click', function() {
                self.relocate(self.paths[i].id, self.paths[i].name);
            }));
        }(i));
    }
    ;
    self.element.append(new Application.prototype.UI.Loader());
    $.get('/Scripts/files.class.php', {action: 'list', type: this.type, pf: this.f_id, receiver_id: this.user_id}, function(response) {
        self.element.html('');
        self.element.append(path);
        response = $.parseJSON(response);
        for (var i = 0; i < response.length; i++) {
            (function(i) {
                var file = new Application.prototype.File(response[i]);
                self.element.append(self.printItem(file).on('click', function() {
                    self.onclick(file);
                }));
            })(i);
        }
        if (response.length === 0) {
            self.element.append(Application.prototype.default.emptyFeed.clone().text('There are no available files in this Folder.'));
        }
//        if (self.pf != 0) {
//            self.container.append($('<button class="pure-button-blue">Back</button>').on('click', function() {
//                self.paths.pop();
//                self.f_id = self.paths[self.paths.length -1].id;
//                self.get();
//            }));
//        }
    });
};

Application.prototype.FileList.prototype.items = new Array();

Application.prototype.FileList.prototype.addFile = function(file) {
    for (var i = 0; i < this.items.length; i++) {
        var self = this.items[i];
        var file = new Application.prototype.File(file);
        self.element.append(file.printTag('none').on('click', function() {
            self.onclick(file);
        }));
    }
};

Application.prototype.Folder = function(list, prop) {
    this.prop = prop;
    this.items.push(this);
    this.files = list;
    this.print();
};
Application.prototype.Folder.prototype.items = new Array();

Application.prototype.Folder.prototype.print = function() {
    for (var file in this.files) {
        this.prop.container.append(new Application.prototype.File(this.files[file]).print_row());
    }
};
Application.prototype.Folder.prototype.addFile = function(file) {
    for (var i = 0; i < this.items.length; i++) {
        var self = this.items[i];
        var file = new Application.prototype.File(file);
        self.prop.container.append(file.print_row());
    }
};

Application.prototype.theater = function() {
    this.active = false;
    this.removeTime = 0;
    this.previousUrl = '';

    this.initiate = function() {
        var self = this;
        Application.prototype.File.view(file_id);
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

        $.post('/Scripts/files.class.php', {action: "preview", file_id: file_id, activity_id: activity_id}, function(response) {
            response = $.parseJSON(response);
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
                self.theater_picture_container.append(Application.prototype.File.videoFrame(response.media[0].uid));
                image = Application.prototype.File.videoPlayer(response.media[0], function(video) {
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
Application.prototype.Chat = function(prop) {
    this.prop = prop;
    this.prop.element = this.prop.element || $('.right_bar_container');
    this.id = this.prop.id;
    this.entry = new Array();
    this.oldest = 0;
    this.newest = 998999999;
    this.getting_previous = false;
    this.last = false;
    this.name = this.prop.name;
    this.items = new Array();
    this.bottom = true;
    this.beforesubmit = function(callback) {
        callback();
    };
    this.oninit = this.prop.oninit || function() {
        
    };
    this.oninit();

    this.all.push(this);


    this.container = $("<div class='chatcomplete contentblock'></div>")
            .append(this.header = $("<div class='chatheader'></div>")
                    .append(this.chathead = $("<div class='chat-head'></div>"))
                    .append(this.chatinfo = $("<div class='chat-info'></div>")
                            .append(this.chat_header_text = $("<p class='chat_header_text ellipsis_overflow user_preview_name'></p>")
                                    .append(this.name))
                            .append(this.preview = $("<div class='chat-preview ellipsis_overflow'></div>"))))
            .append(this.chatcontainer = $("<div class='chat-container'></div")
                    .append("<div></div>")
                    .append(this.chatoutput = $("<div class='chatoutput'></div>")
                            .append(this.loader = new Application.prototype.UI.Loader().hide())
                            .append(this.list = $("<ul class='chatreceive'></ul>")))
                    .append($("<div class='text_input_container'></div>")
                            .append(this.input = $("<textarea class='chatinputtext autoresize' placeholder='Press Enter to send...'></textarea>"))));

    
    if(this.prop.active === true) {
        this.container.addClass('active');
    }
    this.prop.element.prepend(this.container);
    this.iniScroll();
    this.sendRequest('true');

    var self = this;
    this.input.on("propertychange keydown input change", function(e) {
        var element = $(this);
        if (e.keyCode == 13) {
            if (e.shiftKey !== true) {
                e.preventDefault();
                self.beforesubmit(function() {
                    self.submit(element.val());
                });
                
            }
        }
//        self.detectChange();
    });
};
Application.prototype.Chat.prototype.all = new Array();
Application.prototype.Chat.prototype.get = function() {
    return this.all;
};
Application.prototype.Chat.prototype.getChatRoom = function() {
    var self = this;
    $.get("/Scripts/chat.class.php", {action: "getChat", receiver: self.prop.receiver}, function(response) {
        response = parseInt($.parseJSON(response));
        if(response === -1) {
            self.id = 'create';
        } else {
            self.id = response;
        }
        console.log(response);
    });
};

Application.prototype.Chat.prototype.iniScroll = function() {
    var self = this;
    this.chatoutput.on('scroll', function() {
        self.bottom = false;
        if ($(this)[0].scrollTop === 0) {
            self.getPrevious();
        } else if($(this)[0].scrollHeight - $(this)[0].scrollTop - $(this).height() < 20) {
            self.bottom = true;
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
            $.get("/Scripts/chat.class.php", object, function(response) {
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
    $.get("/Scripts/chat.class.php", {chat: self.id, all: all, oldest: 0, newest: self.newest}, function(response) {
        $('[data-chat_room="' + self.id + '"] .chat_loader').slideUp('fast');
        response = $.parseJSON(response);

        if (all == 'true') {
            $('.chatcomplete').fadeIn("fast");
            self.list.append(self.styleResponse(response));
            self.chatoutput.scrollTop(self.chatoutput.get(0).scrollHeight);

        } else {
            $(self.list).append(self.styleResponse(response));
        }
        if (all == 'false' && response.length > 0) {
            for (var i = response.length - 1; i >= 0; i--) {
                if (response[i]['user_id'] != MyUser.attr.id) {
                    $('#chat_new_message_sound').get(0).play();
                }
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
        if ($.inArray(response[i]['id'], this.entry === -1)) {
            var ChatItem = new Application.prototype.Chat.prototype.ChatItem(response[i]);
            this.preview.html(ChatItem.preview);
            final.append(ChatItem.print());
            this.entry.push(ChatItem.id);
            this.items.push(ChatItem);
        }
    }
    ;

    this.newest = Array.max(this.entry);
    this.oldest = Array.min(this.entry);
    return final.html();
};

Application.prototype.Chat.prototype.ChatItem = function(item) {
    this.item = item;
    this.time = {};
    this.time.time = item['time'];
    this.item.pic = item['pic'] || Application.prototype.default.pic;
    this.preview = $('<span><img style="width:20px;" src="' + this.item.pic.icon + '"/> ' + item['text'] + '</span>');
    this.id = item['id'];

    this.print = function() {
        var final = $('<div></div>');

        var string = $("<li class='single_chat'></li>")
        var chat_wrapper = $("<div class='chat_wrapper " + (MyUser.attr.id == item['user_id'] ? 'self-chat' : 'other-chat') + "'>");
        var profile_picture = $("<div data-user_id='" + item['user_id'] + "' class='profile_picture_medium online_status'>");
        profile_picture.css('background-image', 'url("' + this.item.pic.icon + '")');
        var chat_bubble = $("<div class='chat-content'></div>");
        var chat_name = $("<div class='chatname'></div>").append("<span class='user_preview user_preview_name chatname' user_id='" + item['user_id'] + "'>" + item['name'] + "</span>");
        var chat_text = $("<div class='chattext'>").append(item['text'].replaceLinks().replaceEmoticons());
        chat_bubble.append(chat_name);
        chat_bubble.append(chat_text);
        chat_bubble.append(this.time.element = $("<span class='chat_time post_comment_time'>" + new Date.mysql(item.time).timeAgo('short') + "</span>"));

        if (MyUser.attr.id == item['user_id']) {
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
        this.chatoutput[0].scrollTop = this.chatoutput[0].scrollHeight;
    }
};

Application.prototype.Chat.prototype.submit = function(chat_text) {
    var self = this;
    console.log(self.id);
    if (chat_text != "") {
        $('.chatinputtext').val('');
        $('.chatinputtext').attr('placeholder', "Sending...");
        $('.chatinputtext').attr('readonly', 'readonly');
        $.post("/Scripts/chat.class.php", {action: "addchat", aimed: self.id, chat_text: chat_text}, function(response) {
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
Application.prototype.Feed = function(entity_id, entity_type, properties) {
    this.prop = properties || {};
    this.post = new Application.prototype.Post({}).element;
    this.prop.container.replaceWith(this.feed = $("<div></div>")).append(this.loader = new Application.prototype.UI.Loader());
    if(entity_type !== 'activity') {
    	this.feed.prepend(this.post);
    }
    this.entity_id = entity_id;
    this.entity_type = entity_type;
    this.items = new Array();
    this.feed.push(this);
    this.min = 0;
    this.max = 9999999999;
    this.onfetch = function() {
        this.print();
    };
    this.noItems = Application.prototype.default.emptyFeed;
//    this.loader = new Application.prototype.UI.Loader();
    this.get = function() {
        if (this.active !== true) {
            this.prop.container.append(this.loader);
            this.active = true;
        }
        this.loader.show();
        var data = {
            action: "get_feed",
            min: this.min,
            max: this.max,
            entity_id: this.entity_id,
            entity_type: this.entity_type,
        };
        var self = this;
        $.post('/Scripts/home.class.php', data, function(response) {
            response = $.parseJSON(response);
            for (var i = 0; i < response.length; i++) {
                self.addItem(response[i]);
            }
            self.loader.hide();
            self.onfetch(response);
            if (self.active === true && self.items.length === 0) {
                self.feed.append(self.noItems);
            } else {
                self.noItems.remove();
            }
        });
    };

    this.print = function() {
        for (var i = 0; i < this.items.length; i++) {
            this.feed.append(this.items[i].print());
        }
    };
};

Application.prototype.Feed.prototype.feeds = new Array();

Application.prototype.Feed.prototype.addItem = function(item) {
	if(this.items) {
		var item = new Application.prototype.Feed.prototype.Item(item);
		this.items.push(item);
		return item;
	} else {
		for(var i = 0; i < this.feeds.length; i++) {
			this.feeds[i].feed.append(this.feeds[i].addItem(item).print());
		}
	}
};

Application.prototype.Feed.prototype.getNew = function() {
    this.min = this.max;
    this.max += 100;
    this.get();
};

Application.prototype.Feed.prototype.getOld = function() {

};

Application.prototype.Feed.prototype.Item = function(item) {
    this.item = item;
    this.print = function() {
        return this.homify();
    };
    this.homify = function() {
        this.item.stats.like.count = parseInt(this.item.stats.like.count);
        this.item.user = new Application.prototype.User(this.item.user);
        this.item.status_text = typeof this.item.status_text !== 'undefined' && !isEmpty(this.item.status_text) ? this.item.status_text : '';
        var string = $("<div data-this.item_id='" + this.item.id + "' class='post_height_restrictor contentblock' id='post_height_restrictor_" + this.item.id + "'></div>");
        if (this.item.view == 'home') {
            var user_link = $("<a class='user_name_post' href='/user/" + this.item.user.id + "/'></a>");
            user_link.append(this.item.user.printImg());
            var user_name = $("<a class='user_name_post user_preview user_preview_name' data-user_id='" + this.item.user.entity.id + "' href='/user/" + this.item.user.entity.id + "/'>" + this.item.user.entity.name + "</a>");
            var top_content = $("<div class='top_content'>").append(user_link).append(user_name);
            var single_post = $('<div id="single_post_' + this.item.id + '" class="singlepostdiv"></div').append(top_content);
            string.append(single_post);
            var Stats = new Application.prototype.Feed.prototype.Item.prototype.Stats(this.item);
            if (!MyUser.userAgent.isMobile) {
                top_content.append(Stats.printStats());
            }
            var content_wrapper = $("<div class='post_content_wrapper'></div>");
            content_wrapper.append("<p class='post_text'>" + this.item.status_text + '</p>');

            if (this.item.media.length > 0) {
                var media_wrapper = $("<div class='post_feed_media_wrapper'></div>");
                content_wrapper.append(media_wrapper);
                for (var i in this.item.media) {
                    media_wrapper.append(new Application.prototype.File(this.item.media[i]).print(this.item.type));
                }
            }
            single_post.append(content_wrapper);
            if (MyUser.userAgent.isMobile) {
                var stats = $("<div class='this.item_stats_mobile'></div>");
                stats.append(Stats.printStats());
                top_content.append(stats);
            }
            content_wrapper.append(new Application.prototype.Comment(this.item).print());
        }
        return string;
    };

//        this.refreshContent = function(id) {
//            return;
//            Application.prototype.feed.get(null, null, id, function(response) {
//                var activity_container = $('[data-activity_id="' + id + '"]');
//                var comment_container = activity_container.find('.comment_box_comment');
//                for (var i in response) { // FOR THAT ONE ACTIVITY
//                    for (var key in response[i].item.item) {
//                        append_comment(response[i].id, response[i].item.item[key]);
//                    }
//                    for (var media_id in response[i].media) {
//                        for (var key in response[i].media[media_id].activity.item) {
//                            append_comment(response[i].media[media_id].activity.id, response[i].media[media_id].activity.item.item[key]);
//                        }
//                    }
//                }
//            });
//        };
};

Application.prototype.Feed.prototype.Item.prototype.Stats = function(item) {
    this.item = item || this.item;
    this.printStats = function() {
        var string = $("<div class='activity_stats'></div>");
        string.append(this.printLikes());
        var time = $("<span class='post_comment_time'></span>").text(new Date.mysql(this.item['time']).timeAgo() + " |");
        string.append(time);
        var subarray = [{'element': time, 'time': this.item.time}];
        Application.prototype.calendar.datetime.entry.push(subarray);
        if (this.item.type == "File") {
            this.item.media[0] = (this.item.media[0] || new Object());
            this.item.media[0].view = new Object();
            this.item.media[0].view.count = (this.item.media[0].view.count || 0);
        }

        var Dropdown = new Application.prototype.UI.Dropdown({
            id: "activity_options",
            type: "actions"
        });
        Dropdown.addOptions([{
                class: "delete_activity",
                text: "Delete"
            }, {
                class: "edit_activity",
                text: "Edit"
            }]);
        string.append(Dropdown.print());
        return string;
    };
    this.printLikes = function() {
        var string = '';
        string += '<div class="who_liked_hover" ';
        string += 'style="display:inline;"> ';
        string += '<span class="post_comment_time post_like_count">' + this.item.stats.like.count + '</span>';
        string += '<i class="fa fa-heart heart_like_icon"></i>';
        string += '<div style="display:inline;">';
        string += '<span has_liked="';
        string += (this.item.stats.like.has_liked === true ? "true" : "false");
        string += '" class="post_comment_time user_preview_name activity_like_text post_like_activity">';
        string += (this.item.stats.like.has_liked === true ? Application.prototype.Language['unlike_text'] : Application.prototype.Language['like_text']) + '</span><span class="post_comment_time">|</span></div>';
        string += "</span>";
        string += '<div class="who_liked" id="who_liked_' + this.item.id + '">';
        for (var i = 0; i < this.item.stats.like.count; i++) {
            name = this.item.stats.like.user[i].name;
            if (i == 1) {
                string += name;
            }
            else {
                string += ",<br>" + name;
            }
        }
        if (this.item.stats.like.count == 0) {
            string += "No one has liked this post yet.";
        }
        string += "</div></div>";
        return string;
    }
};

Application.prototype.Post = function(options, element) {
    this.element = element || $("<div class='contentblock'></div>");
    var self = this;
    this.onsubmit = function(data) {
    };
    this.post_button = $('<button class="post-button pure-button-blue">Post</button>').on('click', function() {
        self.submit();
    });

    this.dropdown = new Application.prototype.UI.Dropdown({
        type: 'selector',
        id: 'post_dropdown',
        name: false
    });
    this.dropdown.addOptions([
        {
            value: 'a',
            text: "Public",
            class: "",
        },
        {
            value: '',
            text: "Only friends can see this post",
            class: "",
        }]);
    this.fileList = new Application.prototype.FileList('all', 0, MyUser.attr.id, {});
    this.fileList.onclick = function(file) {
        self.dia.slideToggle();
        self.addFile(file);
    };
    this.dia = this.fileList.element.hide();

    this.element
            .append(this.post_wrapper = $("<div class='post_wrapper'></div>")
                    .append($("<div class='post_content_wrapper'></div>")
                            .append(this.status = $('<textarea tabindex="1" placeholder= "Update Status or Share Files..." class="status_text autoresize"></textarea>')
                                    .focus(function() {
                                        $(this).css('min-height', '100px');
                                        self.post_options.show();
                                        $('.home_feed_post_container_arrow_border').css('border-right-color', 'rgb(70, 180,220)');
                                    }).focusout(function() {
                                $('.home_feed_post_container_arrow_border').css('border-right-color', 'lightgrey');
                            }))
                            .append(this.file_container = $("<div class='post_media_wrapper'></div>")
                                    .append(this.filecont = $("<div class='post_media_wrapper_background timestamp' style='text-align:left;'><span>Dropbox</span></div></div>")
                                            )
                                    .append(this.appended_files = $("<div class='appendFiles'></div>")
                                            )
                                            )).append(this.dia)

                    .append(this.post_options = $("<div class='post_more_options'></div>")
                            .append($("<div class='rfloat'></div>")
                                    .append(this.post_button)
                                    .append(this.dropdown.print().addClass('lfloat')))
                            .append($("<div class='lfloat'></div>")
                                    .append(this.addFileButton = $("<div class='action lfloat'><i class='fa fa-file'></i></div>").on('click', function() {
                                        self.dia.slideToggle();
                                    })))
                            ));

    this.files = new Array();
    this.submit = function() {
        var text = this.status.val();
        if (text != "" || this.files.length !== 0) {
           
            var files = new Array();
            for( var i = 0; i < this.files.length; i++) {
                files.push(this.files[i].file.id);
            }
            $.post("/Scripts/home.class.php", {action: "update_status", status_text: text, group_id: share_group_id, post_media_added_files: files}, function(data) {
                data = $.parseJSON(data);
                self.onsubmit(data);
//              self.clear();
            });
        }
    };
    return this;
};

Application.prototype.Post.prototype.addFile = function(object) {
    this.file_container.show();
    this.appended_files.show().append(object.print());
    this.files.push(object);
};
Application.prototype.Post.prototype.removeFile = function(object) {
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

Application.prototype.CommentItem = function(item) {
    this.item = item;
    this.item.user.pic = this.item.user.pic || Application.prototype.default.pic;
};

Application.prototype.CommentItem.prototype.delete = function() {
    this.comment.remove();
    $.post('/Scripts/home.class.php', {action: "deleteComment", comment_id: this.item.id}, function(response) {
    });
};

Application.prototype.CommentItem.prototype.show = function() {
    this.comment = $("<div class='single_comment_container' data-comment_id='" + this.item.id + "'></div>");
    this.item.like.like_text = (this.item.like.has_liked ? "Unlike" : "Like");
    this.comment.append("<div class='profile_picture_medium' style='background-image:url(\"" + this.item.user.pic.icon + "\");'></div>")
            .append(this.comment_info = $("<div class='single_comment_info'></div>")
                    .append($("<a class='userdatabase_connection' href='/user?id=" + this.item.user.id + "'></a>")
                            .append("<span class='user_preview user_preview_name post_comment_user_name' data-user_id='" + this.item.user.id + "'>" + this.item.user.name + " </span>"))
                    .append("<span class='post_comment_text'>" + this.item.text + "</span><br />")
                    .append(this.time = $("<span class='post_comment_time'>" + new Date.mysql(this.item.time).timeAgo('long') + " -</span>"))
                    .append("<span class='post_comment_time post_comment_liked_num'>" + this.item.like.count + "</span>")
                    .append("<i class='fa fa-heart heart_like_icon'></i>")
                    .append("<span data-has_liked='" + this.item.like.has_liked + "' "
                            + "class='user_preview_name post_comment_time post_comment_vote'>"
                            + this.item.like.like_text + "</span>"));

    if (this.item.user.id == MyUser.attr.id) {
        var self = this;
        this.comment_info.append($("<i class='fa fa-times delete_cross delete_cross_top comment_delete'></i>").on('click', function() {
            self.delete();
        }));
    }
    return this.comment;
};


Application.prototype.Comment = function(item) {
    this.item = item;
    this.comments = new Array();
    this.print = function() {
        var object = $('<div class="comment_box contentblock"></div>');
        this.comment = $('<div class="comment_box_comment"></div>');
        this.comment.append(this.showComments());
        object.append(this.comment);
        object.append(this.printInput());
        return object;
    };
    this.printInput = function() {
        var string = $("<div class='comment_input single_comment_container'></div>");
        var self = this;
        string.append("<div class='profile_picture_medium' style='background-image:url(\"" + MyUser.attr.pic.icon + "\");'></div>")
                .append($("<div class='single_comment_info'></div>")
                        .append(this.input = $('<textarea placeholder="Write a comment..." '
                                + 'class="home_comment_input_text inputtext" id="comment_' + this.item.id
                                + '"></textarea>').on('keydown', function(event) {
                                self.comment_text = $(this).val();
                            if (event.keyCode === 13) {
                                self.submit();
                                return false;
                            }
                        })));
        return string;
    };
    this.submit = function(callback) {
        var self = this;
//         var comment_text = this.input.val();//.replace(/^\s+|\s+$/g, "");
        if (this.comment_text == "") {
            return;
        }

        $.post("/Scripts/home.class.php", {comment_text: self.comment_text, post_id: this.item.id, action: 'submitComment'}, function(data) {
            self.input.val('');
            self.input.blur();
            data = $.parseJSON(data);
            var comment = new Application.prototype.CommentItem(data);
            self.comment.append(comment.show());
        });
    };
    this.showComments = function() {
        this.comments[this.item.id] = new Array();
        var string = $("<div></div>");
        this.item.comment.format = this.item.comment.format || 'all';
        if (this.item.comment.format == 'top') {
            string.append("<div class='activity_actions user_preview_name post_comment_user_name' style='font-weight:100;'><a href='post/" + this.item.id + "/'>Show <span class='num_comments'>" + this.item.comment.hidden + "</span> more comments...</a></div>");
        }
        for (var i in this.item.comment.comment) {
            this.comments[this.item.id].push(this.item.comment.comment[i].id);
            var commentItem = new Application.prototype.CommentItem(this.item.comment.comment[i]);
            string.append(commentItem.show());
        }

        this.comments[this.item.id]['max'] = Array.max(this.comments[this.item.id]);
        this.comments[this.item.id]['min'] = Array.min(this.comments[this.item.id]);
        return string;
    };
};



Application.prototype.calendar = {
    event: {},
    datetime: {
        entry: new Array()
    }
};

Application.prototype.ConnectionList = function(props) {
    this.props = props;
    this.object = {};
    this.onfetch = function() {
        this.loader.remove();
        this.print();
    };
    this.loader = new Application.prototype.UI.Loader();
    this.update();
};

Application.prototype.ConnectionList.prototype.update = function() {
    var self = this;
    self.props.container.append(self.loader);
    $.get('/Scripts/user.class.php', {action: 'connections'}, function(response) {
        self.object = $.parseJSON(response);
        self.onfetch();
    });
};

Application.prototype.ConnectionList.prototype.print = function() {
    for (var i in this.object) {
        if (this.object[i].length > 0) {
            var container = $("<div class='contentblock'></div>").append("<h3>" + i + "</h3>");
            for (var key in this.object[i]) {
                if (i === 'Users' || i === 'Connections') {
                    var user = new Application.prototype.User(this.object[i][key]);
                    container.append(user.print({online_status: true}));
                } else {
                    var group = new Application.prototype.Group(this.object[i][key]);
                    container.append(group.print({}));
                }
            }
            this.props.container.append(container);
        }
    }
};

Application.prototype.UI = {
    defaults: {
        fileUpload: true,
        connectionList: true
    },
    prop: {},
    init: function() {
        for (var i in this.defaults) {
            if (!this.prop.hasOwnProperty(i)) {
                this.prop[i] = this.defaults[i];
            }
        }
        this.update();
    },
    update: function() {
        this.fileUpload = $('<div class="upload_file_container contentblock"></div>');
        new Application.prototype.UI.DragUpload({container: this.fileUpload});
        $('.right_bar_container').append(this.fileUpload.hide());
        
        this.connectionList = $("<div></div>");
        new Application.prototype.ConnectionList({container: this.connectionList});
        $('.left_bar_container').append(this.connectionList);
        
        if (this.prop.fileUpload === true) {
            this.fileUpload.show();
        } else {
            this.fileUpload.remove();
        }
        if (this.prop.connectionList === true) {
            this.connectionList.show();
        } else {
            this.connectionList.remove();
        }
        Application.prototype.UI.vNav.prototype.setActive();

        this.prop = this.defaults;
        if($('.noLeftBar').length > 0 || Application.prototype.UI.prop.leftBar === false) {
            $('.left_bar_container').css('display', 'none');
        } else {
            $('.left_bar_container').css('display', 'block');
        }
        if($('.noRightBar').length > 0 || Application.prototype.UI.prop.rightBar === false) {
            $('.right_bar_container').css('display', 'none');
        } else {
            $('.right_bar_container').css('display', 'block');
        }

        $('.upload_here').each(function() {
//             $(this).replaceWith(new Application.prototype.UI.DragUpload().print());
        });
        $('.file_container').each(function() {
            $(this).replaceWith(new Application.prototype.FileList().print());
        });
        $('select.dropdown').each(function() {
            var dropdown = new Application.prototype.UI.Dropdown({
                id: 'select',
                type: 'selector',
                name: $(this).data('text'),
                change: $(this).data('change'),
                href: $(this).data('href')
            });
            var options = new Array();
            var i = 0;
            $(this).find('option').each(function() {
                options[i] = {
                    value: $(this).val(),
                    text: $(this).text(),
                    selected: $(this).attr('selected'),
                    href: $(this).data('href')
                };
                i++;
            });
            dropdown.addOptions(options);
            $(this).replaceWith(dropdown.print());
        });
    },
    launchFullscreen: function(element) {
        if (element.requestFullscreen) {
            element.requestFullscreen();
        } else if (element.mozRequestFullScreen) {
            element.mozRequestFullScreen();
        } else if (element.webkitRequestFullscreen) {
            element.webkitRequestFullscreen();
        } else if (element.msRequestFullscreen) {
            element.msRequestFullscreen();
        }
    },
    dropArrow: $("<i class='fa fa-angle-down'></i>"),
    progress: function() {
        this.container = $("<div class='progress_container'></div>");
        this.container.append($("<div class='progress_bar'></div>"));
        this.print = function() {
            return this.container;
        };

        this.update = function(progress) {
            this.container.width(progress + "%");
            if (progress >= 100) {
                this.container.addClass('progress_bar_processing');
            }
            this.progress = progress;
        };

        this.remove = function() {
            this.container.remove();
        };
        return this;
    },
    ButtonSwitch: function(props) {
        var self = this;
        this.prop = props || {};
        this.prop.container = this.prop.container || $("<div></div>");
        this.containerg = $("<div class='buttons'></div>").append(
        this.container = $("<ul class='buttons'></ul>"));
        this.prop.container.append(this.containerg);
        this.addOptions = function(options) {
            var hasSelect = false;
            for (var o = 0; o < options.length; o++) {
                if (options[o].selected) {
                    hasSelect = true;
                }
            }
            for (var i = 0; i < options.length; i++) {

                var item = $("<li></li>").attr('title', options[i].text).on('click', options[i].onclick);
                var item_content = $("<div class='ellipsis_overflow'></div>");
                if (options[i]['selected'] || (hasSelect === false && options[i]['selected'] !== false)) {
                    hasSelect = true;
                    item.addClass('active');
                }
                if (options[i].unexecutable !== true) {
                    item.on('click', function() {
                        $(this).siblings().removeClass('active');
                        $(this).addClass('active');
                    });
                }
                if (options[i].href) {
                    item_content.append(item_content = $("<a href='" + options[i].href + "'>" + options[i].text + "</a>"));
                } else {
                    item_content.text(options[i].text);
                }
                if (options[i].icon) {
                    item_content.prepend("<i class='fa " + options[i].icon + "'></i>");
                }
                this.container.append(item.append(item_content));
            }
        }
        this.print = function() {
            return this.containerg;
        }
    },
    Loader: function(props) {
        this.prop = props || {class: []};
        this.object = $('<div class="loader"></div>').append($('<i class="fa fa-spinner fa-spin"></i>'));
        for (var i = 0; i < this.prop.class.length; i++) {
            this.object.addClass(this.prop.class[i]);
        }
        return this.object;
    },
    Dropdown: function(controller) {
        var self = this;
        this.options = controller || {};
        this.options.change = this.options.change || true;
        this.options.type = this.options.type || "selector";
        this.options.hover = this.options.hover || "undefined";
        
        this.class = 'default_dropdown_' + this.options.type;
        if(this.options.hover === true) {
            this.class += " default_dropdown_hover";
        } else if(this.options.hover === false) {
            this.class += " default_dropdown_nohover";
        }
        this.object = $("<div class='" + this.class + "' wrapper_id='" + this.options.id + "'></div>")
                .on('click', function(event) {
                    event.stopPropagation();
                    self.object.toggleClass('default_dropdown_active');
                });
                
        if (typeof this.options.href != 'undefined') {
            self.preview = $('<a href="' + this.options.href + '"><div class="default_dropdown_preview">' + this.options.name + '</div></a>');
        } else {
            self.preview = $('<div class="default_dropdown_preview">' + this.options.name + '</div>');
        }
        
        if (this.options.type == 'selector') {
            this.object.append(self.preview);
        }
        this.wrapper = $("<div class='default_dropdown_wrapper'></div>");
        this.list = $("<ul class='default_dropdown_menu'></ul>");

        this.print = function() {
            if(this.list.find("li").length > 0) {
                this.wrapper.append(this.list);
                this.preview.after($("<i class='fa fa-caret-down'></i>"));
                this.object.append(this.wrapper);
            }
            return this.object;
        };
        this.addOptions = function(options) {
            for (var i = 0; i < options.length; i++) {
                if (options[i]['selected'] || self.list.children('li').length === 0 && this.options['name'] == "") {
                    self.preview.html(options[i].text);
                    self.object.val(options[i]['value']);
                }
//                Immediate Function Invokation
                (function(i) {
                    options[i]['value'] = options[i]['value'] || options[i]['text'];
                    var item = $("<li value='" + options[i]['value'] + "' class='default_dropdown_item " + options[i].class + "'></li>").on('click', function(event) {
                        event.stopPropagation();
                        if(self.options.change !== false) {
                            self.preview.text(options[i].text);
                            self.object.val(options[i]['value']);
                        }
                        self.object.toggleClass('default_dropdown_active');
                    });
                    if(typeof options[i].href != 'undefined') {
                        item.html("<a href='" + options[i].href + "'><span>" + options[i].text + "</span></a>");
                    } else {
                        item.html("<span>" + options[i].text + "</span>");
                    }
                    self.list.append(item);
                }(i));
            }
        };
        $(document).on('click', function() {
            self.object.removeClass('default_dropdown_active');
        });
    },
    vNav: function(options) {
        var self = this;
        this.items.push(this);
        this.prop = options || {};
        this.object = $("<div class='vNav contentblock'></div>");
        this.prop.container.remove('.vNav');
        this.prop.container.append(this.object);

        this.addOptions_ = function(options, list) {
            list = list || this.object;
            var hasSelect = false;
            for (var o = 0; o < options.length; o++) {
                if (options[o].selected) {
                    hasSelect = true;
                }
            }
            for (var i = 0; i < options.length; i++) {

                var item = $("<div class='ellipsis_overflow item'></div>").attr('title', options[i].text).on('click', options[i].onclick);
                var item_content = $("<div></div>");
                if (options[i]['selected'] || hasSelect === false) {
                    hasSelect = true;
//                    item.addClass('active');
                }
                if (options[i].unexecutable !== true) {
                    item.on('click', function() {});
                }
                if (options[i].href || 1) {
                    item_content.append(item_content = $("<a href='" + options[i].href + "'>" + options[i].text + "</a>"));
                } else {
                    item_content.text(options[i].text);
                }
                if (options[i].icon) {
                    item_content.prepend("<i class='fa " + options[i].icon + "'></i>");
                }
                list.append(item.append(item_content));
                if (options[i].children) {
                    var new_list = $('<div></div>');
                    item.append(new_list);
                    this.addOptions_(options[i].children, new_list);
                }
            }
        };
        this.addOptions = function(options) {
            for (var i = 0; i < options.length; i++) {
                options[i].children = options[i].children || new Array();
                var sub = $("<div class='sub'></div>");
                var header = $('<h3 class="header">' + options[i].text + '</h3>');
                this.object.append(header);
                this.addOptions_(options[i].children, sub);
                this.object.append(sub);
            }
            self.setActive();
        }
    },
    DragUpload: function(options) {
        this.options = options || {};
        this.options.round = this.options.round || true;
        this.options.buttons = this.options.buttons || [
            {
                text: "Files",
                onclick: function() {
                    var input = $("<input multiple='multiple' type='file'/>");
                    input.trigger('click');
                    input.on('change', function() {
                        upload.addFiles(input[0].files);
                        upload.push();
                    });
                },
                icon: "fa-file"
            },
            {
                text: (navigator.userAgent.toLowerCase().indexOf('chrome') > -1 ? "Folder" : "Folder Upload only available in new versions of Chrome"),
                onclick: (navigator.userAgent.toLowerCase().indexOf('chrome') > -1 ? function() {
                    var input = $("<input multiple='multiple' webkitdirectory='webkitdirectory' mozdirectory='mozdirectory' directory='' type='file'/>");
                    input.trigger('click');
                    input.on('change', function() {
                        upload.addFiles(input[0].files);
                        upload.push('folder');
                    });
                } : function() {
                    dialog = new Application.prototype.UI.Dialog({});
                    dialog.addButton({type: "success", text: "OK"});
                    dialog.title('Oh no...');
                    dialog.content('Unfortunately you are not using the most up to date version of Chrome. This means you cannot upload folders, but you will have to upload File by File. In the future, you may be able to sync using a Desktop alternative.');
                    dialog.show();
                }),
                unexecutable: true,
                icon: "fa-folder"
            }
        ];
        var entered = 0;
        var self = this;
        var upload = new Application.prototype.Upload();
        this.options.container.append('<h3>File Upload</h3>');
        this.buttonSwitch = new Application.prototype.UI.ButtonSwitch({container: this.options.container});
        this.buttonSwitch.addOptions(this.options.buttons);
        this.drag = $("<div class='upload_replaced'></div>");
        if (this.options.round === true) {
            this.drag.addClass('round');
        }

        this.drag.on('dragover', function(event) {
            var files = (event.dataTransfer && event.dataTransfer);
            if (!files) {
                files = event.dataTransfer || (event.originalEvent && event.originalEvent.dataTransfer);
            }
            if (files) {
                for (var i = 0; i < files.types.length; i++) {
                    if (files.types[i] == "Files") {
                        $(this).addClass('upload_hover');
                        entered++;
                    }
                }
            }
        });
        this.drag.on('dragleave', function(event) {
            $(this).removeClass('upload_hover');
            entered--;
        });

        this.drag.on('drop', function(e) {
            $(this).removeClass('upload_hover');
            entered--;
            if (e.dataTransfer.items) {
                var length = e.dataTransfer.items.length;
                for (var i = 0; i < length; i++) {
                    var entry = e.dataTransfer.items[i].webkitGetAsEntry();
                    if (entry.isFile) {
                        alert('file');
                    } else if (entry.isDirectory) {
                        alert('dir');
                    }
// 					upload.addFiles(files.files);
                }
            }
            var files = e.target.files || (e.dataTransfer && e.dataTransfer.files);
            if (!files) {
                files = e.dataTransfer || (e.originalEvent && e.originalEvent.dataTransfer);
            }
            upload.addFiles(files.files);
            upload.push('folder');
        });
        this.drag.on("dragenter dragstart dragend dragleave dragover drag drop", function(e) {
            e.preventDefault();
            e.stopPropagation();
        });
//         this.container.append(this.drag); //NOT SHOWING DRAG UPLOADER
        this.print = function() {
            return this.container;
        }
    }
};
Application.prototype.UI.vNav.prototype.items = new Array();
Application.prototype.UI.vNav.prototype.setActive = function(item) {
    item = this || item;
    if (item.object) {
        item.object.find('a').each(function() {
            if (document.location.href.indexOf($(this).attr('href')) >= 0) {
                $(this).parents('div').first().addClass('active');
            } else {
                $(this).parents('div').first().removeClass('active');
            }
        });
    } else {
        for (var i = 0; i < Application.prototype.UI.vNav.prototype.items.length; i++ ) {
            Application.prototype.UI.vNav.prototype.items[i].setActive(Application.prototype.UI.vNav.prototype.items[i]);
        }
    }
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

Application.prototype.UI.Dialog = function(properties) {
    var self = this;
    this.properties = properties || {};
    this.properties.modal = (typeof this.properties.modal === "undefined") ? true : this.properties.modal;
    this.properties.loading = (typeof this.properties.loading === "undefined") ? false : this.properties.loading;
    this.properties.title = (typeof this.properties.title === "undefined") ? "Undefined Title" : this.properties.title;
    this.properties.width = (typeof this.properties.width === "undefined") ? "auto" : this.properties.width;
    this.properties.subheader = this.properties.subheader || "";

    this.dialog_container = $("<div class='dialog_container contentblock'></div>").css({'opacity': '0'});

    var closingX = $("<i class='dialog_close_button fa fa-times'></i>").click(function() {
        self.remove();
    });
    this.title = $("<h3>" + this.properties.title + "<span>" + this.properties.subheader + "</span></h3>");//.append(closingX)

    this.content_container = $("<div class='dialog_content_container'></div>");
    this.dialog_container.append(this.title);
    this.dialog_container.append(this.content_container);

    this.dialog_container.width(properties.width);
    this.button_complete = $('<div></div>');

    var dialog_buttons = $("<div class='dialog_buttons'></div>");
    this.dialog_container.append(dialog_buttons);
    dialog_buttons.append(this.button_complete);

//    this.content_container.mCustomScrollbar({
//        scrollInertia: 10,
//        autoHideScrollbar: true,
//    });

};

Application.prototype.UI.Dialog.prototype.show = function() {
    var self = this;
// 	if (this.properties.modal == true) {
// 		$('body').append("<div class='background-overlay'></div>");
// 	} else {
    $('body').append(this.background = $("<div style='opacity:0.5' class='background_white_overlay'></div>").click(function() {
        self.remove();
    }));
// 	}
    $('body').append(this.dialog_container);
    this.alignDialog();
    var real_height = this.dialog_container.height();
    this.dialog_container.css({height: "0px"});
    this.dialog_container.animate({minHeight: real_height + "px", opacity: 1}, 100, function() {
        $(this).css({height: "auto", opacity: 1}, 'fast');
//        self.content_container.mCustomScrollbar("update");
        setTimeout(function() {
//            self.content_container.mCustomScrollbar("update");
        }, 200);
    });
};

Application.prototype.UI.Dialog.prototype.addButton = function(button) {
    var single_button = document.createElement('button');
    $(single_button).addClass('small');
    $(single_button).addClass('pure-button-' + button.type);
    $(single_button).css('float', 'right');
    $(single_button).text(button.text);
    single_button.onclick = button.onclick;
    this.button_complete.append(single_button);
};
Application.prototype.UI.Dialog.prototype.alignDialog = function() {
    var width = this.dialog_container.width();
    var height = this.dialog_container.height();
    this.dialog_container.css({
        'margin-left': '-' + width / 2 + "px",
        'margin-top': '-' + height / 2 + "px",
    });
};

Application.prototype.UI.Dialog.prototype.remove = function() {
    this.background.remove();

    this.dialog_container.css('min-height', '0px');
    this.dialog_container.animate({height: 0, opacity: 0}, 100, function() {
        $(this).remove();
    });
};
Application.prototype.UI.Dialog.prototype.content = function(content) {
    this.content_container.append(content);
};
Application.prototype.UI.Dialog.prototype.title = function(content) {
    this.title.html(content);
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
    $.post("/Scripts/searchbar.php", {search: mode, input_text: text}, function(response) {
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

Application.prototype.navigation.relocate = function(link, get) {
    this.get = get || true;
    this.initial = false;

    window.history.pushState({}, 'WhatTheHellDoesThisDo?!', link);//Push new URL before waiting for load to complete
    if (this.get) {
        $('.global_container').html(new Application.prototype.UI.Loader());
        $.get(link, {ajax: 'ajax'}, function(response) {
            var container = $(response);
            $('.global_container').replaceWith(container);
            Application.prototype.UI.update();
            $('body').scrollTop(0);
        });
    }
}

/****************************************************
 * 1.1 Prototypes                                    *
 ****************************************************/

String.prototype.replaceLinks = function() {
    var replacedText, replacePattern1, replacePattern2, replacePattern3;
    replacedText = this;
    //URLs starting with http://, https://, or ftp://
    replacePattern1 = /(\b(https?|ftp):\/\/[-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|])/gim;
    replacedText = replacedText.replace(replacePattern1, '<a class="no-ajax" href="$1" target="_blank">$1</a>');

    //URLs starting with "www." (without // before it, or it'd re-link the ones done above).
    replacePattern2 = /(^|[^\/])(www\.[\S]+(\b|$))/gim;
    replacedText = replacedText.replace(replacePattern2, '$1<a class="no-ajax" href="http://$2" target="_blank">$2</a>');

    //Change email addresses to mailto:: links.
    replacePattern3 = /(([a-zA-Z0-9\-\_\.])+@[a-zA-Z\_]+?(\.[a-zA-Z]{2,6})+)/gim;
    replacedText = replacedText.replace(replacePattern3, '<a class="no-ajax" href="mailto:$1">$1</a>');

    return replacedText;
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
                '<img src="/Images/' + emoticons[match] + '"/>' : //removed onload
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
    $.post('/Scripts/notifications.class.php', {action: "messageList"}, function(response) {
        $('ul.message').html(response);
        $('#popup_message').height($('ul.message').outerHeight(true));
        $('#popup_message').mCustomScrollbar('update');
    });
};

Application.prototype.notification.getNotificationBox = function() {
    $.post('/Scripts/notifications.class.php', {action: "notificationList"}, function(response) {
        $('ul.notify').html(response);
        $('#popup_notify').height($('ul.notify').outerHeight(true));
        $('#popup_notify').mCustomScrollbar('update');
    });
};

Application.prototype.notification.getNetworkBox = function() {
    $.post('/Scripts/notifications.class.php', {action: "networkList"}, function(response) {
        $('ul.network').html(response);
        $('#popup_network').height($('ul.network').outerHeight(true));
        $('#popup_network').mCustomScrollbar('update');
    });
};

Application.prototype.notification.getNotificationNumber = function() {
    $.post('/Scripts/notifications.class.php', {action: "alert_num"}, function(response) {
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
 * 1.4 Entity                                          *
 ****************************************************/
Application.prototype.Entity = function() {

};

Application.prototype.Entity.prototype.print = function(props) {
    props = props || {};
    var container = $("<div class='user-tag' data-user_id='" + this.entity.id + "'></div>");
    var a = $("<a class='friend_list ellipsis_overflow' style='background-image:url(\"" + this.entity.pic.icon + "\");'"
            + "href ='/" + this.baseUrl + "/" + this.entity.id + "/'>" + this.entity.name + "</a>");
    if(props.online_status === true) {
        container.addClass('user_preview');
        a.append("<div class='online_status online_circle rfloat' data-user_id='" + this.entity.id + "'></div>");
    }
    container.append(a);

    return container;
};

Application.prototype.Entity.prototype.printImg = function() {
    return $("<div class='profile_picture_medium' style='background-image:url(\"" + this.entity.pic.icon + "\");'></div>");
};

Application.prototype.Entity.prototype.printMap = function() {
    return $("<img src='http://maps.googleapis.com/maps/api/staticmap?center=" + this.location.coords.latitude
            + "," + this.location.coords.latitude + "&zoom=14&size=400x300&sensor=false' />");
};

Application.prototype.Entity.prototype.printHeader = function(prop) {
    this.container = $("<div class='contentblock userHeader'></div>");
    this.container.append($('<img src="' + this.entity.pic.icon + '"></img>'));
    this.container.append("<span class='user_preview_name'>" + this.entity.name + "</span>");
    this.switch = new Application.prototype.UI.ButtonSwitch();
    var options = [{
            text: "Feed",
            icon: "fa-list-ul",
            href: "/" + this.baseUrl + "/" + this.entity.id + "/feed"
        },
        {
            text: "Files",
            icon: "fa-file",
            href: "/" + this.baseUrl + "/" + this.entity.id + "/files"
        }];
    if (prop.tab === 'f') {
        options[0].selected = false;
        options[1].selected = true;
    }
    this.switch.addOptions(options);
    this.container.append(this.switch.print());
    prop.container.append(this.container);
};

Application.prototype.Entity.prototype.printPreview = function(prop) {
    var self = this;
    this.container = $("<div class='userHeader'></div>");
    this.container.append($('<img class="profile_picture_thumb" src="' + this.entity.pic.thumb + '"></img>'));
    this.container.append("<a href='/user/" + this.entity.id + "/'><span class='user_preview_name'>" + this.entity.name + "</span></a>");
    this.switch = new Application.prototype.UI.ButtonSwitch();
    var options = [{
            text: "Feed",
            icon: "fa-list-ul",
            href: "/" + this.baseUrl + "/" + this.entity.id + "/feed",
            selected: false
        },
        {
            text: "Files",
            icon: "fa-file",
            href: "/" + this.baseUrl + "/" + this.entity.id + "/files",
            selected: false
        },
        {
            text: "Message",
            icon: "fa-comment",
            selected: false,
            onclick: function() {
                var chat
                var chat = new Application.prototype.Chat({
                    id: "unknown",
                    receiver: [self.entity.id],
                    name: self.entity.name,
                    active: true,
                    oninit: function() {
                        this.getChatRoom();
                    }
                });
            }
        }];
    if (prop.tab === 'f') {
        options[0].selected = false;
        options[1].selected = true;
    }
    this.switch.addOptions(options);
    this.container.append(this.switch.print());
    prop.container.append(this.container);
};

Application.prototype.Entity.prototype.init = function(entity, type) {
    this.entity = entity;
    this.entity.pic = this.entity.pic || Application.prototype.default.pic;
    if (this.items[this.entity.id] && this.items[this.entity.id].type === type) {
        return this.items[this.entity.id];
    } else {
        this.items[this.entity.id] = this;
    }
};

Application.prototype.Entity.prototype.printFeed = function(prop) {
    var self = this;
    this.feed = new Application.prototype.Feed(this.entity.id, 'user', {container: prop.container});
    this.feed.get();
};

Application.prototype.Entity.prototype.printSharedFiles = function(prop) {
    var self = this;
    this.feed = new Application.prototype.FileList('all', 0, this.entity.id, {container: prop.container, class: []});
    this.feed.printItem = function(file) {
        return file.print_row();
    };
    this.feed.get();
};

Application.prototype.Entity.prototype.getInfo = function(callback) {
    var self = this;
    $.post('/Scripts/' + this.baseUrl + '.class.php', {action: "get_preview_info", id: this.entity.id}, function(response) {
        response = $.parseJSON(response);
        self.entity.pic = response.pic || Application.prototype.default.pic;
        callback();
    });
};



Application.prototype.User = function() {
    return Application.prototype.Entity.prototype.init.apply(this, arguments);
};
Application.prototype.Group = function() {
    return Application.prototype.Entity.prototype.init.apply(this, arguments);
};
Application.prototype.Group.prototype = new Application.prototype.Entity();
Application.prototype.User.prototype = new Application.prototype.Entity();

Application.prototype.Group.prototype.items = {};
Application.prototype.User.prototype.items = {};
Application.prototype.Group.prototype.baseUrl = 'group';
Application.prototype.User.prototype.baseUrl = 'user';

Application.prototype.User.prototype.MyUser = function() {
    this.userAgent = {
        isMobile: navigator.userAgent.match(/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/)
    };
    this.setAttr = function(attr) {
        this.attr = attr;
        this.attr.pic = this.attr.pic || Application.prototype.default.pic;
    };
    // if (navigator.geolocation) {
//         navigator.geolocation.getCurrentPosition( function(loc) {
//             Application.prototype.User.prototype.MyUser.prototype.location = loc;
//         });
//     }
//    this.setProfilePicture = function(file_id) {
//        $.post('/Scripts/user.class.php', {action: "profile_picture", file_id: file_id}, function(response) {
//            Application.prototype.UI.removeDialog();
//        });
//    }
};

Application.prototype.User.prototype.Preview = function(props) {
    var self = this;
    this.prop = props || {};
    this.user = new Application.prototype.User({id: this.prop.id});
    this.prop.element.on('mouseleave', function() {
        self.remove();
    });
    this.loader = new Application.prototype.UI.Loader();
    this.container = $('<div class="user_preview_info"></div>').append(this.object = $("<div class='contentblock force'></div>").append(this.loader));
};
Application.prototype.User.prototype.Preview.prototype.show = function() {
    var self = this;
    setTimeout(function() {
        if (self.prop.element.parent().find("[data-user_id='" + self.prop.id + "']:hover").length > 0) {
            self.prop.element.append(self.container);
            self.user.getInfo(function() {
                self.loader.remove();
                self.align();
                self.fill();
            });
        }
    }, 500);
};

Application.prototype.User.prototype.Preview.prototype.align = function() {
    var arrow;
    if (this.prop.element.parents('.container').length > 0) {
        alignment = "vertical";
    } else {
        alignment = "left";
    }

    if (this.prop.element.parents('.this.objectainer').length > 0) {
        alignment = "vertical";
    }
    arrow = 'user_preview_arrow-' + alignment;

    this.object.append('<div class="' + arrow + '-border"></div>');
    this.object.append('<div class="' + arrow + '"></div>');
};

Application.prototype.User.prototype.Preview.prototype.fill = function() {
    this.user.printPreview({container: this.object});
};

Application.prototype.User.prototype.Preview.prototype.remove = function() {
    this.container.remove();
};


Application.prototype.user = function() {
};
Application.prototype.user.showPhotoChoose = function() {
    var content = $("<div><table><tr><td><div class='upload_here'></div></td><td><div class='profile_picture_chooser' style='max-height:200px;overflow:auto;margin-left:20px;height:100%;' id='file_container'>Loading...</div></td></tr></table></div>");
    var photo = Application.prototype.UI.dialog(
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
                photo.remove();
            }
        }],
    properties = {
        title: "Choose a photo"
    });
};

Application.prototype.user.connect = function(element, user_id) {
    $.post('/Scripts/user.class.php', {action: "connect", user_id: user_id}, function() {
        element.addClass('connect_button_invited');
    });
};

Application.prototype.user.connectAccept = function(invite_id) {
    $.post('/Scripts/user.class.php', {invite_id: invite_id, action: "acceptInvite"}, function() {

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
        $.post('/Scripts/user.class.php', {action: "getOnlineMass", users: users}, function(response) {
            response = $.parseJSON(response);
            for (var prop in response) {
                if (response.hasOwnProperty(prop)) {
                    if (response[prop] == true) {
                        $('.online_status[data-user_id="' + prop + '"]').addClass('profile_online').removeClass('profile_offline');
                    } else {
                        $('.online_status[data-user_id="' + prop + '"]').addClass('profile_offline').removeClass('profile_online');
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
    $.post("/Scripts/group_actions.php", {action: "invite", user_id: id, group_id: group_id}, function(response) {
        if (("success").indexOf(response))
        {
//            removeDialog();
        }
        else
        {
            alert(response);
        }
    });
};

Application.prototype.user.submitData = function() {
    var about = $('#about_edit_show').html();
    var email = '';
    var school = '';
    var year = '';
    $.post('/Scripts/user.class.php', {about: about, email: email, year: year}, function(response) {
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
    $.post('/Scripts/notifications.class.php', {action: "deleteMessage", thread: thread}, function(response) {
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
    $.get('/Scripts/calendar.class.php', {action: "get_events", limit: limit}, function(response) {
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
    $.post('/Scripts/user.class.php', {action: "update", id: 1, html: text}, function(response) {
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
        e.preventDefault();
        Application.prototype.navigation.relocate($(this).attr('href'));
    });

    window.onpopstate = function(event) {
        if (Application.prototype.navigation.initial === false) {
            Application.prototype.navigation.relocate(window.location);
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

    $(document).on('mouseenter', '.user_preview', function(event) {
        var user_id = $(this).data('user_id');
        var preview = new Application.prototype.User.prototype.Preview({element: $(this), id: user_id});
        preview.show();
    });
    /****************************************************
     * 2.1.3 Startup - Apps                              *
     ****************************************************/
    
    new Application.prototype.App.prototype.AppList({container: $('.right_bar_container')});

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

    $(document).on('click', '.global_header_container .search_option', function() {
        var entity = $(this).data('entity');
        var link;
        if (entity.entity_type == 'user') {
            link = '/user/' + entity.id + "/";
        } else if (entity.entity_type == 'group') {
            link = '/group/' + entity.id + "/";
        } else {
            link = '/files/' + entity.id + "/";
        }
        Application.prototype.navigation.relocate(link);
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

    Application.prototype.UI.init();

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
        var deleter = Application.prototype.UI.dialog(
                content = {
                    type: "html",
                    content: "Are you sure you want to delete this Post?"
                },
        buttons = [{
                type: "success",
                text: "Delete",
                onclick: function() {
                    delete_post(activity_id);
                    deleter.remove();
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
        Application.prototype.File.list(file_container, '');
    });

    $(document).on('click', '.edit_activity_save', function() {
        var activity_id = $(this).parents('[data-activity_id]').data('activity_id');
        var text = $('[data-activity_id="' + activity_id + '"]').find('textarea.autoresize').val();
        $('[data-activity_id="' + activity_id + '"]').find('.edit_activity_save').attr('disabled', 'disabled').addClass('pure-buton-disabled');

        $.post('/Scripts/home.class.php', {activity_id: activity_id, action: "updatePost", text: text}, function() {
            $('[data-activity_id="' + activity_id + '"]').find('.post_text').show().html(text);
            $('[data-activity_id="' + activity_id + '"]').find('.post_text_editor').remove();
        });
    });

//    $(document).on('keyup', '.inputtext', function(event) {
//        var id = $(this).attr('id');
//        var clone = $('#' + id + "_clone")
//        if (event.keyCode == 13) {
//            var post_id = $(this).parents('[data-activity_id]').data('activity_id');
//            Application.prototype.feed.item.submit($(this).val(), post_id, function(this.item) {
//                Application.prototype.feed.this.item.append(post_id, this.item);
//            });
//            return false;
//        }
//    });

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
        $.post('/Scripts/home.class.php', data, function(response) {
            reponse = $.parseJSON(response);
            activity.item = response;
            for (var i in activity.item.item) {
                comments[activity.id].push(activity.item.item[i].id);
                append_comment(post_id, activity.item.item[i]);
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
           var remover = Application.prototype.UI.dialog(
                    content = {
                        type: "html",
                        content: "If you delete this file, your post will be removed, as it does not contain any content. Continue?"
                    },
            buttons = [
                {
                    type: "success",
                    text: "OK",
                    onclick: function() {
                        $.post('/Scripts/files.class.php', {action: "removePostFile", file_id: file_id, activity_id: activity_id}, function() {
                        });
                        $(this).parents('.post_feed_item').remove();
                        delete_post(activity_id);
                        remover.remove();
                    }
                },
                {
                    type: "neutral",
                    text: "Cancel",
                    onclick: function() {
                        remover.remove();
                    }
                }],
            properties = {
                modal: false,
                title: "Remove File from Post"
            });
        } else {
            $(this).parents('.post_feed_item').remove();
            $.post('/Scripts/files.class.php', {action: "removePostFile", file_id: file_id, activity_id: activity_id}, function() {
            });
        }
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
                $(this).text(Application.prototype.Language['unlike_text']);
            } else {
                $(this).text(Application.prototype.Language['like_text']);
            }
        }
        $('[data-activity_id="' + post_id + '"] .post_like_count:first').text(like_count);

        $.post("/Scripts/home.class.php", {type: 1, activity_id: post_id, action: "like"}, function(data)
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
            $(this).text(Application.prototype.Language['like_text']);
        }
        else {
            like_count++;
            $(this).data('has_liked', "true");
            $(this).text(Application.prototype.Language['unlike_text']);
        }

        $('[data-comment_id="' + comment_id + '"] .post_comment_liked_num').text(like_count);
        $.post('/Scripts/home.class.php', {action: "comment_vote", comment_id: comment_id}, function(response) {
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
        $.post('/Scripts/files.class.php', {action: "rename", file_id: file_id, text: $(this).val()}, function() {

        });
        $(this).prev('p.files').text($(this).val()).show();
        $(this).hide();
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
//         if (Application.prototype.File.theater.active) {
//             Application.prototype.File.theater.adjust();
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
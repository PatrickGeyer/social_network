<?php
include_once 'Scripts/declare.php';

function login($user_id) {
    setcookie("home_feed", 'a', time() + 3600000, '/');
    setcookie("id", base64_encode($user_id), time() + 3600000);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_query = "SELECT id FROM user WHERE email = :email AND password = :password;";
    $user_query = Registry::get('db')->prepare($user_query);
    $user_query->execute(array(":email" => $_POST['email'], ":password" => Registry::get('system')->encrypt($_POST['password'])));
    $user_data = $user_query->fetchColumn();
    //die("ID:".$user_data. "EMAIL:".$_POST['email'] ."ENCRYOT:".Registry::get('system')->encrypt($_POST['password']));

    if (!empty($user_data)) {

        //$rooms = $chat->get_chat_rooms();
        //setcookie("chat_feed", $rooms[0]['id'], time() + 3600000);
        //include_once('Scripts/user.class.php');
        //$user = User::getInstance();
        //$user->setLocation($user_data);
        login($user_data);
        die("200");
    }
    else {
        $user_query = "SELECT password FROM user WHERE email = :email;";
        $user_query = Registry::get('db')->prepare($user_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $user_query->execute(array(":email" => $_POST['email']));
        $user_data = $user_query->fetchColumn();
        die('<p style="background-color:red;">' . Registry::get('system')->encrypt($_POST['password']) . ' <=> Your Email or Password is invalid</p>');
    }
}
Registry::get('system')->getGlobalMeta();
if (isset($_COOKIE['id'])) {
    header("location: home");
}
// $fb_id = Base::$FB->getUser();

if ($fb_id) {
    if (isset($_GET['code'])) {
        $user_profile = Base::$FB->api('/me');
        $user_info = array(
            'firstname' => $user_profile['first_name'],
            'lastname' => $user_profile['last_name'],
            'gender' => $user_profile['gender'],
            'email' => $user_profile['email'],
            'dob' => date("Y-m-d H:i:s", strtotime($user_profile['birthday'])),
            'fb_id' => $fb_id
        );
        login(Registry::get('user')->create($user_info));
        header("location: home");
    }
    else {
        $sql = "SELECT id FROM user WHERE fb_id=:fb_id;";
        $sql = Registry::get('db')->prepare($sql);
        $sql->execute(array(
            ":fb_id" => $fb_id
        ));
        login($sql->fetchColumn());
    }
}
else {
//     $loginUrl = Base::$FB->getLoginUrl(); //USER HAS NOT LINKED FB
}
?>

<html>
    <head>
        <?php
        Registry::get('system')->jsVars();
        // $sql = "SELECT * FROM user;";
//         $sql = Registry::get('db')->prepare($sql);
//         $sql->execute();
//         $users = $sql->fetchAll(PDO::FETCH_ASSOC);
//         foreach($users as $user) {
//         	echo $user['name']." => ".Registry::get('system')->encrypt($user['password']);
//         }
        ?>
        <script src='Scripts/external/jquery-1.10.2.min.js'></script>
        <script src='Scripts/js.js'></script>
        <script src="//cdn.jsdelivr.net/jquery.mcustomscrollbar/2.8.1/jquery.mCustomScrollbar.min.js"></script>
        <script>window.mCustomScrollbar || document.write('<script src="Scripts/external/jquery.mCustomScrollbar.min.js">\x3C/script>');</script>
        <link href="Scripts/external/jquery.mCustomScrollbar.min.css" rel="stylesheet" type="text/css" />
        <title>Login</title>
        <script>
            function signUp() {
                var error = false;
                var firstname = $('.first_name_signup').val();
                if (firstname === "" || firstname === "undefined" || typeof firstname === "undefined" || (/^[a-zA-Z0-9- ]*$/.test(firstname) === false)) {
                    pulsate($('.first_name_signup'), 300, "red");
                    error = true;
                }
                var lastname = $('.last_name_signup').val();
                if (lastname === "" || lastname === "undefined" || typeof lastname === "undefined" || (/^[a-zA-Z0-9- ]*$/.test(lastname) === false)) {
                    pulsate($('.last_name_signup'), 300, "red");
                    error = true;
                }
                var email = $('.email_signup').val();
                if (email === "" || email === "undefined" || typeof email === "undefined") {
                    pulsate($('.email_signup'), 300, "red");
                    error = true;
                }
                var password = $('.password_signup').val();
                if (password === "" || password === "undefined" || typeof password === "undefined") {
                    pulsate($('.password_signup'), 300, "red");
                    error = true;
                }
//                var community_id = $('.default_dropdown_selector[wrapper_id="organization_choose"]').attr('value');
//                if (community_id === "" || community_id === "undefined" || typeof community_id === "undefined") {
//                    pulsate($('.default_dropdown_selector[wrapper_id="organization_choose"]'), 300, "red");
//                    error = true;
//                }
//                var position = $('.default_dropdown_selector[wrapper_id="position_choose"]').attr('value');
//                if (position === "" || position === "undefined" || typeof position === "undefined") {
//                    pulsate($('.default_dropdown_selector[wrapper_id="position_choose"]'), 300, "red");
//                    error = true;
//                }
                var gender = $('.default_dropdown_selector[wrapper_id="gender_choose"]').attr('value');
                if (gender === "" || gender === "undefined" || typeof gender === "undefined") {
                    pulsate($('.default_dropdown_selector[wrapper_id="gender_choose"]'), 300, "red");
                    error = true;
                }
                if (error === false) {
                    Application.prototype.UI.modal($('body'), properties = {text: "Signing up..."});
                    $.post('Scripts/verifysignup.php', {
                        firstname: firstname,
                        lastname: lastname,
                        email: email,
                        password: password,
                        position: position,
                        gender: gender
                    }, function(response) {
                        if (response == "") {
                            window.location.replace('home');
                        }
                    });
                }
            }
            function logIn() {
                Application.prototype.UI.modal($('body'),
                        properties = {
                            centered: true,
                            type: "",
                            text: "Logging in..."
                        });
                var error = false;
                var email = $('.email_login').val();
                if (email === "" || email === "undefined" || typeof email === "undefined") {
                    pulsate($('.email_login'), 300, "red");
                    error = true;
                }
                var password = $('.password_login').val();
                if (password === "" || password === "undefined" || typeof password === "undefined") {
                    pulsate($('.password_login'), 300, "red");
                    error = true;
                }
                if (error === false) {
                    $.post(window.location, {email: email, password: password}, function(response) {
                        if (response === "200") {
                            window.location.replace('home');
                        }
                        else {
                            Application.prototype.UI.modal($('body'),
                                    properties = {
                                        centered: true,
                                        type: "error",
                                        text: "Incorrect Password or Email<br /><a style='font-weight:normal;' class='user_preview_name' href='accountrecovery'>Forgotten password?</a>"
                                    });
                            setTimeout(Application.prototype.UI.removeModal, 2000);
                        }
                    });
                }
                else {
                    Application.prototype.UI.removeModal("force");
                }
            }
            $(function() {
                $('.email_login, .password_login').on('keypress', function(event) {
                    if (event.keyCode == 13) {
                        logIn();
                    }
                });
            });
<?php
if (isset($_GET['m'])) {
    showToast('Text');
}
?>
            function showToast(toast) {
                alert(toast);
                //Android.showToast(toast);
            }
        </script>
    </head>
    <body class="login">
<?php include_once('welcome.php'); ?>
        <!--         <div id="header-featured"></div> -->

        <section class='layer'>
            <div>
                <div>
                    <div class="column"> <span class="fa fa-cogs"></span>
                        <div class="title">
                            <h3>Maecenas lectus sapien</h3>
                        </div>
                        <p>In posuere eleifend odio. Quisque semper augue mattis wisi. Pellentesque viverra vulputate enim. Aliquam erat volutpat.</p>
                    </div>
                    <div class="column"> <span class="fa fa-legal"></span>
                        <div class="title">
                            <h3>Praesent scelerisque</h3>
                        </div>
                        <p>In posuere eleifend odio. Quisque semper augue mattis wisi. Pellentesque viverra vulputate enim. Aliquam erat volutpat.</p>
                    </div>
                    <div class="column double_column"> <span class="fa fa-cogs"></span>
                        <div class="signup_container">
                            <div class='title'><h3>Signup</h3></div>
                            <div class="signupbox">
                                <table border="0" style='width:100%'>
                                    <tr>
                                        <td><input type="text" class="first_name_signup" spellcheck="false" placeholder="First Name"autocomplete="off"/>
                                        </td><td><input type="text" class="last_name_signup" placeholder="Last Name"autocomplete="off"/></td>
                                    </tr>
                                    <tr>
                                        <td colspan="2"><input class="password_signup" spellcheck="false" type="password" style="width:100%;" placeholder="Password" autocomplete="off"/></td>
                                    </tr>
                                    <tr>
                                        <td colspan="2"><input class="email_signup" spellcheck="false" type="text" style="width:100%;" autocomplete="off" placeholder="Email"/></td>
                                    </tr>

                                    <!-- 
<tr>
                                            <td colspan="2">
                                                    <select class='dropdown' data-wrapper_id='gender_choose'>
                                                            <option selected>
                                                                    Gender
                                                            </option>
                                                            <option value='male'>
                                                                    Male
                                                            </option>
                                                            <option value='female'>
                                                                    Female
                                                            </option>
                                                    </select>
                                            </td>
                                    </tr>
                                    -->
                                    <tr>
                                        <td colspan="2">
                                            <button id="signup" onclick="signUp();" class="pure-button-green large">Sign Up</button>
                                            <a class='no-ajax' href='<?php echo Base::$FB_LOGIN; ?>'>
                                                <button class="pure-button-green large">Facebook Sign Up</button>
                                            </a>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                    </div>
                </div>
        </section>
        <section class='layer'>
            <div>
                <div>
                    <div class="column"> <span class="fa fa-cogs"></span>
                        <div class="title">
                            <h3>Maecenas lectus sapien</h3>
                        </div>
                        <p>In posuere eleifend odio. Quisque semper augue mattis wisi. Pellentesque viverra vulputate enim. Aliquam erat volutpat.</p>
                    </div>
                    <div class="column"> <span class="fa fa-legal"></span>
                        <div class="title">
                            <h3>Praesent scelerisque</h3>
                        </div>
                        <p>In posuere eleifend odio. Quisque semper augue mattis wisi. Pellentesque viverra vulputate enim. Aliquam erat volutpat.</p>
                    </div>
                    <div class="column"> <span class="fa fa-unlock"></span>
                        <div class="title">
                            <h3>Fusce ultrices fringilla</h3>
                        </div>
                        <p>In posuere eleifend odio. Quisque semper augue mattis wisi. Pellentesque viverra vulputate enim. Aliquam erat volutpat.</p>
                    </div>
                    <div class="column"> <span class="fa fa-wrench"></span>
                        <div class="title">
                            <h3>Etiam posuere augue</h3>
                        </div>
                        <p>In posuere eleifend odio. Quisque semper augue mattis wisi. Pellentesque viverra vulputate enim. Aliquam erat volutpat.</p>
                    </div>
                </div>
            </div>
        </section>
        <section class='layer'>
            <div>
                <div>
                    <div class="column"> <span class="fa fa-cogs"></span>
                        <div class="title">
                            <h3>Maecenas lectus sapien</h3>
                        </div>
                        <p>In posuere eleifend odio. Quisque semper augue mattis wisi. Pellentesque viverra vulputate enim. Aliquam erat volutpat.</p>
                    </div>
                    <div class="column"> <span class="fa fa-legal"></span>
                        <div class="title">
                            <h3>Praesent scelerisque</h3>
                        </div>
                        <p>In posuere eleifend odio. Quisque semper augue mattis wisi. Pellentesque viverra vulputate enim. Aliquam erat volutpat.</p>
                    </div>
                    <div class="column"> <span class="fa fa-unlock"></span>
                        <div class="title">
                            <h3>Fusce ultrices fringilla</h3>
                        </div>
                        <p>In posuere eleifend odio. Quisque semper augue mattis wisi. Pellentesque viverra vulputate enim. Aliquam erat volutpat.</p>
                    </div>
                    <div class="column"> <span class="fa fa-wrench"></span>
                        <div class="title">
                            <h3>Etiam posuere augue</h3>
                        </div>
                        <p>In posuere eleifend odio. Quisque semper augue mattis wisi. Pellentesque viverra vulputate enim. Aliquam erat volutpat.</p>
                    </div>
                </div>
            </div>
        </section>
        <section class='layer'>
            <div>
                <div>
                    <div>
                        <div style='float:left;margin-right:20px;'>
                            <a href="http://stackoverflow.com/users/2506225/patrick-geyer">
                                <img src="http://stackoverflow.com/users/flair/2506225.png?theme=clean" width="208" height="58" alt="profile for Patrick Geyer at Stack Overflow, Q&A for professional and enthusiast programmers" title="profile for Patrick Geyer at Stack Overflow, Q&A for professional and enthusiast programmers">
                            </a>
                        </div>
                        <span>Warning: This site is in development. I will not be held liable for any damages you may incur on this site. By signing up, you agree to these terms. Although you are welcome to sign-up, Registry schools and store your files here for testing purposes, I cannot guarantee the safety/availability of any of your data yet. Release date: 2014, June 21. You can watch the site develop everyday.</span>
                    </div>
                    <div class="links" style='display:none;'>
                        <a id="schoollink" href="login" style="text-decoration:none; font-size:0.8em;">Registry a User</a>/
                        <a id="schoollink" href="login?action=school" style="text-decoration:none; font-size:0.8em;">Registry a School + User</a>/
                        <a id="schoollink" href="about" style="text-decoration:none; font-size:0.8em;">About</a><br/>
                        <span>Suggestions/bugs? Email me at patrick.geyer1@gmail.com</span>
                    </div>
                </div>
            </div>
        </div>

</body>
</html>
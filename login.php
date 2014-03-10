<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include_once 'Scripts/declare.php';
    $user_query = "SELECT id FROM user WHERE email = :email AND password = :password;";
    $user_query = $database_connection->prepare($user_query);
    $user_query->execute(array(":email" => $_POST['email'], ":password" => $system->encrypt($_POST['password'])));
    $user_data = $user_query->fetchColumn();
    //die("ID:".$user_data. "EMAIL:".$_POST['email'] ."ENCRYOT:".$system->encrypt($_POST['password']));

    if (!empty($user_data)) {
        setcookie("id", base64_encode($user_data), time() + 3600000);
        setcookie("chat_feed", 'y', time() + 3600000);
        include_once('Scripts/user.class.php');
        $user = User::getInstance();
        //$user->setLocation($user_data);
        die("200");
    }
    else {
        $user_query = "SELECT password FROM user WHERE email = :email;";
        $user_query = $database_connection->prepare($user_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $user_query->execute(array(":email" => $_POST['email']));
        $user_data = $user_query->fetchColumn();
        die('<p style="background-color:red;">' . $system->encrypt($_POST['password']) . ' <=> Your Email or Password is invalid</p>');
    }
}

include_once("Scripts/config.php");
require_once("../Global_Tools/facebook-php-sdk-master/facebook.php");
//include_once("Scripts/demo.php");
include_once("Scripts/system.class.php");
//include_once("Scripts/js.php");
$allschools = "SELECT name, id FROM community;";
$allschools = $database_connection->prepare($allschools, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
$allschools->execute();
$system = System::getInstance();
$system->getGlobalMeta();
if (isset($_COOKIE['id'])) {
    header("location: home");
}
$config = array(
    'appId' => '219388501582266',
    'secret' => 'c1684eed82295d4f1683367dd8c9a849',
    'fileUpload' => false, // optional
    'allowSignedRequest' => false, // optional, but should be set to false for non-canvas apps
);

$facebook = new Facebook($config);
?>

<html>
    <head>
        <?php
        $system->jsVars();
        // $sql = "SELECT * FROM user;";
//         $sql = $database_connection->prepare($sql);
//         $sql->execute();
//         $users = $sql->fetchAll(PDO::FETCH_ASSOC);
//         foreach($users as $user) {
//         	echo $user['name']." => ".$system->encrypt($user['password']);
//         }
        ?>
        <script src='Scripts/external/jquery-1.10.2.min.js'></script>
        <script src='Scripts/js.js'></script>
        <script src="//cdn.jsdelivr.net/jquery.mcustomscrollbar/2.8.1/jquery.mCustomScrollbar.min.js"></script>
        <script>window.mCustomScrollbar || document.write('<script src="Scripts/external/jquery.mCustomScrollbar.min.js">\x3C/script>');</script>
        <link href="Scripts/external/jquery.mCustomScrollbar.min.css" rel="stylesheet" type="text/css" />
        <script src="Scripts/eventhandlers.js"></script>
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
                var community_id = $('.default_dropdown_selector[wrapper_id="organization_choose"]').attr('value');
                if (community_id === "" || community_id === "undefined" || typeof community_id === "undefined") {
                    pulsate($('.default_dropdown_selector[wrapper_id="organization_choose"]'), 300, "red");
                    error = true;
                }
                var position = $('.default_dropdown_selector[wrapper_id="position_choose"]').attr('value');
                if (position === "" || position === "undefined" || typeof position === "undefined") {
                    pulsate($('.default_dropdown_selector[wrapper_id="position_choose"]'), 300, "red");
                    error = true;
                }
                var gender = $('.default_dropdown_selector[wrapper_id="gender_choose"]').attr('value');
                if (gender === "" || gender === "undefined" || typeof gender === "undefined") {
                    pulsate($('.default_dropdown_selector[wrapper_id="gender_choose"]'), 300, "red");
                    error = true;
                }
                if (error === false) {
                    modal($('body'), properties = {text: "Signing up..."});
                    $.post('Scripts/verifysignup.php', {
                        firstname: firstname,
                        lastname: lastname,
                        email: email,
                        password: password,
                        community_id: community_id,
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
                modal($('body'),
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
                            modal($('body'),
                                    properties = {
                                        centered: true,
                                        type: "error",
                                        text: "Incorrect Password or Email<br /><a style='font-weight:normal;' class='user_preview_name' href='accountrecovery'>Forgotten password?</a>"
                                    });
                            setTimeout(removeModal, 2000);
                        }
                    });
                }
                else {
                    removeModal("force");
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
            if(isset($_GET['m'])) {
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
        <?php //if (isset($_GET['m'])) die('WElcome to mobile site!');  ?>
        <div class="login_container">
            <div class="loginbox">

                <input type="text" spellcheck="false" placeholder="Email" autocomplete="off" tabindex="1" class='email_login'/>
                <input type="password" spellcheck="false" tabindex="2" placeholder="Password" autocomplete="off" class='password_login'/>
                <button onclick='logIn();' class='pure-button-secondary small'>Login</button>
                <a href='signup?m'><button class='pure-button-neutral small signup_button'>Signup</button><a/>

            </div>
        </div>
        <div class='login_background'>
            <p class=''><span class='blue_thick'>DO IT</span><span class='grey_thin'>WITH COLLABORATOR</span></p>
            <hr style="border:0px;border-bottom: 1px dotted lightgrey;">
            <p class=''><span class='blue_thick'>5GB+</span><span class='grey_thin'>FREE SPACE</span></p>
            <p class=''><span class='blue_thick'>SHARE</span><span class='grey_thin'>YOUR FILES</span></p>
            <p class=''><span class='blue_thick'>COLLABORATE</span><span class='grey_thin'></span></p>
            <p class=''><span class='blue_thick'>INSTANT</span><span class='grey_thin'>TEAMWORK</span></p>
        </div>
        <div class="signup">
            <div class="signup_container">
                <h1 class="signupheader">Join</h1>
                <div class="signupbox">
                    <table border="0">
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
                        <tr>
                            <td colspan="2">
                                <div tabindex="0" wrapper_id='organization_choose' class='dropdown_login default_dropdown_selector'>
                                    <span class='default_dropdown_preview'>Choose School</span>
                                    <div id='organization_choose' class='scroll_thin default_dropdown_wrapper'>
                                        <ul class='default_dropdown_menu'>
                                            <?php
                                            foreach ($allschools->fetchAll(PDO::FETCH_ASSOC) as $schools) {
                                                echo "<li value='" . $schools['id'] . "' class='default_dropdown_item'>";
                                                echo $schools['name'];
                                                echo "</li>";
                                            }
                                            ?>
                                        </ul>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <div tabindex="0" wrapper_id='position_choose' class='dropdown_login default_dropdown_selector'>
                                    <span class='default_dropdown_preview'>Select Year</span>
                                    <div id='position_choose' class='scroll_thin default_dropdown_wrapper'>
                                        <ul class='default_dropdown_menu'>
                                            <?php
                                            $i = 7;
                                            while ($i < 15) {
                                                echo "<li value='" . $i . "' class='default_dropdown_item'>Year " . $i . "</li>";
                                                $i++;
                                            }
                                            ?>
                                        </ul>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <div tabindex="0" wrapper_id='gender_choose' class='dropdown_login default_dropdown_selector'>
                                    <span class='default_dropdown_preview'>Gender</span>
                                    <div id='gender_choose' class='scroll_thin default_dropdown_wrapper'>
                                        <ul class='default_dropdown_menu'>
                                            <li value='Male' class='default_dropdown_item'>Male</li>
                                            <li value='Female' class='default_dropdown_item'>Female</li>
                                        </ul>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <button id="signup" onclick="signUp();" class="pure-button-success small">Sign Up</button>
                            </td>
                        </tr>
                    </table>
                </div>
                <?php
                if (isset($_GET['action'])) {
                    echo '  <h1 class="signupheader">Register a School</h1>
			<form id="school" action="Scripts/verifysignup.php" method="POST">
			<div class="signupbox">
			<table border="0">
			<tr>
			<td colspan="2"><input type="text" style="width:100%;" autocomplete="off" placeholder="School Name (e.g. Clifford School)" name="school"/></td>
			</tr>
			<tr>
			<td><input type="text" placeholder="First Name"autocomplete="off" name="firstname"/></td><td><input type="text" placeholder="Last Name"autocomplete="off" name="lastname"/></td>
			</tr>
			<tr>
			<td colspan="2"><input type="password" style="width:100%;" placeholder="Password" autocomplete="off" name="newpassword"/></td>
			</tr>
			<tr>
			<td colspan="2"><input type="text" style="width:100%;" autocomplete="off" placeholder="Email" name="email"/></td>
			</tr>
			<tr>
			<td><label>Select Year:</label></td><td><div class="styled-select"><select style="width:100%;" name="year"> <option>1</option><option>2</option><option>3</option>
			<option>4</option><option>5</option><option>6</option><option>7</option><option>10</option><option>8</option><option>9</option><option>10</option>
			<option>11</option><option>12</option><option>13</option><option>14</option></select></div></td>
			</tr>
			<tr>
			<td><div class="styled-select"><select style="width:100%;" name="gender"><option>Male</option><option>Female</option></select></div></td>
			</tr>
			<tr>
			<td colspan="2"><input type="submit" value="Register User and School"></input></td>
			</tr>
			<tr>
			<td colspan="2"><label>*When you register a school <br>you will automatically be<br> appointed admin.</label></td>
			</tr>
			</table>
			</div>
			</form>';
                }
                ?>
            </div>
            <div class='bottom_bar'>
                <div style='float:left;margin-right:20px;'>
                    <a href="http://stackoverflow.com/users/2506225/patrick-geyer">
                        <img src="http://stackoverflow.com/users/flair/2506225.png?theme=clean" width="208" height="58" alt="profile for Patrick Geyer at Stack Overflow, Q&A for professional and enthusiast programmers" title="profile for Patrick Geyer at Stack Overflow, Q&A for professional and enthusiast programmers">
                    </a>
                </div>
                <span>Warning: This site is in development. I will not be held liable for any damages you may incur on this site. By signing up, you agree to these terms. Although you are welcome to sign-up, register schools and store your files here for testing purposes, I cannot guarantee the safety/availability of any of your data yet. Release date: 2014, June 21. You can watch the site develop everyday.</span>
            </div>
            <div class="links" style='display:none;'>
                <a id="schoollink" href="login" style="text-decoration:none; font-size:0.8em;">Register a User</a>/
                <a id="schoollink" href="login?action=school" style="text-decoration:none; font-size:0.8em;">Register a School + User</a>/
                <a id="schoollink" href="about" style="text-decoration:none; font-size:0.8em;">About</a><br/>
                <span>Suggestions/bugs? Email me at patrick.geyer1@gmail.com</span>
            </div>
        </div>
    </body>
</html>
<?php
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
        </script>
    </head>
    <body class="signup">
        <div class="signup">
            <h1 class="signupheader">Join</h1>
            
            <div class="signup_container">
                
                <div class="signupbox">
                    <input type="text" class="first_name_signup" spellcheck="false" placeholder="First Name"autocomplete="off"/>
                    <input type="text" class="last_name_signup" placeholder="Last Name" autocomplete="off"/>

                    <input class="password_signup" spellcheck="false" type="password" placeholder="Password" autocomplete="off"/>
                    <input class="email_signup" spellcheck="false" type="text" autocomplete="off" placeholder="Email"/>

                    <div tabindex="0" wrapper_id='gender_choose' class='dropdown_login default_dropdown_selector'>
                        <span class='default_dropdown_preview'>Gender</span>
                        <div id='gender_choose' class='scroll_thin default_dropdown_wrapper'>
                            <ul class='default_dropdown_menu'>
                                <li value='Male' class='default_dropdown_item'>Male</li>
                                <li value='Female' class='default_dropdown_item'>Female</li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <button id="signup" onclick="signUp();" class="pure-button-success small">Sign Up</button>
            </div>
            <a href='login'><button class="pure-button-neutral small">Back to Login</button></a>
        </div>
    </body>
</html>
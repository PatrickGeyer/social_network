<?php
include_once('welcome.php');
include_once('chat.php');
include_once('friends_list.php');
if(isset($_GET['ci'])) {
    $collaboration_id = $_GET['ci'];
}
else {
    $collaboration_id = 0;
}
?>
<html>
    <head>
        <link rel="stylesheet" type="text/css" href="Scripts/external/jQuery-TE_v.1.4.0/jquery-te-1.4.0.css" />
        <script src='Scripts/external/jQuery-TE_v.1.4.0/jquery-te-1.4.0.min.js'></script>
        <script>
            //setInterval(function(){refreshCollaboration();}, 2000);
            $(window).keypress(function(event) {
                if (!(event.which == 115 && event.ctrlKey) && !(event.which == 19)) return true;
                alert("Ctrl-S pressed");
                event.preventDefault();
                return false;
            });
            $(function() {
                $('#editor').jqte();
            });
            function refreshCollaboration()
            {
                $.post('Scripts/collaborator.class.php', {action: 'getSrc', id: <?php echo $collaboration_id; ?>}, function(response)
                {
                    //alert(response);
                    $('.collaborator').html(response);
                });
            }
            $('#about_edit_show').blur(function()
            {
                submitData();
            });
            function submitData()
            {
                var about = $('#about_edit_show').html();
                var email = '';
                var school = '';
                var year = '';
                $.post('Scripts/user.class.php', {about: about, email: email, year: year}, function(response)
                {
                    $('#about_saved').fadeIn(function()
                    {
                        $('#about_saved').fadeOut(1000);
                    });

                    //alert(response);
                });
            }
        </script>
    </head>
    <body>
        <div class='container'>
            <span class='thin_blue_header'><?php //echo $collaborator->getName($collaboration_id)  ?></span><br><br><br>
            <textarea id='editor' class='collaborator' contenteditable>Loading...</textarea>
        </div>
    </body>
</html>
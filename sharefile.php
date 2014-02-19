<?php
include_once('welcome.php');
include_once('chat.php');
$file_name = $_GET['filename'];
$file_id = $_GET['file_id'];
?>
<head>
    <title><?php echo $file_name; ?></title>
    <link rel="stylesheet" type="text/css" href="CSS/message.css">
    <script>
        function getnamesgroup()
        {
            var value = $('#names_value').val();
            $.post("Scripts/searchbar.php", {search: "share", input_text: value}, function(data) {
                $('#names').empty();
                $('#names').append(data);
            });
        }

        $(document).click(function(e)
        {
            $("#names").hide();
        });	// ***ADD*** if element is input do nothing!
        $(document).keypress(function(e)
        {
            if (e.which == 13)
            {
                $('#match').click();
            }
        });

        function share()
        {
            var comment = $('#comment').val();
            var file_id = "<?php echo $file_id; ?>";
            var name = "<?php echo $file_name; ?>";
            $.post("Scripts/share_file.php", {file_id: file_id, file_name: name, comment: comment, receivers: receivers}, function(response)
            {
                var status = response.split("/");
                alert(response);
            });
        }
    </script>
</head>
<div style="padding-top:50px;" class="container">
    <p><em><strong>Share </strong><?php echo $file_name; ?></em></p>
    <div style="margin-left:50px;">
        <input id="names_value" autocomplete='off' style="width:220px;" onkeyup='getnamesgroup();' type='text' placeholder="With..." class='names_input' />
        <div hidden style='overflow:auto;max-height:200; position:absolute; 
             min-width:350px;padding:2px;border: 1px solid lightgrey; background-color:white;' id='names'></div>
        <hr>
        <textarea style="width:100%;" class="thin" id="comment" placeholder="Comment..."></textarea>
        <hr>
        <center>
            <button onclick="share();">Share</button>
        </center>
    </div>
</div>
<?php

function print_body() { ?>
    <div class="container noRightBar">
        <div class='contentblock'>
            <h2>Developer API</h2>
            <h4>How to use?</h4>
            <p>Bla Bla bla...</p>
        </div>
    </div>
    <script>
        Application.prototype.UI.prop.fileUpload = false;
        Application.prototype.UI.prop.connectionList = false;
        Application.prototype.UI.prop.chat = false;
        var nav = new Application.prototype.UI.vNav({container: $('.left_bar_container')});
        nav.addOptions([
            {
                text: "How to use",
                element: $("#how_to_use")
            },
            {
                text: "Second",
                element: ""
            }
        ]);
        document.title = 'Developer API - Social Network';
    </script>
    <?php
}

require_once($_SERVER['DOCUMENT_ROOT'] . '/Scripts/lock.php');
?>
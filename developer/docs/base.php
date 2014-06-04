<?php

function print_body() { 
    $tab = (isset($_GET['tab']) ? $_GET['tab'] : 'intro'); ?>
    <div class="container noRightBar">
        <!--<div class='contentblock hNav'></div>-->
        <div class='contentblock'>
            <?php if($tab === 'when_to') { ?>
                <h2>Developer API - When to use</h2>
                <p>Bla Bla bla...</p>
            <?php } else { ?> 
                <h2>Developer API - Introduction</h2>
                <p>Here you will find a menu listing the available API at the time.</p>
            <?php } ?>
        </div>
    </div>
    <script>
//        var page_contents = new Application.prototype.UI.ButtonSwitch({container:$(".hNav")});
//            page_contents.addOptions([
//                {
//                    text: "Introduction",
//                    href: "?tab=intro",
//                    selected: <?php echo ($tab === 'intro' ? 'true' : 'false'); ?>
//                },
//            ]);
        document.title = 'Developer API - Social Network';
    </script>
    <?php
    require_once $_SERVER['DOCUMENT_ROOT'].'/developer/vnav.php';
}

require_once($_SERVER['DOCUMENT_ROOT'] . '/Scripts/lock.php');
?>
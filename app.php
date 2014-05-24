<?php

function print_body() { ?>
    <div class="container">
        <div class='contentblock'>
            <?php if (isset($_GET['id'])) { ?>
            <div class='app' data-game_id='<?php echo $_GET['id']; ?>'></div>
                <script>
                    var options = <?php echo json_encode(Registry::get('app')->get($_GET['id'])); ?>;
                    var app = new Application.prototype.App(options);
                    app.print();
                    document.title = app.attr.name;
                </script>
            <?php } ?>
        </div>
    </div>
    <?php
}
require_once($_SERVER['DOCUMENT_ROOT'] . '/Scripts/lock.php');
?>
<?php

function print_body() { ?>
    <div class="container noRightBar">
        <div class='contentblock'>
            <h2>Game API</h2>
            <h3>App.game Object</h3>
            <p>
                As soon as your Application initiates the <code>App</code> object will be available.
                <br>
                An attribute to this object is the <code>game</code> object that lies within it.
            <div>
                <code>App.game.getHighscore();</code>
            </div>
            </p>
        </div>
    </div>
    <script>
        document.title = 'Game API - Social Network';
    </script>
    <?php
    require_once $_SERVER['DOCUMENT_ROOT'] . '/developer/vnav.php';
}

require_once($_SERVER['DOCUMENT_ROOT'] . '/Scripts/lock.php');
?>
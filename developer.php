<?php

function print_body() { ?>
    <div class="container">
        <div class='contentblock'>
            <?php if (isset($_GET['create'])) { ?>
                <h2>Create</h2>
                <section class='layer'>
                    <div>
                        <div>
                            <div class="column">
                                <div class="title">
                                    <h3>App Name</h3>
                                </div>
                                <input type="text" placeholder="App Name"/>
                                <div class="title">
                                    <h3>App Type</h3>
                                </div>
                                <select class='dropdown'>
                                    <option value='0'>Game</option>
                                </select>
                                <div class="title"> </div>
                                <button class='pure-button-blue large'>Create</button>
                            </div>
                        </div>
                    </div>
                </section>
            <?php
            }
            else {
                ?>
                <h2>Developer</h2>
                <section class='layer'>
                    <div>
                        <div>
                            <a href="developer?create">
                                <div class="column"> <span class="fa fa-legal"></span>
                                    <div class="title">
                                        <h3>Create</h3>
                                    </div>
                                    <p>Create or register awesome apps here.</p>
                                </div>
                            </a>
                        </div>
                    </div>
                </section>
                <section class='layer'>
                    <div>
                        <div>
                            <a href="developer?view">
                                <div class="column"> <span class="fa fa-cogs"></span>
                                    <div class="title">
                                        <h3>Dashboard</h3>
                                    </div>
                                    <p>Here you can modify existing application settings.</p>
                                </div>
                            </a>
                        </div>
                    </div>
                </section>
    <?php } ?>
        </div>

    </div>
    <script>
        document.title = 'Developer';
    </script>
    <?php
}

$min_activity_id = $user_id = $group_id = $filter = NULL;
if (isset($_GET['min_activity_id'])) {
    $min_activity_id = $_GET['min_activity_id'];
}
if (isset($_GET['fg'])) {
    $group_id = $feed_id = $_GET['fg'];
}
else if (isset($_GET['f'])) {
    if ($_GET['f'] == 'a') {
        $filter = $feed_id = 'a';
    }
    else {
        $filter = $feed_id = $_GET['f'];
    }
}
else if (isset($_GET['u'])) {
    $user_id = $_GET['u'];
    $feed_id = 'u_' . $user_id;
}
else {
    $filter = $feed_id = 'a';
}
require_once('Scripts/lock.php');
?>
<?php

function print_body() { ?>
    <div class="container noRightBar noLeftBar">
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
                            <a class='no-ajax' href="/developer?create">
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
                            <a class='no-ajax' href="/developer/dashboard">
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
                <section class='layer'>
                    <div>
                        <div>
                            <a class='no-ajax' href="/developer/api">
                                <div class="column"> <span class="fa fa-cogs"></span>
                                    <div class="title">
                                        <h3>API</h3>
                                    </div>
                                    <p>An overview of the Social Network API functions.</p>
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

require_once($_SERVER['DOCUMENT_ROOT'].'/Scripts/lock.php');
?>
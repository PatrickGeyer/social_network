<?php

function print_body() { ?>
    <div class="container noRightBar noLeftBar">
        <div class='contentblock'>
            <h2>Developer</h2>
            <section class='layer'>
                <div>
                    <div>
                        <a class='no-ajax createApp' href="/developer/app/create">
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
        </div>

    </div>
    <script>
        document.title = 'Developer';
        $(document).on('click', 'a.createApp', function(e) {
            e.preventDefault();
            e.stopPropagation();
            var dialog = new Application.prototype.UI.Dialog({title: "Create a new app", subheader: "Get started integrating our service into your app or website", hover: true});
            dialog.addButton({text: "Create", onclick: function() {
                    alert('Creating');
                }});
            var content = $("<div></div>");
            content.append("<span class='label'>App Name</span>").append('<input type="text" placeholder="App Name"/>');
            content.append("<span class='label'>Choose Category</span>");
            var dropdown = new Application.prototype.UI.Dropdown({
                name: "Choose Category"
            });
            dropdown.addOptions(
                    [
                        {
                            text: "Games",
                            value: 0
                        },
                        {
                            text: "Entertainment",
                            value: 1
                        }
                    ]
                    );
            content.append(dropdown.print());
            dialog.content(content);
            dialog.show();
        });
    </script>
    <?php
}

require_once($_SERVER['DOCUMENT_ROOT'] . '/Scripts/lock.php');
?>
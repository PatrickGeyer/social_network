<?php

function print_body() { ?>
    <div class="container noRightBar noLeftBar">
        <div class='contentblock'>
            <h2>Create a new Application</h2>
            <section class='layer'>
                <div>
                    <div>
                        <div class="column">
                            <div class="title">
                                <h3>App Name</h3>
                                <input type="text" placeholder="App Name"/>
                            </div>
                            <div class="title">
                                <h3>App Type</h3>
                                <select data-text='Choose a Category' class='dropdown'>
                                    <option value='0'>Games</option>
                                    <option value='1'>Books</option>
                                    <option value='2'>Business</option>
                                    <option value='3'>Communication</option>
                                    <option value='4'>Education</option>
                                    <option value='5'>Entertainment</option>
                                    <option value='6'>Fashion</option>
                                    <option value='7'>Finance</option>
                                    <option value='8'>Food & Drink</option>
                                    <option value='9'>Health & Fitness</option>
                                </select>
                            </div>

                            <div class="title">
                                <label>By creating an Application you agree to the <a href=''>Terms and Conditions</a></label>
                            </div>
                            <button class='pure-button-blue large createApp'>Create</button>

                        </div>
                    </div>
                </div>
            </section>
        </div>
        <script>
            document.title = 'Create App';
        </script>
    </div>
    <?php
}

require_once($_SERVER['DOCUMENT_ROOT'] . '/Scripts/lock.php');
?>
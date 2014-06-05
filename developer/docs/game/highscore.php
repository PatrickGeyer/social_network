<?php

function print_body() { ?>
    <div class="container noRightBar">
        <div class='contentblock'>
            <h2>Highscore methods</h2>
            <table class='rows'>
                <tr>
                    <td>Function</td>
                    <td>Description</td>
                </tr>
                <tr>
                    <td>
                        <pre class="prettyprint"><code>getHighscores(min, max, callback)</code></pre>
                    </td>
                    <td>
                        Returns the top highscores registered by your game using the <code>setHighscore</code> method within the given range as an array:
                        <br />
                        <div>
<pre class="prettyprint">
<code>[{
    user_id: Integer,
    name: String,
    score: Integer
}, ...]</code></pre>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <pre class="prettyprint"><code>getHighscore(callback)</code></pre>
                    </td>
                    <td>
                        Returns the top highscore of the currrent user in the form of a JSON object:
                        <br /><br />
                        <div>
<pre class='prettyprint'>
<code>{
    user_id: Integer,
    name: String,
    score: Integer
}</code></pre>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <code>setHighscore(score, callback)</code>
                    </td>
                    <td>
                        Inserts a record into the current users scoreboard. It does not have to be a 'Highscore', but any score as sorting will occur in MySQL.
                    </td>
                </tr>
            </table>
        </div>
    </div>
    <script>
        document.title = 'Highscore API - Social Network';
    </script>
    <?php
    require_once $_SERVER['DOCUMENT_ROOT'] . '/developer/vnav.php';
}

require_once($_SERVER['DOCUMENT_ROOT'] . '/Scripts/lock.php');
?>
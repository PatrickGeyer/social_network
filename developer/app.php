<?php

function print_body() {
    $app = (int) $_GET['id'];
    $info = Registry::get('app')->getAll($app);
    $tab = (isset($_GET['tab']) && !empty($_GET['tab']) ? $_GET['tab'] : 'dashboard');
    ?>
<?php if($tab === 'dashboard') { ?>
    <div class="container noRightBar">
        <div class="contentblock">
            <table>
                <tr>
                    <td rowspan="2">
                        <img class='profile_picture_thumb' src='<?= $info['pic']['thumb']?>'/>
                    </td>
                    <td>
                        <div class='title'><?= $info['info']['name']?></div>
                        <span class='comment'><?= ($info['mode']['dev'] == 0 ? "This Application is in development" : "This Application is Public") ?></span>
                    </td>
                </tr>
                <tr>
                    <td>
                        <table>
                            <tr>
                                <td>
                                    <div>APP ID: </div>
                                    <code><?= str_pad($info['info']['id'], 8, '0', STR_PAD_LEFT) ?></code>
                                </td>
                                <td>

                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </div>
    </div>
    <script>
        document.title = 'Dashboard - <?= $info['info']['name'] ?>';
    </script>
<?php } ?>
    <?php
}
require_once($_SERVER['DOCUMENT_ROOT'].'/Scripts/lock.php');
?>
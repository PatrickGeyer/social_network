<?php
$REQUEST = (isset($_SERVER['HTTP_X_REQUESTED_WITH']) ? $_SERVER['HTTP_X_REQUESTED_WITH'] : "FIRST");
if ($REQUEST == "FIRST") {
    ?>
    <script>

        var nav = new Application.prototype.UI.vNav({container: $('.left_bar_container'), preset: 'developer'});
        nav.addOptions(
                [{
                        text: 'Available APIs',
                        children:
                                [
                                    {
                                        text: "Game",
                                        href: "/developer/api/game",
                                        children: [
                                            {
                                                text: "Highscores",
                                                href: "/developer/api/game/highscore",
                                            }
                                        ]
                                    },
                                    {
                                        text: "Else",
                                        href: "/developer/api/else"
                                    },
                                ]
                    }]);
    </script>
<?php } ?>
<script>
    Application.prototype.UI.prop.fileUpload = false;
    Application.prototype.UI.prop.connectionList = false;
    Application.prototype.UI.prop.chat = false;
</script>
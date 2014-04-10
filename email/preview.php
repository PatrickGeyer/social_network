<?php
$page = file_get_contents('style.html');
$page .= file_get_contents('header.html');
$page .= file_get_contents('template-widgets.html');
$page .= file_get_contents('footer.html');

echo $page;
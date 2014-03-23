<?php
require_once("../Global_Tools/facebook-php-sdk-master/facebook.php");
class FB extends Facebook {
    
    public function __construct($config) {
        parent::__construct($config);
    }
    
    public function post($attachement) {
        return $this->api('/me/feed', 'POST', $attachment);
    }
}

//$attachment =  array(
// 'message' => 'This is the Message. My Personal Project, posting via Facebook API Test.',
// 'name' => 'Post Title',
// 'link' => 'http://hopto.redirectme.net',
// 'description' => 'My Personal Project, posting via Facebook API Test.',
// 'picture'=> 'http://i.telegraph.co.uk/multimedia/archive/02413/seal-shark-leap_2413378k.jpg'
//);
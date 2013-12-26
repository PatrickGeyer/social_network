<?php
include('database.class.php');

class System extends Database
{
   
	public function __construct()
	{

	}

	function getSchoolNames()
	{
		$query = "SELECT name FROM community;";
	}

	function getSchoolIds()
	{

	}

    const CYPHER = MCRYPT_RIJNDAEL_256;
    const MODE   = MCRYPT_MODE_CBC;
    const KEY    = 'somesecretphrase';

    public function encrypt($plaintext)
    {
        $td = mcrypt_module_open(self::CYPHER, '', self::MODE, '');
        $iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
        mcrypt_generic_init($td, self::KEY, $iv);
        $crypttext = mcrypt_generic($td, $plaintext);
        mcrypt_generic_deinit($td);
        return base64_encode($iv.$crypttext);
    }

    public function decrypt($crypttext)
    {
        $crypttext = base64_decode($crypttext);
        $plaintext = '';
        $td        = mcrypt_module_open(self::CYPHER, '', self::MODE, '');
        $ivsize    = mcrypt_enc_get_iv_size($td);
        $iv        = substr($crypttext, 0, $ivsize);
        $crypttext = substr($crypttext, $ivsize);
        if ($iv)
        {
            mcrypt_generic_init($td, self::KEY, $iv);
            $plaintext = mdecrypt_generic($td, $crypttext);
        }
        return trim($plaintext);
    }
    public function audioPlayer($path = null, $name = null, $close_button = true, $rndm = null)
    {
        if($rndm == "blank") {
            $rndm = ":::uid:::";
        } else if($rndm == NULL) {
            $rndm = rand();
        }

        if($path == null)
        {
            $path = ":::path:::";
            $name = ":::name:::";
        }

            $string = '<div class="audio_container" contenteditable="false" id="audio_container_' . $rndm 
            . '">'.($close_button == true ? "<div onclick=\"removeAudio(" . $rndm . ");\" class=\'audio_remove\'>x</div>" : "").'<audio id="audio_' . $rndm . 
            '" style="display:none;" controls="controls" class="player" preload="none"> <source src="' . $path . '" />Your browser doesnt support the audio element, please download to listen instead...</audio>';

            $string .= '<div contenteditable="false" id="image_' . $rndm . '" onclick="audioPlay(&quot;' . $rndm 
                . '&quot;);" class="audio_button" style="background-image:url(&quot;../Images/play-button.png&quot;);"></div>';

            $string .= '<div id="audio_info_' . $rndm . '" class="audio_info"><div class="audio_title">' . $name . '</div><div id="audio_progress_container_' . $rndm
             . '"class="audio_progress_container"><div id="audio_progress_' . $rndm . '" class="audio_progress"></div><div class="audio_buffered" id="audio_buffered_' . $rndm . 
             '"></div></div><div class="audio_time" id="audio_time_' . $rndm . '">0:00</div></div></div>';
        return $string;
    }
    function trimStr($string, $length)
    {
        $string = (strlen($string) > $length) ? substr($string,0,$length).'...' : $string;
        return $string;
    }
    function humanTiming ($time)
    {
        $time = time() - $time; 

        $tokens = array (
            31536000 => 'year',
            2592000 => 'month',
            604800 => 'week',
            86400 => 'day',
            3600 => 'hour',
            60 => 'minute',
            1 => 'second'
            );

        foreach ($tokens as $unit => $text) 
        {
            if ($time < $unit)
            {
                continue;
            }
            $numberOfUnits = floor($time / $unit);
            if($numberOfUnits < 20 && $text == "second")
            {
                return "Just Now";
            }
            else
            {
                return $numberOfUnits.' '.$text.(($numberOfUnits>1)?'s':'')." ago";
            }
        }
    }
}

?>
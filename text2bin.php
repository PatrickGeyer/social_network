<?php
	$response;
	function text2dec($string)
	{
		$return = "";
		    $tokens = array (
	    	'a' => 97,
	    	'b' => 98,
	    	'c' => 99,
	    	'd' => 100,
	    	'e' => 101,
	    	'f' => 102,
	    	'g' => 103,
	    	'h' => 104,
	    	'i' => 105,
	    	'j' => 106,
	    	'k' => 107,
	    	'l' => 108,
	    	'm' => 109,
	    	'n' => 110,
	    	'o' => 111,
	    	'p' => 112,
	    	'q' => 113,
	    	'r' => 114,
	    	's' => 115,
	    	't' => 116,
	    	'u' => 117,
	    	'v' => 118,
	    	'w' => 119,
	    	'x' => 120,
	    	'y' => 121,
	    	'z' => 122,
	    	'.' => 46,
	    	' ' => 32,
	    	);
	    	$stringLength = strlen($string);
			for ($i = 0; $i < $stringLength; $i++)
			{
   				$char = $string[$i];
			    foreach ($tokens as $letter => $ascii) 
	    		{
	    			if ($char == $letter)
	    			{
	    				$return .= $ascii." ";
	    			}
	    		}
			}
			return $return;
	}
	function ascii2bin($ascii)
	{
		$return = "";
	 	$ascii = explode(" ", $ascii);
		foreach ($ascii as $single_ascii) 
	    {
	    	$single_bin = decbin($single_ascii); 				//Using a DLL that came in the installation of PHP
	      	$single_bin = str_pad($single_bin, 8, 0, STR_PAD_LEFT);
		   	$return .= $single_bin." ";
	    }
	    return $return." ";
	}

	if($_SERVER['REQUEST_METHOD'] == "POST")
	{
		if($_POST['action'] == "bin2text")
		{
			$string = $_POST['bin'];
			$string = pack('H*', base_convert($string ,2, 16));
			die(json_encode($string));
		}
		else
		{
			$string = $_POST['string'];
			$ascii  = text2dec($string);
			$bin    = ascii2bin($ascii);
			die(json_encode(array('ascii' => $ascii, 'bin' => $bin)));
		}
	}
?>
<html>
	<head>
		<script src="https://code.jquery.com/jquery-latest.min.js"></script>
	</head>
	<body>
		<label for="input">Text</label>
		<textarea id="input" onkeyup="updateOutput();"></textarea>
		<label for="output">ASCII</label>
		<textarea id="output"></textarea>
		<label for="outputbin">Binary</label>
		<textarea id="outputbin"></textarea>
		<br />
		<label>Binary</label><textarea id="bin_input" onkeyup="updateBinOutput();"></textarea>
		<label>Text</label><textarea id="text_output"></textarea>
	</body>
	<script>
	function updateOutput()
	{
		var string = $('#input').val();
		$.post('text2bin.php', {action:"else", string: string}, function(response_origional)
		{
			var response = $.parseJSON(response_origional);
			$('#output').val(response['ascii']);
			$('#outputbin').val(response['bin']);
		});
	}
	</script>
	<script>
	function updateBinOutput()
	{
		var bin_input = $('#bin_input').val();
		$.post('text2bin.php', {action:"bin2text", bin: bin_input}).done(function(response)
		{
			$('#text_output').val(response);
		});
	}
	</script>
</html>
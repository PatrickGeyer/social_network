<?php
	if($_SERVER['REQUEST_METHOD'] == "POST")
	{
		$string = $_POST['bin'];
		$string = pack('H*', base_convert($string ,2, 16));
		die(json_encode($string));
	}
?>
<html>
<head>
		<script src="https://code.jquery.com/jquery-latest.min.js"></script>
</head>
<body>
<label>Binary</label><textarea id="bin_input" onkeyup="updateOutput();"></textarea>
<label>Text</label><textarea id="text_output"></textarea>
</body>
<script>
function updateOutput()
{
	var bin_input = $('#bin_input').val();
	$.post('bin2text.php', {bin: bin_input}).done(function(response)
	{
		$('#text_output').val(response);
	});
}
</script>
</html>
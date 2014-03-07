<?php
$page_identifier = "stats";
include_once('welcome.php');
include_once('chat.php');
?>
<html>
    <head>
        <link rel="stylesheet" type="text/css" href="CSS/message.css">
        <script src="Scripts/external/chart.min.js"></script>
        <title>Statistics</title>
        <script>
        $(function(){
        	var ctx = $("#likes_chart").get(0).getContext("2d");
			//This will get the first returned node in the jQuery collection.
			var myNewChart = new Chart(ctx);
			var options = {
				
	//Boolean - If we show the scale above the chart data			
	scaleOverlay : false,
	
	//Boolean - If we want to override with a hard coded scale
	scaleOverride : false,
	
	//** Required if scaleOverride is true **
	//Number - The number of steps in a hard coded scale
	scaleSteps : null,
	//Number - The value jump in the hard coded scale
	scaleStepWidth : null,
	//Number - The scale starting value
	scaleStartValue : null,

	//String - Colour of the scale line	
	scaleLineColor : "rgba(0,0,0,.1)",
	
	//Number - Pixel width of the scale line	
	scaleLineWidth : 1,

	//Boolean - Whether to show labels on the scale	
	scaleShowLabels : true,
	
	//Interpolated JS string - can access value
	scaleLabel : "<%=value%>",
	
	//String - Scale label font declaration for the scale label
	scaleFontFamily : "'Arial'",
	
	//Number - Scale label font size in pixels	
	scaleFontSize : 12,
	
	//String - Scale label font weight style	
	scaleFontStyle : "normal",
	
	//String - Scale label font colour	
	scaleFontColor : "#666",	
	
	///Boolean - Whether grid lines are shown across the chart
	scaleShowGridLines : true,
	
	//String - Colour of the grid lines
	scaleGridLineColor : "rgba(0,0,0,.02)",
	
	//Number - Width of the grid lines
	scaleGridLineWidth : 1,	
	
	//Boolean - Whether the line is curved between points
	bezierCurve : true,
	
	//Boolean - Whether to show a dot for each point
	pointDot : false,
	
	//Number - Radius of each point dot in pixels
	pointDotRadius : 3,
	
	//Number - Pixel width of point dot stroke
	pointDotStrokeWidth : 1,
	
	//Boolean - Whether to show a stroke for datasets
	datasetStroke : true,
	
	//Number - Pixel width of dataset stroke
	datasetStrokeWidth : 1,
	
	//Boolean - Whether to fill the dataset with a colour
	datasetFill : true,
	
	//Boolean - Whether to animate the chart
	animation : true,

	//Number - Number of animation steps
	animationSteps : 6,
	
	//String - Animation easing effect
	animationEasing : "easeOutQuart",

	//Function - Fires when the animation is complete
	onAnimationComplete : null
	
}
var labels = new Array();
labels = <?php echo json_encode(array("January","February","March","April","May","June","July","August","September","October", "November", "December"));?>;
			var data = {
			labels : labels,
				//labels : ["January","February","March","April","May","June","July","August","September","October", "November", "December"],
				datasets : [
					{
						fillColor : "rgba(220,220,220,0.5)",
						strokeColor : "rgba(220,220,220,1)",
						pointColor : "rgba(220,220,220,1)",
						pointStrokeColor : "#fff",
						data : [65,59,70,81,56,55,40]
					},
					{
						fillColor : "rgba(151,187,205,0.5)",
						strokeColor : "rgba(151,187,205,1)",
						pointColor : "rgba(151,187,205,1)",
						pointStrokeColor : "#fff",
						data : [28,48,40,19,6,27,10]
					}
				]
			}
			new Chart(ctx).Line(data,options);
			});
        </script>
    </head>
    <body>
        <div class="global_container">
            <?php include_once('left_bar.php'); ?>
            <div class="container">
                <div class='timestamp'><span>Likes</span></div>
                <canvas id="likes_chart" width="350" height="400"></canvas>
                <div class='timestamp'><span>Comments</span></div>
                <canvas id="likes_chart" width="350" height="400"></canvas>
            </div>
            <?php include_once('right_bar.php'); ?>
        </div>
    </body>
</html>
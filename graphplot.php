<?php
$dataPoints = json_decode(file_get_contents('array1.txt'), true);
$formattedDataPoints = array();
for ($i =0; $i< count($dataPoints);$i++){
	$tempArr = array("x"=> $dataPoints[$i][0], "y"=> $dataPoints[$i][1]);
	array_push($formattedDataPoints, $tempArr);
}
?>
<!DOCTYPE HTML>
<html>
<head>  
<script>
window.onload = function () {
 
var chart = new CanvasJS.Chart("chartContainer", {
	animationEnabled: true,
	exportEnabled: true,
	theme: "light1", 
	title:{
		text: "K means input data"
	},
	axisX:{
		title: "x axis",
		//suffix: " kg"
	},
	axisY:{
		title: "y axis",
		//suffix: " inch",
		//includeZero: false
	},
	data: [{
		type: "scatter",
		markerType: "circle",
		markerSize: 10,
		toolTipContent: "y: {y} <br>x: {x} ",
		dataPoints: <?php echo json_encode($formattedDataPoints, JSON_NUMERIC_CHECK); ?>
	}]
});
chart.render();
}
</script>
</head>
<body>
<div id="chartContainer" style="height: 370px; width: 100%;"></div>
<script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>
</body>
</html>    
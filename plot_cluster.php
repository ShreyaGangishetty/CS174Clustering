<?php
require_once('dbConnection.php');
$formattedDataPoints = plot_kmeans();

function plot_kmeans(){
	$formattedDataPoints = array();
	$testOutputPoints = $_SESSION['kmeansTestResult'];
	for($ind =0; $ind<count($testOutputPoints); $ind++){
			$tempArr = array();
			$cluster = $testOutputPoints[$ind];
			foreach ($cluster as $point) {
				//print_r($point);
				$formattedPoint = array("x" => $point[0], "y" => $point[1]);
				array_push($tempArr, $formattedPoint);
			}
			array_push($formattedDataPoints, $tempArr);
	}
	return $formattedDataPoints;
}

?>

<!DOCTYPE HTML>
<html>
<head>
<script>
window.onload = function () { 
var chart = new CanvasJS.Chart("chartContainer", {
	animationEnabled: true,
	title:{
		text: "K means clusters"
	},
	axisX: {
		title:"X axis"
	},
	axisY:{
		title: "Y axis"
	},
	legend:{
		cursor: "pointer",
		itemclick: toggleDataSeries
	},
	data: [
	{
		type: "scatter",
		toolTipContent: "<span style=\"color:#4F81BC \"><b>{name}</b></span><br/><b> X:</b> {x} <br/><b>  Y:</b></span> {y}",
		name: "Cluster 1",
		markerType: "square",
		showInLegend: true,
		dataPoints: <?php echo json_encode($formattedDataPoints[0]); ?>
	},
	{
		type: "scatter",
		name: "Cluster 2",
		markerType: "triangle",
		showInLegend: true, 
		toolTipContent: "<span style=\"color:#C0504E \"><b>{name}</b></span><br/><b> X:</b> {x} <br/><b>  Y:</b></span> {y}",
		dataPoints: <?php echo json_encode($formattedDataPoints[1]); ?>
	}
	]
});
 
chart.render();
 
function toggleDataSeries(e){
	if (typeof(e.dataSeries.visible) === "undefined" || e.dataSeries.visible) {
		e.dataSeries.visible = false;
	}
	else{
		e.dataSeries.visible = true;
	}
	chart.render();
}
 
}
</script>
</head>
<body>
<div id="chartContainer" style="height: 370px; width: 100%;"></div>
<script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>
</body>
</html>          
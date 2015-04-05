<?php
include 'includes/common.php';
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>DataDrink</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="css/bootstrap.min.css">
<style>
html, body { height: 100%; }
</style>
<script src="js/Chart.min.js"></script>
<script>
Chart.defaults.global.legendTemplate = "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<segments.length; i++){%><li><span style=\"background-color:<%=segments[i].fillColor%>\">toto</span><%if(segments[i].label){%><%=segments[i].label%><%}%></li><%}%></ul>";
</script>
<script src="js/jquery.min.js"></script>
</head>

<body>
<div class="container" style="height: 100%;">

<nav role="navigation" class="navbar navbar-inverse navbar-static-top">
    <!-- container-fluid -->    
    <div class="container-fluid">
        <!-- navbar-header -->
        <div class="navbar-header">
              
              <a href="./" class="navbar-brand">Home</a>
              <ul class="nav navbar-nav nav-pills">
                  <li class="active"><a href="map.php"><span class="glyphicon glyphicon-globe"></span></a></li>
		  <li class="active"><a href="stats.php"><span class="glyphicon glyphicon-stats"></span></a></li>
		  <li class="active"><a href="#"><span class="glyphicon glyphicon-dashboard"></span></a></li>
		  <li class="active pull-right"><a href="phpliteadmin.php"><span class="glyphicon glyphicon-wrench"></span></a></li>
              </ul>
        </div>
        <!-- /navbar-header -->
    </div>
    <!-- /container-fluid -->    
</nav>


<div class="panel panel-default">
  <div class="panel-heading">Top 10 Lieux</div>
  <table class="table table-condensed table-bordered text-center">
	<tr><th>Lieu</th><th>Quantité</th></tr>
<?php
$tmp = sql_select('sum(quantity) as quantity, comment as name 
from data
WHERE comment != ""
AND user_id = :uid
group by comment
order by quantity DESC
LIMIT 10', array('uid' => $user->getId()));
foreach($tmp as $item) {
	echo '<tr><td>'. $item['name'] .'</td><td>'. $item['quantity'] .'</td></tr>';
}
?>
  </table>
</div>

<div class="panel panel-default">
  <div class="panel-heading">Conso / mois / jour</div>
  <div class="text-center"><canvas id="chart-month" style="width:100%; height:20%; min-height:200px;"></canvas></div>
  <table class="table table-condensed table-bordered text-center">
	<tr><th>Mois</th><th>Quantité</th></tr>
<?php
$items = $user->getQuantityByDayByMonth();
?>
	<tr><td>Janvier</td><td><?=$items[0]?></td></tr>
	<tr><td>Février</td><td><?=$items[1]?></td></tr>
	<tr><td>Mars</td><td><?=$items[2]?></td></tr>
	<tr><td>Avril</td><td><?=$items[3]?></td></tr>
	<tr><td>Mai</td><td><?=$items[4]?></td></tr>
	<tr><td>Juin</td><td><?=$items[5]?></td></tr>
	<tr><td>Juillet</td><td><?=$items[6]?></td></tr>
	<tr><td>Août</td><td><?=$items[7]?></td></tr>
	<tr><td>Septembre</td><td><?=$items[8]?></td></tr>
	<tr><td>Octobre</td><td><?=$items[9]?></td></tr>
	<tr><td>Novembre</td><td><?=$items[10]?></td></tr>
	<tr><td>Décembre</td><td><?=$items[11]?></td></tr>
  </table>
</div>

<div class="panel panel-default">
  <div class="panel-heading">Conso / jour</div>
  <div class="text-center"><canvas id="chart-jour" style="width:100%; height:20%; min-height:200px;"></canvas></div>
  <table class="table table-condensed table-bordered text-center">
	<tr><th>Jour</th><th>Quantité</th></tr>
<?php
$items = $user->getQuantityByDay();
?>
	<tr><td>Lundi</td><td><?=$items[0]?></td></tr>
	<tr><td>Mardi</td><td><?=$items[1]?></td></tr>
	<tr><td>Mercredi</td><td><?=$items[2]?></td></tr>
	<tr><td>Jeudi</td><td><?=$items[3]?></td></tr>
	<tr><td>Vendredi</td><td><?=$items[4]?></td></tr>
	<tr><td>Samedi</td><td><?=$items[5]?></td></tr>
	<tr><td>Dimanche</td><td><?=$items[6]?></td></tr>
  </table>
</div>

<div class="panel panel-default">
  <div class="panel-heading">Conso moyenne / jour</div>
  <div class="text-center"><canvas id="chart-day-average" style="width:100%; height:20%; min-height:200px;"></canvas></div>
  <table class="table table-condensed table-bordered text-center">
	<tr><th>Jour</th><th>Quantité</th></tr>
<?php
$days_average = $user->getAverageQuantityByDay();
?>
	<tr><td>Lundi</td><td><?=$days_average[0]?></td></tr>
	<tr><td>Mardi</td><td><?=$days_average[1]?></td></tr>
	<tr><td>Mercredi</td><td><?=$days_average[2]?></td></tr>
	<tr><td>Jeudi</td><td><?=$days_average[3]?></td></tr>
	<tr><td>Vendredi</td><td><?=$days_average[4]?></td></tr>
	<tr><td>Samedi</td><td><?=$days_average[5]?></td></tr>
	<tr><td>Dimanche</td><td><?=$days_average[6]?></td></tr>
  </table>
</div>

<div class="panel panel-default">
  <div class="panel-heading">Conso / type</div>
  <div class="panel-body">
  <div class="col-xs-7 text-center"><canvas id="chart-type" width="150" height="150"></canvas></div>
  <div id="chart-legend-type" class="col-xs-5"></div>
  </div>
</div>

</div>
<script>
<?php
$months = $user->getQuantityByDayByMonth();
$days = $user->getQuantityByDay();
$quantities = $user->getQuantityByType();
?>
var ctx_month = document.getElementById("chart-month").getContext("2d");
var data_month = {
    labels: ["Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre", "Janvier"],
    datasets: [
        {
            label: "My First dataset",
            fillColor: "rgba(151,187,205,0.2)",
            strokeColor: "rgba(151,187,205,1)",
            pointColor: "rgba(151,187,205,1)",
            pointStrokeColor: "#fff",
            pointHighlightFill: "#fff",
            pointHighlightStroke: "rgba(151,187,205,1)",
            data: [<?=implode($months, ',')?>,<?=$months[0]?>]
        }
    ]
};
new Chart(ctx_month).Line(data_month);

var ctx_day = document.getElementById("chart-jour").getContext("2d");
var data_day = {
    labels: ["Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi", "Dimanche", "Lundi"],
    datasets: [
        {
            label: "My First dataset",
            fillColor: "rgba(151,187,205,0.2)",
            strokeColor: "rgba(151,187,205,1)",
            pointColor: "rgba(151,187,205,1)",
            pointStrokeColor: "#fff",
            pointHighlightFill: "#fff",
            pointHighlightStroke: "rgba(151,187,205,1)",
            data: [<?=implode($days, ',')?>, <?=$days[0]?>]
        }  
    ]
};
new Chart(ctx_day).Line(data_day);

var ctx_day_average = document.getElementById("chart-day-average").getContext("2d");
var data_day_average = {
    labels: ["Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi", "Dimanche", "Lundi"],
    datasets: [
        {
            label: "My First dataset",
            fillColor: "rgba(151,187,205,0.2)",
            strokeColor: "rgba(151,187,205,1)",
            pointColor: "rgba(151,187,205,1)",
            pointStrokeColor: "#fff",
            pointHighlightFill: "#fff",
            pointHighlightStroke: "rgba(151,187,205,1)",
            data: [<?=implode($days_average, ',')?>, <?=$days_average[0]?>]
        } 
    ]
};
new Chart(ctx_day_average).Line(data_day_average);

var ctx = document.getElementById("chart-type").getContext("2d");
var data = [
<?php
for($i = 0; $i < count($quantities); $i++) {
$quantity = $quantities[$i];
?>
    {
        value: <?=$quantity['quantity']?>,
        color:"<?=getUniqueColor($i)?>",
        highlight: "<?=getUniqueHighlightColor($i)?>",
        label: "<?=$quantity['type']?>"
    },
<?php
}
?>
];
var myNewChart = new Chart(ctx).Doughnut(data, {legendTemplate : "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<segments.length; i++){%><li><span style=\"background-color:<%=segments[i].fillColor%>\">-</span> <%if(segments[i].label){%><%=segments[i].label%><%}%></li><%}%></ul>"});
document.getElementById("chart-legend-type").innerHTML = myNewChart.generateLegend();
</script>
</body>
</html>

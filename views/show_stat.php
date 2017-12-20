<?php
/** @var array $data */

$xKeys = array_values($data['avg']);

$values = array_values($data['disp']);

$yData = [];

for ($i = 1; $i <= 40; $i++) {
    $yData[] = [
        $values[$i-1],
        "card" . (string)$i,
    ];
}

array_multisort($xKeys, $yData);

$yKeys = array_column($yData, 0);
$labels = array_column($yData, 1);

?>

<!DOCTYPE html>
<head>
    <title>Show stat</title>
    <script src="https://cdn.plot.ly/plotly-latest.min.js"></script>
</head>
<body>
    <a href="/new_record">Insert pyramid</a> <a href="/stat">Calculate</a>
    <br/><br/><?php print json_encode($xKeys, JSON_PRETTY_PRINT); ?>
    <br/><br/><?php print json_encode($yKeys, JSON_PRETTY_PRINT); ?>
    <br/><br/><?php print json_encode($labels, JSON_PRETTY_PRINT); ?>
    <div id="myDiv"></div>
    <script>
        var trace1 = {
            x: <?php print json_encode($xKeys); ?>, 
            y: <?php print json_encode($yKeys); ?>, 
            mode: 'markers', 
            name: 'Points', 
            text: <?php print json_encode($labels); ?>, 
            marker: {
                color: 'rgb(219, 64, 82)', 
                size: 12,
                width: 3
            }, 
            type: 'scatter'
        };
        var data = [trace1];
        var layout = {
            title: 'Data', 
            xaxis: {
                title: 'avg', 
                showgrid: false, 
                zeroline: false
            }, 
            yaxis: {
                title: 'sq avg', 
                showline: true
            }
        };

        Plotly.newPlot('myDiv', data, layout);
    </script>
</body>
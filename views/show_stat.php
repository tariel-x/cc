<?php
/** @var array $data */
?>

<?php
/** @var \App\Model $model */
?>

<!DOCTYPE html>
<head>
    <title>Show stat</title>
    <script src="https://cdn.plot.ly/plotly-latest.min.js"></script>
</head>
<body>
    <?php echo json_encode($data, JSON_PRETTY_PRINT); ?>
    <div id="myDiv"></div>
    <script>
        var trace1 = {
            x: [4.666666666666667, 4.333333333333333], 
            y: [0.6666666666666666, 1],
            mode: 'lines+markers', 
            name: 'Points', 
            text: ['1', '2'], 
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
                title: 'x', 
                showgrid: false, 
                zeroline: false
            }, 
            yaxis: {
                title: 'y', 
                showline: true
            }
        };

        Plotly.newPlot('myDiv', data, layout);
    </script>
</body>
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
    <link rel="stylesheet" href="//cdn.jsdelivr.net/chartist.js/latest/chartist.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/chartist-plugin-tooltips@0.0.17/dist/chartist-plugin-tooltip.css">
    <script src="//cdn.jsdelivr.net/chartist.js/latest/chartist.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartist-plugin-tooltips@0.0.17/dist/chartist-plugin-tooltip.min.js"></script>
</head>
<body>
    <a href="/new_record">Insert pyramid</a> <a href="/stat">Calculate</a>
    <br/><br/><?php print json_encode($xKeys, JSON_PRETTY_PRINT); ?>
    <br/><br/><?php print json_encode($yKeys, JSON_PRETTY_PRINT); ?>
    <br/><br/><?php print json_encode($labels, JSON_PRETTY_PRINT); ?>
    <div class="ct-chart ct-perfect-fourth"></div>
    <script>
        var chart = new Chartist.Line(
            '.ct-chart', 
            {
                labels: [1, 2, 3],
                series: [
                [
                    {meta: 'description', value: 1 },
                    {meta: 'description', value: 5},
                    {meta: 'description', value: 3}
                ]
                ]
            }, 
            {
                width: '70%',
                height: '500px',
                fullWidth: true,
                plugins: [
                    Chartist.plugins.tooltip()
                ],
                showLine: false
            }
        );
    </script>
</body>
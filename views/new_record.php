<?php
/** @var \App\Model $model */
?>

<!DOCTYPE html>
<head>
    <title>New record</title>
</head>
<body>
    <form action="">
    <h1>New record</h1>
    <p>Class name: <input type='text' name='class_name' size=5></p>
    <p>Full name: <input type='text' name='full_name' size=5></p>
    <table border="0px">
        <?php for ($i=1; $i<=12; $i++) {?>
        <tr>
            <td>
                <?php if ($i >= 11) {?>
                    <input type='text' name='row<?php print 13-$i;?>col1' size=2 value='<?php print $model->{'row'.(13-$i).'col1'} ?>'>
                <?php } ?>
            </td>
            <td>
                <?php if ($i >= 9) {?>
                    <input type='text' name='row<?php print 13-$i;?>col2' size=2 value='<?php print $model->{'row'.(13-$i).'col2'} ?>'>
                <?php } ?>
            </td>
            <td>
                <?php if ($i >= 5) {?>
                    <input type='text' name='row<?php print 13-$i;?>col3' size=2 value='<?php print $model->{'row'.(13-$i).'col3'} ?>'>
                <?php } ?>  
            </td>
            <td>
                <input type='text' name='row<?php print 13-$i;?>col4' size=2 value='<?php print $model->{'row'.(13-$i).'col4'} ?>'>
            </td>
            <td>
                <?php if ($i >= 5) {?>
                    <input type='text' name='row<?php print 13-$i;?>col5' size=2 value='<?php print $model->{'row'.(13-$i).'col5'} ?>'>
                <?php } ?>    
            </td>
            <td>
                <?php if ($i >= 9) {?>
                    <input type='text' name='row<?php print 13-$i;?>col6' size=2 value='<?php print $model->{'row'.(13-$i).'col6'} ?>'>
                <?php } ?>
            </td>
            <td>
                <?php if ($i >= 11) {?>
                    <input type='text' name='row<?php print 13-$i;?>col7' size=2 value='<?php print $model->{'row'.(13-$i).'col7'} ?>'>
                <?php } ?>
            </td>
        </tr>
        <?php } ?>
    </table>
    <input type="submit" value="ok">
    </form>
</body>
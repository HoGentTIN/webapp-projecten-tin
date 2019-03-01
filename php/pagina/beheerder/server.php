<!-- Test pagina voor debugging -->
<?php
echo '<table cellpadding="10">' ;
foreach (array_keys($_SERVER) as $arg) {
    if (isset($_SERVER[$arg])) {
        echo '<tr><td>'.$arg.'</td><td>' . $_SERVER[$arg] . '</td></tr>' ;
    }
    else {
        echo '<tr><td>'.$arg.'</td><td>-</td></tr>' ;
    }
}
echo '</table>' ;
?>
<?php phpinfo(); ?>

<form method="post">
    <div><label for="username">Username: </label><input type="text" name="username" value="<?php echo $_POST['username'] ?>"></div>
    <div><label for="updated_in">Updated in last: </label><input type="text" name="updated_in" value="<?php echo $_POST['updated_in'] ?>"> days</div>
    <div><input type="submit"></div>
</form>

<?php
date_default_timezone_set('Europe/Prague');

function date_compare($a, $b)
{
    $t1 = strtotime($a['created_at']);
    $t2 = strtotime($b['created_at']);
    return $t1 - $t2;
}

if(!empty($_POST['username'])) {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    echo 'IP adresa požadavku: ' . $ip;
    echo '<h1>' . $_POST['username'] . "</h1>";

    require 'vendor/autoload.php';
    $client = new \Github\Client();
    $repositories = $client->api('user')->repositories('brzezinavaclav');
    usort($repositories, 'date_compare');
    foreach ($repositories as $repo) {
        if(empty($_POST['updated_in']) or (time() - strtotime($repo['updated_at'])) / (3600*24) < $_POST['updated_in']) {
            echo '<h2>' . $repo['name'] . "</h2>";
            echo '<h4>' . $repo['git_url'] . '</h4>';
            echo '<ul>';
            echo '<li>Created at: ' . $repo['created_at'] . '</li>';
            echo '<li>Updated at: ' . $repo['updated_at'] . '</li>';
            echo '</ul>';
        }
    }
}
?>

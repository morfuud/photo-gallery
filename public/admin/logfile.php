<?php
require_once("../../includes/initialize.php");

if (!$session->is_logged_in()) {
    redirect_to("login.php");
}

$file = SITE_ROOT.DS."logs".DS."log_file.txt";

if (isset($_GET['clear'])) {
    if ($_GET['clear'] == 'true') {
        file_put_contents($file, '');
        // Add the first log entry
        log_action('Logs Cleared', "by User {$session->user_id}");
        // redirect to same page so that the url will not have 
        // true anymore
        redirect_to('logfile.php');
    }
}
?>

<?php include_layout_template("admin_header.php"); ?>
<header>
    <div class="container">
        <br>
         <a href="index.php" class="btn btn-primary">
            <span class="fa fa-angle-double-left"></span> back
         </a>
        <h1><span class="fa fa-file-text-o"></span> Log File</h1>
    </div>
</header>

<section>
    <div class="container">
        <div class="col-sm-6">
            <?php
            if (file_exists($file) && is_readable($file) && 
                $handle = fopen($file, 'r')) {
                    echo "<ul class=\"list-group\">";
                    while(!feof($handle)) {
                        $entry = fgets($handle);
                        // checks for blank/new lines
                        if(trim($entry) !== "") {
                            echo "<li class=\"list-group-item list-group-item-success\">".
                            nl2br($entry)."</li>";
                        }
                    }
                    echo "</ul>";
                    fclose($handle);
                } else {
                    echo "Could not read form file<br>";
                }
            ?>
            <a href="logfile.php?clear=true" class="btn btn-danger">
                <span class="fa fa-trash"></span> clear logfile
            </a>
        </div>
    </div>
</section>
<?php include_layout_template("admin_footer.php"); ?>
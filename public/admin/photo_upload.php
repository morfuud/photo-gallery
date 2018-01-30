<?php
require_once("../../includes/initialize.php");

if (!$session->is_logged_in()) { 
    redirect_to("login.php");
}
?>
<?php
    $max_file_size = 1048576; // expressed in bytes
    
    if (isset($_POST['submit'])) {
        $photo = new Photograph();
        $photo->caption = $_POST['caption'];
        $photo->attach_file($_FILES['file_upload']);
        if ($photo->save()) {
            // success
            $session->message("Photograph uploaded successfully");
            redirect_to('list_photos.php');
        } else {
            // failure
            $message = join("<br>", $photo->errors);
        }
    }

?>
<?php include_layout_template("admin_header.php"); ?>

    <section id="main">
        <div class="container">
            <h2>Photo Upload</h2>

            <?php if(!empty($message)) {echo $message;} ?>
            <form action="<?php $_SERVER['PHP_SELF']; ?>" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo $max_file_size; ?>">
                </div>
                <div class="form-group">
                    <input type="file" name="file_upload">
                </div>
                <div class="form-group">
                    <label>Caption:</label>
                    <input type="text" name="caption" placeholder="caption" class="form-control">
                </div>
                <div>   
                    <button type="submit" name="submit" class="btn btn-default">Upload</button>
                </div>
            </form>
        </div>
    </section>
<?php include_layout_template("admin_footer.php"); ?>


<?php
require_once("../../includes/initialize.php");

if (!$session->is_logged_in()) { 
    redirect_to("login.php");
}

?>
<?php include_layout_template("admin_header.php"); ?>

    <section id="main">
        <div class="container">
         <h2><span class="fa fa-list-ul"></span> Menu</h2>
         <?php echo $message; ?>

         <a href="logfile.php" class="btn btn-info">
            <span class="fa fa-eye"></span> view logfile
         </a>
         <a href="photo_upload.php" class="btn btn-default">
         <span class="fa fa-file-image-o"><span> Upload Photo</a>
         <a href="list_photos.php" class="btn btn-warning">
         <span class="fa fa-file-image-o"><span> View Photos</a>
         <a href="comments.php" class="btn btn-success">
         <span class="fa fa-eye"><span> View Comments</a>
         <br><br>
         <a href="logout.php" class="btn btn-danger">
            <span class="fa fa-arrow-left"></span> logout
         </a>
        </div>
    </section>
<?php include_layout_template("admin_footer.php"); ?>


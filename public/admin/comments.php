<?php
require_once("../../includes/initialize.php");

if (!$session->is_logged_in()) {
    redirect_to("login.php");
}

if (empty($_GET['id'])) {
    $session->message("No photograph ID was provided");
    redirect_to("index.php");
}

$photo = Photograph::find_by_id($_GET['id']);
if (!$photo) {
    $session->message("The photo could not be located");
    redirect_to("index.php");
}
// Find all the comments
$comments = $photo->comments();
?>

<?php include_layout_template('admin_header.php'); ?>
<section>
    <div class="container">
        <div class="col-sm-6">
            <a href="list_photos.php" class="btn btn-primary">
                <span class="fa fa-arrow-left"></span> Back
            </a>
            <br><br>
            <h3>Comments on: <?php echo $photo->filename; ?></h3>
            <?php if (!empty($message)) {echo $message;} ?>
            <?php foreach($comments as $comment): ?>
            <div class="author">
                <?php echo htmlentities($comment->author); ?> wrote:
            </div>
            <div class="body">
                <?php echo strip_tags($comment->body, '<strong><em><p>'); ?>
            </div>
            <div class="created">
                <?php echo datetime_to_text($comment->created); ?>
                <a href="delete_comment.php?id=<?php echo $comment->id; ?>" class="btn btn-danger action">
                    <span class="fa fa-trash"></span> Delete
                </a>
            </div>
            <?php endforeach; ?>
            <?php if(empty($comments)) {echo "No comments.";} ?>
        </div>
    </div>
</section>
<?php include_layout_template('admin_footer.php'); ?>
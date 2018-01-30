<?php
require_once("../includes/initialize.php");

if (empty($_GET['id'])) {
    $session->message("No photograph ID was provided");
    redirect_to("index.php");
}

$photo = Photograph::find_by_id($_GET['id']);
if (!$photo) {
    $session->message("The photo could not be uploaded");
    redirect_to("index.php");
}

// form submission and processing
if (isset($_POST['submit'])) {
    $author = trim($_POST['author']);
    $body = trim($_POST['body']);

    $new_comment = Comment::make($photo->id, $author, $body);
    if($new_comment && $new_comment->save()) {
        // comment saved
        // No message needed; seeing the comment is a proof enough

        // Important! You could just let the page render form here.
        // But then if the page is reloaded, the form will try
        // to resubmit the comment. So redirect instead:
        redirect_to("photo.php?id={$photo->id}"); 
    } else {
        // failure
        $message = "There was an error that prevented the comment from being saved.";
    }
} else {
    $author = "";
    $body = "";
}

$comments = $photo->comments();
?>


<?php include_layout_template("header.php"); ?>

<section>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <a href="index.php" class="btn btn-primary"><span class="fa fa-arrow-left"></span> back</a>
            </div>
            <br><br>
            <div class="col-md-12">
                <img src="<?php echo $photo->image_path(); ?>">
                <p class="lead text-success"><?php echo $photo->caption ?></p>
            </div>
        </div>
    </div>
</section>

<!-- List comments -->
<section>
    <div class="container">
        <div class="row">

            <div class="col-sm-6">
                <?php foreach($comments as $comment): ?>
                <div class="author">
                    <?php echo htmlentities($comment->author); ?> wrote:
                </div>
                <div class="body">
                    <?php echo strip_tags($comment->body, '<strong><em><p>'); ?>
                </div>
                <div class="created">
                    <?php echo datetime_to_text($comment->created); ?>
                </div>
                <?php endforeach; ?>
                <?php if(empty($comments)) {echo "No comments.";} ?>
            </div>
            <div class="clearfix"></div>
            <div class="col-sm-6">
                <h3>New Comment:</h3>
                <?php if(!empty($message)) {echo $message;} ?>
                <form action="<?php $_SERVER['PHP_SELF']; ?>" method="POST">
                    <div class="form-group">
                        <label>Your Name:</label>
                        <input type="text" class="form-control" name="author" value="<?php echo $author; ?>">
                    </div>
                    <div class="form-group">
                        <label>Your comment:</label>
                        <textarea class="form-control" name="body" cols="40" row="8" value="<?php echo $body; ?>"></textarea>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-info" name="submit">Submit Comment</button>
                    </div>
                </form>  
            </div>
        </div>
    </div>
</section>
<?php include_layout_template("footer.php"); ?>
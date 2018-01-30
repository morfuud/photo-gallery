<?php
require_once("../includes/initialize.php");

// You'll need three things for pagination to work
// 1. the current page number ($current_page)
$page = !empty($_GET['page']) ? (int)$_GET['page'] : 1;
// 2. records per page ($per_page)
$per_page = 2;

// 3. total record count
$total_count = Photograph::count_all();

// Find all the photos
// Use pagination instead
/// $photos = Photograph::find_all();

$pagination = new Pagination($page, $per_page, $total_count);

// Instead of finding all records, just find the records
// for this page
$sql  = "SELECT * FROM photographs ";
$sql .= "LIMIT {$per_page} ";
$sql .= "OFFSET {$pagination->offset()}";

$photos = Photograph::find_by_sql($sql);

// Need to add ?page=$page to all links we want to
// maintain the current page (or store $page in $session)

?>

<?php include_layout_template("header.php"); ?>

<section>
    <div class="container">
        <div class="row">

            <?php foreach ($photos as $photo) : ?>
            <div class="col-md-4 col-lg-3 col-sm-6">
                <a href="photo.php?id=<?php echo $photo->id ?>" class="thumbnail">
                    <img src="<?php echo $photo->image_path(); ?>">
                    <p><?php echo $photo->caption ?></p>
                </a>
            </div>
            <?php endforeach; ?>
            <div class="clearfix"></div>
            <div>
                <?php 
                    if ($pagination->total_pages() > 1) {
                        if ($pagination->has_next_page()) {
                            echo "<a href=\"index.php?page=";
                            echo $pagination->next_page();
                            echo "\">Next &raquo; </a>";
                        }

                        if ($pagination->has_previous_page()) {
                            echo "<a href=\"index.php?page=";
                            echo $pagination->previous_page();
                            echo "\">Previous &laquo; </a>";
                        }
                    }

                ?>
            </div>
        </div>
    </div>
</section>
<?php include_layout_template("footer.php"); ?>
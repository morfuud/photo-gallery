<?php
require_once("../../includes/initialize.php");

if ($session->is_logged_in()) {
    redirect_to("index.php");
}

if (isset($_POST['submit'])) {

    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Check database to see if username/password exist
    $found_user = User::authenticate($username, $password);

    if ($found_user) {
        $session->login($found_user);
        log_action("Login", "{$found_user->username} logged in");
        redirect_to("index.php");
    } else {
        // username/password combo was not found in the database
        $message = "Username/password combination incorrect";
    }
}
?>
<!DOCTYPE html>
<?php include_layout_template("admin_header.php"); ?>

<section id="main">
    <div class="container">
        <h2>Staff login</h2>
        <div class="col-sm-6">
            <br>
            <?php if(!empty($message)) {echo $message;} ?>
            <form method="POST" action="<?php $_SERVER['PHP_SELF']; ?>">
                <div class="form-group">
                    <label>Username:</label>
                    <input type="text" class="form-control" name="username" placeholder="Enter your Username..."
                        value="<?php if(isset($username)) {echo htmlentities($username);} ?>">
                </div>

                <div class="form-group">
                    <label>Password:</label>
                    <input type="password" class="form-control" name="password" placeholder="Enter you Password..."
                        value="<?php if(isset($password)) {echo htmlentities($username);} ?>">
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-default" name="submit">Login</button>
                </div>
            </form>
        </div>
    </div>
</section>

<?php include_layout_template("admin_footer.php"); ?>
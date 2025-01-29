<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" type="text/css" href="public/css/signup.css">
    <link rel="icon" type="image/x-icon" href="public/img/favicon.ico">
    <title>ConcertLog • Sign up</title>
</html>
<body>
    <div class="container">
        <div class="logo">
            <img src="public/img/logo-nazwa.svg">
        </div>
        <div class="sign-up-container">
            <form class="sign-up" action="signup" method="POST">
                <input class="username" name="username" type="text" placeholder="Username" required>
                <input class="email" name="email" type="text" value="<?PHP echo reset($email);?>" placeholder="E-mail" required>
                <input class="password" name="password" type="password" placeholder="Password" required>
                <input class="repeat-password" name="repeat-password" type="password" placeholder="Repeat password" required>
                <button class="sign-up" type="submit">Sign up</button>
            </form>
            <p>Already have an account? <a href="login" class="has-account">Sign in</a></p>
        </div>
</body>
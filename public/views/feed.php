<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" type="text/css" href="public/css/style.css">
    <link rel="stylesheet" type="text/css" href="public/css/posts.css">
    <link rel="icon" type="image/x-icon" href="public/img/favicon.ico">
    <script src="https://kit.fontawesome.com/7ae6ad35c3.js" crossorigin="anonymous"></script>
    <title>ConcertLog • Main Page</title>
</head>
<body>
    <div class = "base-container">
        <nav>
            <img src = "public/img/logo-nazwa-biala.svg">
            <ul>
                <li>
                    <i class="fa-solid fa-house"></i>
                    <a href="feed" class="button">Feed</a>
                </li>
                <li>
                    <i class="fa-solid fa-user-group"></i>
                    <a href="friends" class="button">Friends</a>
                </li>
                <li>
                    <i class="fa-solid fa-plus"></i>
                    <a href="addconcert" class="button">Add concert</a>
                </li>
                <li>
                    <i class="fa-solid fa-user"></i>
                    <a href="profile" class="button">Profile</a>
                </li>
            </ul>
            <div class="logout">
                <ul>
                    <li>
                    <i class="fa-solid fa-sign-out"></i>
                    <a href="logout" class="button">Logout</a>
                    </li>
                </ul>
            </div>

        </nav>
        <main>
            <header>
                <div class = "search-bar">
                    <form>
                        <input placeholder="Search">
                    </form>
                </div>

            </header>
            <section class = "feed">
                <div id="post-1">
                    <img src="public/uploads/concert1.jpg">
                    <div>
                        <h2>Concert 1</h2>
                        <p>description</p>
                        <div class="social-section">
                            <i class="fa-solid fa-heart"> 666</i>
                            <i class="fa-solid fa-bookmark"> 2</i>
                        </div>
                    </div>
                </div>
                <div id="post-2">
                    <img src="public/img/uploads/concert2.jpg">
                    <div>
                        <h2>Concert 2</h2>
                        <p>description</p>
                        <div class="social-section">
                            <i class="fa-solid fa-heart"> 666</i>
                            <i class="fa-solid fa-bookmark"> 2</i>
                        </div>
                    </div>
                </div>
                <div id="post-3">
                    <img src="public/img/uploads/concert1.jpg">
                    <div>
                        <h2>Concert 3</h2>
                        <p>description</p>
                        <div class="social-section">
                            <i class="fa-solid fa-heart"> 666</i>
                            <i class="fa-solid fa-bookmark"> 2</i>
                        </div>
                    </div>
                </div>
                <div id="post-4">
                    <img src="public/img/uploads/concert1.jpg">
                    <div>
                        <h2>Concert 4</h2>
                        <p>description</p>
                        <div class="social-section">
                            <i class="fa-solid fa-heart"> 666</i>
                            <i class="fa-solid fa-bookmark"> 2</i>
                        </div>
                    </div>
                </div>
                <div id="post-5">
                    <img src="public/img/uploads/concert1.jpg">
                    <div>
                        <h2>Concert 5</h2>
                        <p>description</p>
                        <div class="social-section">
                            <i class="fa-solid fa-heart"> 666</i>
                            <i class="fa-solid fa-bookmark"> 2</i>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </div>
    
</body>
</html>
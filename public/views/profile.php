<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" type="text/css" href="public/css/style.css">
    <link rel="stylesheet" type="text/css" href="public/css/profile.css">
    <link rel="icon" type="image/x-icon" href="public/img/favicon.ico">
    <script src="https://kit.fontawesome.com/7ae6ad35c3.js" crossorigin="anonymous"></script>
    <title>ConcertLog • Profile</title>
</head>
<body>
    <div class="base-container">
        <nav>
            <img src="public/img/logo-nazwa-biala.svg" alt="Logo">
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
        </nav>
        <main>
            <header>
                <div class="search-bar">
                    <form>
                        <input placeholder="Search" type="text">
                    </form>
                </div>
            </header>
            <section class="profile">
                <div class="profile-header">
                    <img src="public/img/default-profile.svg" alt="Profile Picture" class="profile-picture">
                    <h1 class="profile-name">Szymon Dral</h1>
                </div>
                <div class="profile-stats">
                    <div class="stat">
                        <h2>Last concert</h2>
                        <p>We Are Era 47</p>
                    </div>
                    <div class="stat">
                        <h2>Concerts Attended</h2>
                        <p>47</p>
                    </div>
                    <div class="stat">
                        <h2>Favorite Artist</h2>
                        <p>Travis Scott</p>
                    </div>
                    <div class="stat">
                        <h2>Artists seen</h2>
                        <p>33</p>
                    </div>
                </div>
            </section>
        </main>
    </div>
</body>
</html>

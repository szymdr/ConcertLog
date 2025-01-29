<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" type="text/css" href="public/css/style.css">
    <link rel="stylesheet" type="text/css" href="public/css/posts.css">
    <link rel="icon" type="image/x-icon" href="public/img/favicon.ico">
    <script src="https://kit.fontawesome.com/7ae6ad35c3.js" crossorigin="anonymous"></script>
    <script src="public/js/photo-carousel.js" defer></script>
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
                <li class="profile">
                    <img src = "public/img/profile_picture.png"></img>
                    <a href="profile" class="button-profile">Profile</a>
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
            <section class="feed">
                <?php foreach ($concerts as $concert): ?>
                <div class="post">
                <div class="carousel">
                    <div class="carousel-track">
                        <?php foreach ($concert->getImages() as $image): ?>
                            <img src="public/uploads/<?= $image; ?>" alt="Concert image">
                        <?php endforeach; ?>
                    </div>
                    <?php if (count($concert->getImages()) > 1): ?>
                        <button class="carousel-button prev">&lt;</button>
                        <button class="carousel-button next">&gt;</button>
                    <?php endif; ?>
                </div>
                    <div class="details">
                        <h2><?= htmlspecialchars($concert->getTitle()); ?></h2>
                        <p><strong>Artist:</strong> <?= htmlspecialchars($concert->getArtist()); ?></p>
                        <p><strong>Date:</strong> <?= htmlspecialchars($concert->getDate()); ?></p>
                        <p><strong>Venue:</strong> <?= htmlspecialchars($concert->getVenue()); ?></p>
                        <p><strong>Location:</strong> <?= htmlspecialchars($concert->getLocation()); ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </section>

        </main>
    </div>
    
</body>
</html>
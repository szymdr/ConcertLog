<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">  
    <link rel="stylesheet" type="text/css" href="public/css/style.css">
    <link rel="stylesheet" type="text/css" href="public/css/profile.css">
    <link rel="stylesheet" type="text/css" href="public/css/edit_profile_popup.css">
    <link rel="icon" type="image/x-icon" href="public/img/favicon.ico">
    <script src="https://kit.fontawesome.com/7ae6ad35c3.js" crossorigin="anonymous"></script>
    <script src="public/js/popup.js"></script>
    <title>ConcertLog • Profile</title>
</head>
<body>
    <script src="public/js/popup.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <div class="base-container">
        <nav>   
            <img src="public/img/logo-nazwa-biala.svg" alt="Logo">
            <ul>
                <li>
                    <i class="fa-solid fa-house"></i>
                    <a href="feed" class="button">Feed</a>
                </li>
                <li>
                    <i class="fa-solid fa-plus"></i>
                    <a href="addconcert" class="button">Add concert</a>
                </li>
                <li class="navigation-profile">
                    
                    <img src = "public/uploads/profilePictures/<?=$_SESSION['profile_picture']?>"></img>
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
            </ul>
        </nav>
        <main>
            <div class="profile-container">
                <section class="profile">
                    <div class="profile-header">
                        <img src="public/uploads/profilePictures/<?=$_SESSION['profile_picture']?>" alt="Profile Picture" class="profile-picture">
                        <h1 class="profile-name"><?=$_SESSION['username']?></h1>
                        <button class="edit-profile-button" onclick="openPopup()"><i class="fa-solid fa-gear"></i></button>
                    </div>
                    <div class="profile-stats">
                        <div class="stat">
                            <h2>Last concert</h2>
                            <p><?=$statistics->getLastConcert()?></p>
                        </div>
                        <div class="stat">
                            <h2>Concerts Attended</h2>
                            <p><?=$statistics->getConcertsAttended()?></p>
                        </div>
                        <div class="stat">
                            <h2>Favorite Artist</h2>
                            <p><?=array_keys($statistics->getTopArtists())[0]?></p>
                        </div>
                        <div class="stat">
                            <h2>Artists seen</h2>
                            <p><?=$statistics->getArtistsSeen()?></p>
                        </div>
                    </div>
                </section>
                <section class="stats-grid">
                    <div class="stats-box-years">
                        <h2>Concerts Per Year</h2>
                        <canvas id="concertsChart"></canvas>
                    </div>
                    <div class="stats-box">
                        <h2>Top Artists</h2>
                        <ul>
                        <?php foreach (array_keys($statistics->getTopArtists()) as $artist): ?>
                            <li><?=$artist?> <span><?=$statistics->getTopArtists()[$artist]?></span></li>
                        <?php endforeach; ?>
                        </ul>
                    </div>
                    <div class="stats-box">
                        <h2>Top Genres</h2>
                        <ul>
                        <?php foreach (array_keys($statistics->getTopGenres()) as $genre): ?>
                            <li><?=$genre?> <span><?=$statistics->getTopGenres()[$genre]?></span></li>
                        <?php endforeach; ?>
                        </ul>
                    </div>
                    <div class="stats-box">
                        <h2>Top Venues</h2>
                        <ul>
                        <?php foreach (array_keys($statistics->getTopVenues()) as $venue): ?>
                            <li><?=$venue?> <span><?=$statistics->getTopVenues()[$venue]?></span></li>
                        <?php endforeach; ?>
                        </ul>
                    </div>
                </section>
                <section class="concerts-container">
                    <h2>Your Concerts</h2>
                    <div class="concerts-grid">
                        <?php foreach ($concerts as $concert): ?>
                            <div class="concert-item">
                                <h3><?= htmlspecialchars($concert->getTitle()); ?></h3>
                                <p><strong>Artist:</strong> <?= htmlspecialchars($concert->getArtist()); ?></p>
                                <p><strong>Date:</strong> <?= htmlspecialchars($concert->getDate()); ?></p>
                                <p><strong>Venue:</strong> <?= htmlspecialchars($concert->getVenue()); ?></p>
                                <p><strong>Location:</strong> <?= htmlspecialchars($concert->getLocation()); ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>



            </div>
            

        </main>
    </div>

    <!-- Pop-up for editing profile -->
    <div id="edit-profile-popup" class="popup">
        <div class="popup-content">
            <span class="close-button" onclick="closePopup()">&times;</span>
            <h1>Edit Profile</h1>
            <form class="popup-form" action="saveProfileChanges" method="POST" ENCTYPE="multipart/form-data">
                <div class="form-group">
                    <label for="username">Change Username</label>
                    <input type="text" id="username" name="username" placeholder="New username">
                </div>
                <div class="file-upload">
                    <input type="file" id="profile-pic-input" name="profile-picture"" class="file-input" accept="image/*">
                    <label for="profile-pic-input" class="edit-concert-button">Wybierz zdjęcie profilowe</label>
                    <span class="file-name">Nie wybrano pliku</span>
                </div>
                <button type="submit" class="save-button">Save Changes</button>
            </form>
        </div>
    </div>
</body>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const fileInput = document.getElementById('profile-pic-input');
        const fileNameSpan = document.querySelector('.file-name');

        fileInput.addEventListener('change', () => {
            if (fileInput.files.length > 0) {
                fileNameSpan.textContent = fileInput.files[0].name;
            } else {
                fileNameSpan.textContent = 'Nie wybrano pliku';
            }
        });
    });
</script>
</html>


<?php $concertsPerYear = $statistics->getConcertsPerYear(); ?>
<script>
  const concertsPerYear = <?= json_encode($concertsPerYear) ?>;
</script>
<script src="public/js/chart.js"></script>

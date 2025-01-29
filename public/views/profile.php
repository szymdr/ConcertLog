<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" type="text/css" href="public/css/style.css">
    <link rel="stylesheet" type="text/css" href="public/css/profile.css">
    <link rel="stylesheet" type="text/css" href="public/css/edit_profile_popup.css">
    <link rel="icon" type="image/x-icon" href="public/img/favicon.ico">
    <script src="https://kit.fontawesome.com/7ae6ad35c3.js" crossorigin="anonymous"></script>
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
                    <i class="fa-solid fa-user-group"></i>
                    <a href="friends" class="button">Friends</a>
                </li>
                <li>
                    <i class="fa-solid fa-plus"></i>
                    <a href="addconcert" class="button">Add concert</a>
                </li>
                <li class="navigation-profile">
                    
                    <img src = "public/uploads/<?=$user->getProfilePicture()?>"></img>
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
            <header>
                <div class="search-bar">
                    <form>
                        <input placeholder="Search" type="text">
                    </form>
                </div>
            </header>
            <div class="profile-container">
                <section class="profile">
                    <div class="profile-header">
                        <img src="public/uploads/<?=$user->getProfilePicture()?>" alt="Profile Picture" class="profile-picture">
                        <h1 class="profile-name">szymon_dral</h1>
                        <button class="edit-profile-button" onclick="openPopup()">Edit Profile</button>
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
                    <label for="profile-picture">Change Profile Picture</label>
                    <input type="file" id="profile-picture" name="profile-picture" accept="image/*">
                </div>
                <div class="form-group">
                    <label for="username">Change Username</label>
                    <input type="text" id="username" name="username" placeholder="New username">
                </div>
                <button type="submit" class="save-button">Save Changes</button>
            </form>
        </div>
    </div>
</body>
</html>

<?php
// Przykładowe dane z bazy danych
$concertsPerYear = $statistics->getConcertsPerYear();
?>


<script>
    const concertsPerYear = <?php echo json_encode($concertsPerYear); ?>;
</script>
<script src="public/js/chart.js"></script>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" type="text/css" href="public/css/style.css">
    <link rel="stylesheet" type="text/css" href="public/css/addconcert.css">
    <link rel="icon" type="image/x-icon" href="public/img/favicon.ico">
    <script src="https://kit.fontawesome.com/7ae6ad35c3.js" crossorigin="anonymous"></script>
    <title>ConcertLog • Add concert</title>
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
                        <input placeholder="Search concerts" type="text">
                        <button type="submit" class="search-button">Search</button>
                    </form>
                </div>
            </header>
            <section class="add-concert">
                <h1>Add a New Concert</h1>
                <form action="createConcert" method="POST" ENCTYPE="multipart/form-data" class="add-concert-form">
                    <?php
                        if(isset($messages)){
                            foreach($messages as $message){
                                echo $message;
                            }
                            unset($messages);
                        }
                    ?>
                    <div class="form-group">
                        <label for="artist">Artist</label>
                        <input type="text" id="artist" name="artist" placeholder="Artist name" >
                    </div>
                    <div class="form-group">
                        <label for="date">Date</label>
                        <input type="date" id="date" name="date" >
                    </div>
                    <div class="form-group">
                        <label for="title">Concert or Tour Title</label>
                        <input type="text" id="title" name="title" placeholder="Concert or tour title">
                    </div>
                    <div class="form-group">
                        <label for="venue">Venue</label>
                        <input type="text" id="venue" name="venue" placeholder="Venue name" >
                    </div>
                    <div class="form-group">
                        <label for="location">Location</label>
                        <input type="text" id="location" name="location" placeholder="City, Country" >
                    </div>
                    <div class="form-group">
                        <label for="images">Add Photos</label>
                        <input type="file" id="images" name="images" accept="image/*" multiple>
                    </div>
                    <button type="submit" class="submit-button">Add Concert</button>
                </form>
            </section>
        </main>
    </div>
</body>
</html>

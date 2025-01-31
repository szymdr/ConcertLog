<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
                    <i class="fa-solid fa-plus"></i>
                    <a href="addconcert" class="button">Add concert</a>
                </li>
                <li class="profile">
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
            <header>
                <h1>Add a New Concert</h1>
            </header>
            <section class="add-concert">
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
                        <input type="text" id="artist" name="artist" placeholder="Artist name" required>
                    </div>
                    <div class="form-group">
                        <label for="date">Date</label>
                        <input type="date" id="date" name="date" required>
                    </div>
                    <div class="form-group">
                        <label for="title">Concert or Tour Title</label>
                        <input type="text" id="title" name="title" placeholder="Concert or tour title">
                    </div>
                    <div class="form-group">
                        <label for="title">Genre</label>
                        <input type="text" id="genre" name="genre" placeholder="Genre">
                    </div>
                    <div class="form-group">
                        <label for="venue">Venue</label>
                        <input type="text" id="venue" name="venue" placeholder="Venue name" >
                    </div>
                    <div class="form-group">
                        <label for="location">Location</label>
                        <input type="text" id="location" name="location" placeholder="City, Country" >
                    </div>
                    <div class="file-upload">
                        <input type="file" id="profile-pic-input" class="file-input" name="images[]" accept="image/*" multiple>
                        <label for="profile-pic-input" class="edit-concert-button">Add Photos (.png or .jpeg)</label>
                        <span class="file-name">Nie wybrano pliku</span>
                    </div>
                    <button type="submit" class="submit-button">Add Concert</button>
                </form>
            </section>
        </main>
    </div>
    <script>
        const fileInput = document.getElementById('profile-pic-input');
        const fileNameSpan = document.querySelector('.file-name');

        fileInput.addEventListener('change', () => {
            const files = Array.from(fileInput.files);
            if (files.length > 0) {
                const names = files.map(file => file.name).join(', ');
                fileNameSpan.textContent = names;
            } else {
                fileNameSpan.textContent = 'Nie wybrano pliku';
            }
        });
    </script>
</body>
</html>

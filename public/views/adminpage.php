<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Users</title>
    <link rel="stylesheet" type="text/css" href="public/css/style.css">
    <link rel="stylesheet" type="text/css" href="public/css/adminpage.css">
    <script src="https://kit.fontawesome.com/7ae6ad35c3.js" crossorigin="anonymous"></script>
</head>
<body>
    <div class="admin-container">
        <h1>Admin Panel - User Management</h1>
        <table class="user-table">
            <thead>
                <tr>
                    <th>Profile Picture</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                <tr>
                    <td>
                        <img src="public/uploads/profilePictures/<?= htmlspecialchars($user['profile_picture']) ?>" alt="Profile" class="profile-pic">
                    </td>
                    <td><?= htmlspecialchars($user['username']) ?></td>
                    <td><?= htmlspecialchars($user['email']) ?></td>
                    <td>
                        <form action="removeUser" method="POST">
                            <input type="hidden" name="email" value="<?= $user['email'] ?>">
                            <button type="submit" class="delete-btn">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
            <a href="logout" class="logout-button">
            <i class="fa-solid fa-sign-out"></i>Logout</a>
    </div>
</body>
</html>
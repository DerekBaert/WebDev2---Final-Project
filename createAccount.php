<?php
    session_start();
    require 'header.php';
    require 'ProjectFunctions.php';
    use Gumlet\ImageResize;
    require 'ImageResize.php';    
    require 'ImageResizeException.php';

    $validEntry = true;
    $validFile = false;
    $errorMessage = '';

    if($_POST)
    {       
        $image_upload = isset($_FILES['profile']) && ($_FILES['profile']['error'] === 0);
 
        if ($image_upload) 
        {
            $image_filename       = $_FILES['profile']['name'];
            $temporary_path       = $_FILES['profile']['tmp_name'];
            $new_image_path       = file_upload_path($image_filename);
            $uniqueId             = uniqid();

            if (file_is_an_image($temporary_path, $new_image_path)) 
            {
                move_uploaded_file($temporary_path, $new_image_path);                 

                $withoutExt = preg_replace('/\\.[^.\\s]{3,4}$/', '', $image_filename);
                $fileExtension   = pathinfo($image_filename, PATHINFO_EXTENSION);

                $image = new ImageResize($new_image_path);
                $image->resizeToWidth(400);
                $image->save(file_upload_path($withoutExt . '_Medium' . '.' . $fileExtension));  

                $image = new ImageResize($new_image_path);
                $image->resizeToWidth(75);
                $image->save(file_upload_path($withoutExt . '_Thumbnail' . '.' . $fileExtension));  
                $imagePath = $withoutExt . '_Thumbnail' . '.' . $fileExtension;
                $validFile = true;
            }
            else
            {
                $validFile = false;
            }      
        }
        if (!empty($_POST['email']) && !empty($_POST['username']) && !empty($_POST['password']))
        {
            $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
            $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);

            if(isInUse($username, $db, 'username'))
            {
                $validEntry = false;
                $errorMessage = 'Username is already in use.';
            }
            else if(isInUse($email, $db, 'email'))
            {
                $validEntry = false;
                $errorMessage = 'Email address is already in use.';
            }
            else
            {
                if($validFile)
                {
                    $query = "INSERT INTO user (username, password, email, profile_picture) values (:Username, :Password, :Email, :ImagePath)";
                    $statement = $db->prepare($query); 
                    $statement->bindValue(':ImagePath', $imagePath);
                }
                else
                {
                    $query = "INSERT INTO user (username, password, email) values (:Username, :Password, :Email)";
                    $statement = $db->prepare($query); 
                }

                $statement->bindValue(':Username', $username); 
                $statement->bindValue(':Password', $password);
                $statement->bindValue(':Email', $email);

                $statement->execute(); 
            }
        }
        else
        {
            $validEntry = false;
            $errorMessage = 'Username, Email Address and Password are all required fields.';
        }        
    }    
?>

<form class='login' method="post">
    <label for="username">Username:</label>
    <input type="text" id="username" name="username">
    <label for="password">Password:</label>
    <input type="password" id="password" name="password">
    <label for="email">Email Address:</label>
    <input type="text" id="email" name="email">
    <label for="image">Upload Profile Picture:</label>
    <input type="file" id="profile" name="profile">
    <input type="submit" value="Create Account" id="submitLogin">
    <?php if($_POST) : ?>
        <?php if($validEntry && $validFile) : ?>
            <p>Registration complete! Click <a href="login.php">here</a> to login</p>
        <?php elseif(!$validEntry) : ?>
            <p><?= $errorMessage ?></p>   
        <?php else : ?>    
            <p>Invalid file type.</p>
        <?php endif ?>
    <?php endif ?>
</form>

        <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/js/bootstrap.bundle.min.js" integrity="sha384-b5kHyXgcpbZJO/tY9Ul7kGkf1S0CWuKcCD38l8YkeH8z8QjE0GmW1gYU5S9FOnJ0" crossorigin="anonymous"></script>
    </body>    
</html>
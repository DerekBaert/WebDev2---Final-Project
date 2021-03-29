<?php
    // Create register form
    // Select * from user table where user=username and password=password.
    // If rowcount = 1, user logs in
    // Store username, role and id in SESSION
    // if session[user] isset, display logout (unset(session[user]))

    session_start();
    require 'ProjectFunctions.php';
    require 'header.php'; 
    
    if($_POST)
    {
        $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
        $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
        
        $query = "SELECT * FROM user WHERE username = :username AND password = :password";  
        $statement = $db->prepare($query); 

        $bind_values = ['username' => $username, 'password' => $password];
        $statement->execute($bind_values);

        $statement->execute(); 

        $row = $statement->fetchAll();

        if(count($row) != 0)
        {
            $_SESSION['user'] = [
                                    'id' => $row[0]['id'], 
                                    'role'=> $row[0]['account_type'], 
                                    'username' => $username
                                ];

            ?>
            <script type="text/javascript">
                window.location.href = 'index.php';
            </script>
            <?php
        }        
    }  
       
?>

<form class='login' method="post">
    <label for="username">Username:</label>
    <input type="text" id="username" name="username">
    <label for="password">Password:</label>
    <input type="password" id="password" name="password">
    <button type="submit" class="btn btn-outline-success">Login</button>
        <?php if($_POST) : ?>
            <?php if(count($row) == 0) :?>
                <h6 class="invalidLogin">Invalid username or password.</h6>
            <?php endif ?>
        <?php endif ?>
    <h6>Not Registered? <a href='createAccount.php'>Create an account.</a></h6>        
</form>

        <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/js/bootstrap.bundle.min.js" integrity="sha384-b5kHyXgcpbZJO/tY9Ul7kGkf1S0CWuKcCD38l8YkeH8z8QjE0GmW1gYU5S9FOnJ0" crossorigin="anonymous"></script>
    </body>    
</html>


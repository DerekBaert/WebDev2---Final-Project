<!-----------
 * Author: Derek Baert
 * Date: March 04, 2021
 * File: Recently Reviewed
 ------------>

<?php
    require 'header.php';


    $query = "SELECT game_id FROM reviews JOIN user ON user.id = reviews.user_id WHERE date_posted > " . strtotime('-14 days') . " AND visible = 1";
    $statement = $db->prepare($query); 
    $statement->execute();
    $row = $statement->fetchall();
?>


        <div class="container" id="games">
            
        </div>  
        

        <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/js/bootstrap.bundle.min.js" integrity="sha384-b5kHyXgcpbZJO/tY9Ul7kGkf1S0CWuKcCD38l8YkeH8z8QjE0GmW1gYU5S9FOnJ0" crossorigin="anonymous"></script>
    </body>    
</html>
<?php
    session_start();
    require 'header.php';
    require 'ProjectFunctions.php';

    $query = "SELECT * FROM user";  
    $statement = $db->prepare($query); 

    $statement->execute();

    $statement->execute();
?>
<div class="container">
    <table class="table">
        <thead>
            <tr>
                <th>User</th>
                <th>Email</th>
                <th>Number of Reviews</th>
                <th>Account Type</th>
                <th>Change Account Type</th>
            </tr>
        </thead>
        <tbody>            
            <?php while($row = $statement->fetch()) : ?>
                <tr>
                    <td><?=$row['username']?></td>
                    <td><?=$row['email']?></td>
                    <td><?=$row['number_of_reviews']?></td>
                    <td><?=findAccountType($row['account_type'])?></td>
                    <?php if($row['account_type'] == 3 || $row['account_type'] == 4 || ($row['account_type'] == 2 && $owner)) : ?>
                        <td>
                            <select>
                                <?php if($owner) : ?>
                                    <option>Administrator</option>
                                <?php endif ?>
                                <option>User</option>
                                <option>Suspended</option>
                            </select>
                            <button class="btn btn-outline-success" type="submit">Submit</button>
                        </td>
                    <?php else : ?> 
                        <td></td>
                    <?php endif ?> 
                </tr>
                <?php endwhile ?>            
        </tbody>
    </table>
</div>

        <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/js/bootstrap.bundle.min.js" integrity="sha384-b5kHyXgcpbZJO/tY9Ul7kGkf1S0CWuKcCD38l8YkeH8z8QjE0GmW1gYU5S9FOnJ0" crossorigin="anonymous"></script>
    </body>    
</html>
<!-----------
 * Author: Derek Baert
 * Date: March 21, 2021
 * File: Header for all pages
 ------------>
<?php    
    if(!isset($db))
    {
        define('DB_DSN','mysql:host=localhost;dbname=reviewbase');
        define('DB_USER','UserConnect');
        define('DB_PASS','Password01');

        try 
        {
            $db = new PDO(DB_DSN, DB_USER, DB_PASS);
        } 
        catch (PDOException $e) 
        {
            print "Error: " . $e->getMessage();
            die(); // Force execution to stop on errors.
        }
    }
    
?>
<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">        
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BmbxuPwQa2lc/FVzBcNJ7UAyJxM6wuqIj61tLrc4wSX0szH/Ev+nYRRuWlolflfl" crossorigin="anonymous">
        <link href="style.css" rel="stylesheet">
        <title>Review Base</title>
    </head>
    <body>    
        <div class="page-header">
            <h1>ReviewBase</h1>
            <div class="sign-in">
                <?php if(!isset($_SESSION['user'])) :?>
                    <a href="login.php">Login</a>
                    <a href="createAccount.php">Create Account</a>
                <?php else : ?>
                    <img src="profile_images/<?=$_SESSION['user']['profile_picture']?>" alt="Profile Picture">
                    <a href="user_profile.php?user=<?=$_SESSION['user']['id']?>"><?=$_SESSION['user']['username']?>'s Profile</a>
                    <a href="logout.php">Logout</a>
                <?php endif ?>
            </div>
        </div>
        <div class="container">
            <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container-fluid">
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                    <a class="nav-link" href="newReleases.php">New Releases</a>
                    </li>
                    <li class="nav-item">
                    <a class="nav-link" href="recentlyReviewed.php">Recently Reviewed</a>
                    </li>
                    <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">Platform</a>
                    <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <li class="dropdown-header">Microsoft</li> 
                            <li><a class="dropdown-item" href="console.php?id=49&name=Xbox%20One">Xbox One</a></li>
                            <li><a class="dropdown-item" href="console.php?id=169&name=Xbox%20Series">Xbox Series</a></li>
                        <li class="dropdown-header">Sony</li> 
                            <li><a class="dropdown-item" href="console.php?id=48&name=PlayStation%204">PlayStation 4</a></li>
                            <li><a class="dropdown-item" href="console.php?id=167&name=PlayStation%205">PlayStation 5</a></li>
                        <li class="dropdown-header">Nintendo</li>                             
                        <li><a class="dropdown-item" href="console.php?id=130&name=Nintendo%20Switch">Nintendo Switch</a></li>
                        <li class="dropdown-header">PC</li>                             
                        <li><a class="dropdown-item" href="console.php?id=6&name=Windows">Windows</a></li> 
                        <li><hr class="dropdown-divider" style="background-color:#6c757d;"></li>
                        <li><a class="dropdown-item" href="#">All Platforms</a></li> 
                    </ul>
                    </li>                    
                </ul>
                <form class="d-flex" method='post' action='search.php'>
                    <input class="form-control me-2" name='search' id='search' type="search" placeholder="Search by game or platform..." aria-label="Search">\
                    <input type="radio" id="gameRadio" name="category" value="game" checked="checked">
                    <label for="gameRadio" class='radio-label'>Game</label><br>
                    <input type="radio" id="platformRadio" name="category" value="platform">
                    <label for="platformRadio" class='radio-label'>Platform</label><br> 
                    <button class="btn btn-outline-success" type="submit">Search</button>
                </form>
                </div>
            </div>
            </nav>
        </div>
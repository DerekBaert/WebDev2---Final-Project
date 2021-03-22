<!-----------
 * Author: Derek Baert
 * Date: March 04, 2021
 * File: Recently Reviewed
 ------------>

<?php
    $json = file_get_contents('https://www.giantbomb.com/api/releases/?format=json&api_key=2d85f2dfc0c87ca82eba3b139d9b58d59079e080&filter=release_date:' . date('Y-m-d', strtotime('-7 days')) . '|' . date('Y-m-d') . '&sort=release_date:desc&limit=20');
    $games = json_decode($json, true);
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
                    <a class="nav-link active" href="#">Recently Reviewed</a>
                    </li>
                    <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">Platform</a>
                    <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <li class="dropdown-header">Microsoft</li> 
                            <li><a class="dropdown-item" href="#">Xbox One</a></li>
                            <li><a class="dropdown-item" href="#">Xbox Series X/S</a></li>
                        <li class="dropdown-header">Sony</li> 
                            <li><a class="dropdown-item" href="#">Playstation 4</a></li>
                            <li><a class="dropdown-item" href="#">Playstation 5</a></li>
                        <li class="dropdown-header">Nintendo</li>
                            <li><a class="dropdown-item" href="#">Nintendo Switch</a></li> 
                            <li><hr class="dropdown-divider" style="background-color:#6c757d;"></li>
                        <li><a class="dropdown-item" href="#">All Platforms</a></li> 
                    </ul>
                    </li>                    
                </ul>
                <form class="d-flex">
                    <input class="form-control me-2" type="search" placeholder="Search for a game..." aria-label="Search">
                    <button class="btn btn-outline-success" type="submit">Search</button>
                </form>
                </div>
            </div>
            </nav>
        </div>
        <div class="container" id="games">
            <div class="game">
                <div class="thumbnail"><img src="https://static.wikia.nocookie.net/metroid/images/7/75/Metroid_Prime_3_Packaging.jpg" alt="Boxart"></div>
                <div class="quickdata">
                    <h4>Metroid Prime: Corruption</h4>
                    <h5>Release Date: August 28, 2007</h5> 
                    <h5>Platforms: Gamecube, Wii</h5> 
                    <h5>Genre: First-person shooter</h5>
                    <h5>Rating: T</h5>
                    <h5>Average Score: 10/10</h5>
                </div>
            </div>
            <div class="game">
                <div class="thumbnail"><img src="https://static.wikia.nocookie.net/metroid/images/7/75/Metroid_Prime_3_Packaging.jpg" alt="Boxart"></div>
                <div class="quickdata">
                    <h4>Metroid Prime: Corruption</h4>
                    <h5>Release Date: August 28, 2007</h5> 
                    <h5>Platforms: Gamecube, Wii</h5> 
                    <h5>Genre: First-person shooter</h5>
                    <h5>Rating: T</h5>
                    <h5>Average Score: 10/10</h5>
                </div>                
            </div>
        </div>  
        

        <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/js/bootstrap.bundle.min.js" integrity="sha384-b5kHyXgcpbZJO/tY9Ul7kGkf1S0CWuKcCD38l8YkeH8z8QjE0GmW1gYU5S9FOnJ0" crossorigin="anonymous"></script>
    </body>    
</html>
<!-----------
 * Author: Derek Baert
 * Date: March 04, 2021
 * File: New releases page
 ------------>

<?php

    $json = file_get_contents('https://api.rawg.io/api/games?key=1d542aca19da40d9948a4becef92ba41&dates=' . date('Y-m-d', strtotime('-14 days')) . ','. date('Y-m-d'));
    $games = json_decode($json, true);

    function CommaList($array, $key)
        {
            $return = '';
            $first = true;
            foreach($array as $item)
            {
                if($first)
                {
                    $return .= $item[$key]['name'];
                    $first = false;
                }
                else
                {
                    $return .= ', ' . $item[$key]['name'];
                }               
            }
            return $return;                            
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
                    <a class="nav-link active" href="newReleases.php">New Releases</a>
                    </li>
                    <li class="nav-item">
                    <a class="nav-link" href="recentlyReviewed.php">Recently Reviewed</a>
                    </li>
                    <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">Platform</a>
                    <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                    <li class="dropdown-header">Microsoft</li> 
                            <li><a class="dropdown-item" href="console.php?id=1">Xbox One</a></li>
                            <li><a class="dropdown-item" href="console.php?id=186">Xbox Series S/X</a></li>
                        <li class="dropdown-header">Sony</li> 
                            <li><a class="dropdown-item" href="console.php?id=18">Playstation 4</a></li>
                            <li><a class="dropdown-item" href="console.php?id=187">Playstation 5</a></li>
                        <li class="dropdown-header">Nintendo</li>                             
                        <li><a class="dropdown-item" href="console.php?id=7">Nintendo Switch</a></li>
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
            <?php foreach ($games['results'] as $game): ?>
                <div class="game">
                        <div class="thumbnail"><img src=<?= $game['background_image']?> alt="Boxart"></div>
                    <div class="quickdata">
                        <h4><?= $game['name']?></h4>
                        <h5>Release Date: <?= date('F d, Y', strtotime($game['released']))?></h5> 
                        <h5>Platforms: <?= CommaList($game['platforms'], 'platform') ?> </h5>         
                        <?php if($game['esrb_rating'] == null): ?>
                            <h5>Age Rating: [Unrated]</h5>
                        <?php else: ?>
                            <h5>Age Rating: <?= $game['esrb_rating']['name'] ?></h5>
                        <?php endif  ?>    
                        <h5>Average Score: 10/10</h5>
                    </div>
                    <div class="addReview"> 
                        <form action="postReview.php" method="post">
                            <input type="hidden" id="id" name="id" value="<?= $game['id'] ?>" />
                            <input type="submit" name = <?= $game['id'] ?> class="btn btn-primary" value="Post Review"/>
                        </form>       
                        <form action="gamePage.php" method="post">  
                            <input type="hidden" id="id" name="id" value="<?= $game['id'] ?>" />
                            <input type="submit" name="View Reviews" class="btn btn-primary" value="View Reviews"/>
                        </form>                
                    </div> 
                </div>
            <?php endforeach ?>
        </div>  
        

        <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/js/bootstrap.bundle.min.js" integrity="sha384-b5kHyXgcpbZJO/tY9Ul7kGkf1S0CWuKcCD38l8YkeH8z8QjE0GmW1gYU5S9FOnJ0" crossorigin="anonymous"></script>
    </body>    
</html>
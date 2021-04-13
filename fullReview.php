<?php
    session_start();
    require 'header.php';
    require 'ProjectFunctions.php';

    if($_GET)
    {
        $reviewId = filter_input(INPUT_GET, 'review', FILTER_VALIDATE_INT);
        $gameId = filter_input(INPUT_GET, 'game', FILTER_VALIDATE_INT);

        $query = "SELECT  r.id AS id, r.review AS review, r.score AS score, r.user_id AS user_id, r.game_id AS game_id, r.date_posted AS date_posted, u.username AS username 
            FROM reviews r JOIN user u ON u.id = user_id WHERE r.id = :ReviewId";
        $statement = $db->prepare($query); 
        $statement->bindValue(":ReviewId", $reviewId);
        $statement->execute();

        
        //$statement->execute();

        if(!isset($_SESSION['token']) || $_SESSION['expiry'] <= strtotime(date('Y/m/d')))
        {
            // Create array to send in initial post to gain an access token
            $data = array(
                'client_id' => 'dnk3ybvozhyxj1fsck4crna182t1yy',
                'client_secret' => 'jpfcm95gboogaf4c5cqa8v763vzqy7',
                'grant_type' => 'client_credentials'
            );   

            $_SESSION['token'] = getToken($data, 'https://id.twitch.tv/oauth2/token');
            $_SESSION['expiry'] = (strtotime(date('Y/m/d')) + $_SESSION['token']['expires_in']);
        }

        $token = $_SESSION['token']['access_token'];

            // Start building query
            $header = array
                        (
                            'Content-Type: application/application/x-www-form-urlencoded\r\n',
                            'Client-ID: dnk3ybvozhyxj1fsck4crna182t1yy',
                            'Authorization: Bearer ' .  $token
                        );

            //$body = 'fields date, game.*; where date > ' . strtotime('-14 days') . ';';
        $body = "fields id, name, cover.image_id, age_ratings.rating, first_release_date, genres.name, summary, platforms.name; 
                    sort first_release_date asc; 
                        where id = {$gameId};";
            
        $post = array
                ('http' => array
                            (
                                'method'  => 'POST',
                                'header'  => $header,
                                'content' => $body
                            )
                );

        $context  = stream_context_create($post);
        $json = file_get_contents('https://api.igdb.com/v4/games', false, $context);
        $game = json_decode($json, true)[0]; 
    }
?>

<link href="styleSingle.css" rel="stylesheet">
        <div class="container gameData" id="game">
            <div class="image reviewPage">
                <?php if(array_key_exists('cover', $game)) : ?>
                    <?php $image = 'https://images.igdb.com/igdb/image/upload/t_cover_big/' . $game['cover']['image_id'] . '.jpg' ?>
                <?php else : ?>
                    <?php $image = 'placeholder.png' ?>
                <?php endif ?>
                <img src=<?= $image?> alt="Boxart">  
            </div>                        
            <div class="gameData reviewPage">  
                <div class="quickdata title">
                    <h4><?= $game['name']?></h4> 
                </div>      
                <div class="quickdata">                       
                    <?php if(isset($game['genres'])) : ?>      
                        <h5><span class="title">Genres:</span> <?=  commaList($game['genres'], 'name') ?> </h5>          
                    <?php else : ?>
                        <h5><span class="title">Genres:</span> No Genres Listed </h5>
                    <?php endif ?>
                    <h5><span class="title">Platforms: </span> <?= commaList($game['platforms'], 'name') ?> </h5>                        
                    <h5><span class="title">Average Score: </span> 10/10</h5>
                </div>   
                <div class="quickdata">      
                    <h5><span class="title">Original Release: </span> <?= date('F d, Y', $game['first_release_date'])?></h5> 
                    <?php if(!array_key_exists('age_ratings', $game)): ?>
                        <h5><span class="title">Age Rating:</span> [Unrated]</h5>
                    <?php else: ?>
                        <h5><span class="title">Age Rating:</span> <?= getRating($game['age_ratings'][0]['rating']) ?></h5>
                    <?php endif  ?>                        
                </div>                                 
            </div>                      
            <div class = "fullReview">
                <?php while($row = $statement->fetch()) : ?>
                    <div class="review full">
                        <div class= "reviewHeader">
                                <h1><?=$row['score']?>/10</h1>
                            <div>
                                <h5><a href="user_profile.php?user=<?=$row['user_id']?>"><?=$row['username']?></a></h5>
                                <h6><?= date('F d, Y', strtotime($row['date_posted']))?></h6>
                            </div>
                        </div>                        
                        <div class = "reviewContent">
                            <p><?=$row['review']?></p>                                                       
                        </div>
                        <?php if(isset($_SESSION['user'])) :?>
                            <?php if($_SESSION['user']['role'] == 1 || $_SESSION['user']['role'] == 2) : ?>
                                <div class="reviewFooter">
                                    </span> <a href="delistReview.php?review=<?= $row['id']?>">Delist review</a>                            
                                </div>
                            <?php endif ?>
                        <?php endif ?>
                    </div>
                <?php endwhile ?>
            </div>
        </div> 

        <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/js/bootstrap.bundle.min.js" integrity="sha384-b5kHyXgcpbZJO/tY9Ul7kGkf1S0CWuKcCD38l8YkeH8z8QjE0GmW1gYU5S9FOnJ0" crossorigin="anonymous"></script>
    </body>    
</html>
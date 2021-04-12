<!-----------
 * Author: Derek Baert
 * Date: March 04, 2021
 * File: Specific game page
 ------------>

<?php
    session_start();
    require 'header.php';
    require 'ProjectFunctions.php';    
    $order = "date_posted ASC";
    if($_POST)
    {
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);                
        
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
        $body = 'fields id, name, cover.image_id, age_ratings.rating, first_release_date, genres.name, summary, platforms.name; 
                    sort first_release_date asc; 
                        where id = ' . $id . ';';
            
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

        if(isset($_POST['order']))
        {
            $order = filter_input(INPUT_POST, "order", FILTER_SANITIZE_STRING);
        }
            
        $query= "SELECT  r.id AS id, r.review AS review, r.score AS score, r.user_id AS user_id, r.game_id AS game_id, r.date_posted AS date_posted, u.username AS username 
                 FROM reviews r 
                 JOIN user u ON u.id = user_id
                 WHERE game_id = :GameId AND visible = 1
                 ORDER BY :Order";

        $statement = $db->prepare($query); 
        $statement->bindValue(":GameId", $id);
        $statement->bindValue(":Order", $order);
        $statement->execute();        
    }    

    //var_dump($statement->fetch());
?>
        <link href="styleSingle.css" rel="stylesheet">
        <div class="container" id="game">
            <div class="image">
                <?php if(array_key_exists('cover', $game)) : ?>
                    <?php $image = 'https://images.igdb.com/igdb/image/upload/t_cover_big/' . $game['cover']['image_id'] . '.jpg' ?>
                <?php else : ?>
                    <?php $image = 'placeholder.png' ?>
                <?php endif ?>
                <img src=<?= $image?> alt="Boxart">                
            </div> 
            <div class="quickdata title">
                <h4><?= $game['name']?></h4> 
            </div>            
            <div class="gameData">        
                <div class="quickdata">                       
                    <?php if(isset($game['genres'])) : ?>      
                        <h5><span class="title">Genres:</span> <?=  commaList($game['genres'], 'name') ?> </h5>          
                    <?php else : ?>
                        <h5><span class="title">Genres:</span> No Genres Listed </h5>
                    <?php endif ?>
                    <h5><span class="title">Platforms: </span> <?= commaList($game['platforms'], 'name') ?> </h5>                        
                    <h5><span class="title">Average Score: </span> <?=reviewAverage($game['id'], $db)?></h5>
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
            <div class ="quickdata description">
                    <p><?= $game['summary'] ?></p>
            </div>            
            <div class="addReview"> 
                <?php if(isset($_SESSION['user'])) : ?>
                    <?php if($_SESSION['user']['role'] != 4) : ?>
                        <form action="postReview.php" method=post class="postReview">                  
                            <input type="hidden" id="id" name="id" value="<?= $game['id'] ?>" />
                            <input type="submit" name=<?= $game['id'] ?> class="btn btn-primary" value="Post Review"/>
                        </form> 
                    <?php else :?>  
                        <div class ="quickdata message">
                            <p>Your account has been suspended, you cannot post reviews.</p>
                        </div> 
                    <?php endif ?>
                <?php else: ?>
                    <div class ="quickdata message">
                        <p>Must be a registered user to post reviews.</p>
                        <p class='registerLogin'><a href='createAccount.php'>Register</a> or <a href='login.php'>Login</a> here.</p>
                    </div>                    
                <?php endif ?>      
                <form class="changeOrder" action="gamePage.php" method="post">
                    <select name="order" id="order">
                        <option value="date_posted ASC">Date Posted Ascending</option>
                        <option value="date_posted DESC">Date Posted Descending</option>
                        <option value="score ASC">Review Score Ascending</option>
                        <option value="score DESC">Review Score Descending</option>
                    </select>
                    <input type="hidden" name="id" value="<?= $game['id'] ?>">
                    <input type="submit" name=<?= $game['id'] ?> class="btn btn-primary" value="Submit"/>
                </form>
            </div>
            <div class = "reviews">
                <?php while($row = $statement->fetch()) : ?>
                    <div class="review">
                        <div class= "reviewHeader">
                                <h1><?=$row['score']?>/10</h1>
                            <div>
                                <h5><a href="user_profile.php?user=<?=$row['user_id']?>"><?=$row['username']?></a></h5>
                                <h6><?= date('F d, Y', strtotime($row['date_posted']))?></h6>
                            </div>
                        </div>                        
                        <div class = "reviewContent">
                            <?php if(strlen($row['review']) > 200) : ?>
                                <p><?= substr($row['review'], 0, 200)?>...</p> 
                            <?php else : ?>   
                                <p><?=$row['review']?></p>
                            <?php endif ?>                                                        
                        </div>
                        <div class="reviewFooter">
                            </span> <a href="fullReview.php?review=<?= $row['id']?>&game=<?=$row['game_id']?>">See full review</a>                            
                        </div>
                    </div>
                <?php endwhile ?>
            </div>
        </div> 

        <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/js/bootstrap.bundle.min.js" integrity="sha384-b5kHyXgcpbZJO/tY9Ul7kGkf1S0CWuKcCD38l8YkeH8z8QjE0GmW1gYU5S9FOnJ0" crossorigin="anonymous"></script>
    </body>    
</html>
<!-----------
 * Author: Derek Baert
 * Date: March 04, 2021
 * File: New releases page
 ------------>

<?php          

    session_start();

    require 'ProjectFunctions.php'; 
    require 'header.php';

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
    $body = 'fields id, name, cover.image_id, age_ratings.rating, first_release_date, genres.name, platforms.name; 
                sort first_release_date desc; 
                    where first_release_date > ' . strtotime('-14 days') . ' & first_release_date < ' . strtotime(date(('Y-m-d'))) . '; 
                        limit 24;';
    
    $post = array
    ('http' =>
        array
        (
            'method'  => 'POST',
            'header'  => $header,
            'content' => $body
        )
    );

    $context  = stream_context_create($post);
    $json = file_get_contents('https://api.igdb.com/v4/games', false, $context);
    $games = json_decode($json, true);

     //var_dump($games);

    //var_dump($games[5]['age_ratings']);
    //echo array_key_exists('age_ratings', $games[5]);
?>
        <h2>New releases</h2>
        <div class="container" id="games">
            <?php foreach ($games as $game): ?>
                <div class="game">
                    <?php if(array_key_exists('cover', $game)) : ?>
                        <?php $image = 'https://images.igdb.com/igdb/image/upload/t_cover_big/' . $game['cover']['image_id'] . '.jpg' ?>
                    <?php else : ?>
                        <?php $image = 'placeholder.png' ?>
                    <?php endif ?>
                    <div class="thumbnail"><img src=<?= $image ?> alt="Boxart"></div>  
                    <div class="quickdata">
                        <h4><?= $game['name']?></h4>
                        <h5>Release Date: <?= date('F d, Y', $game['first_release_date'])?></h5> 
                            <h5>Platforms: <?= commaList($game['platforms'], 'name') ?> </h5> 
                        <?php if(!array_key_exists('age_ratings', $game)): ?>
                            <h5>Age Rating: [Unrated]</h5>
                        <?php else: ?>
                            <h5>Age Rating: <?= getRating($game['age_ratings'][0]['rating']) ?></h5>
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
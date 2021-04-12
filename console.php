<!-----------
 * Author: Derek Baert
 * Date: March 04, 2021
 * File: Console specific page
 ------------>

 <?php

    if($_GET)
    {
        $consoleId = FILTER_INPUT(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        $console = FILTER_INPUT(INPUT_GET, 'name', FILTER_SANITIZE_STRING);         
        session_start();

        require 'ProjectFunctions.php';
        require 'header.php';

        $pageOffset = 0;

        if(isset($_GET['offset']))
        {
            $pageOffset = FILTER_INPUT(INPUT_GET, 'offset', FILTER_VALIDATE_INT);
        }

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


        $body = 
        "query games/count \"Count\" 
        {
            where release_dates.platform = {$consoleId};
        };

        query games \"Games\" 
        {
            fields id, name, parent_game, version_parent, cover.image_id, age_ratings.rating, first_release_date, genres.name, platforms.name; 
                sort name asc; 
                    where release_dates.platform = {$consoleId}; 
                        limit 99; offset {$pageOffset}; 
        };";

        /*$body = "fields id, name, parent_game, version_parent, cover.image_id, age_ratings.rating, first_release_date, genres.name, platforms.name; 
                    sort name asc; 
                        where release_dates.platform = {$consoleId}; 
                            limit 99; offset {$pageOffset};";*/
        
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
        $json = file_get_contents('https://api.igdb.com/v4/multiquery', false, $context);
        $games = json_decode($json, true);     
    }  
    else
    {
        header("Location: index.php");
    }     

    //var_dump($games[0]['count']);
    //var_dump($games[1]['result']);
    $count = $games[0]['count'];
?>        
        <div class="container" id="games">
            <h2><?=$console?></h2>
            <nav aria-label="Page navigation example">
                <ul class="pagination">
                <?php if($pageOffset == 0) :?>
                    <li class="page-item disabled" id="previousButton"><a class="page-link" href="console.php">Previous</a></li>
                <?php else : ?>
                    <li class="page-item" id="previousButton"><a class="page-link" href="console.php?offset=<?=($pageOffset-100)?>&id=<?=$consoleId?>&name=<?=$console?>">Previous</a></li>
                <?php endif?>
                    <li class="page-item" id="nextButton"><a class="page-link" href="console.php?offset=<?=($pageOffset+100)?>&id=<?=$consoleId?>&name=<?=$console?>">Next</a></li>
                </ul>
            </nav>
            <?php foreach ($games[1]['result'] as $game): ?>
                <?php if(array_key_exists('first_release_date', $game)): ?>
                    <?php if(!isset($game['parent_game']) && !isset($game['version_parent'])) : ?>
                        <div class="game">
                            <?php if(array_key_exists('cover', $game)) : ?>
                                <?php $image = 'https://images.igdb.com/igdb/image/upload/t_cover_big/' . $game['cover']['image_id'] . '.jpg' ?>
                            <?php else : ?>
                                <?php $image = 'placeholder.png' ?>
                            <?php endif ?>
                            <div class="thumbnail"><img src=<?= $image ?> alt="Boxart"></div>
                            <div class="quickdata">
                                <?php if(strlen($game['name']) > 25) :?>
                                    <h4><?=substr($game['name'], 0, 50)?>...</h4>
                                <?php else : ?>
                                    <h4><?= $game['name']?></h4> 
                                <?php endif ?>                                
                                <h5>Release Date: <?= date('F d, Y', $game['first_release_date'])?></h5>                                         
                                <?php if(!array_key_exists('age_ratings', $game)): ?>
                                    <h5>Age Rating: [Unrated]</h5>
                                <?php else: ?>
                                    <h5>Age Rating: <?= getRating($game['age_ratings'][0]['rating']) ?></h5>
                                <?php endif  ?>   
                                <h5>Average Score: <?=reviewAverage($game['id'], $db)?></h5>
                            </div>
                            <div class="addReview"> 
                                <form action="gamePage.php" method="post">  
                                    <input type="hidden" id="id" name="id" value="<?= $game['id'] ?>" />
                                    <input type="submit" name="View Reviews" class="btn btn-primary" value="View Reviews"/>
                                </form>                
                            </div> 
                        </div>
                    <?php endif ?>
                <?php endif ?>
            <?php endforeach ?>
            <nav aria-label="Page navigation example">
                <ul class="pagination">
                <?php if($pageOffset == 0) :?>
                    <li class="page-item disabled" id="previousButton"><a class="page-link" href="console.php">Previous</a></li>
                <?php else : ?>
                    <li class="page-item" id="previousButton"><a class="page-link" href="console.php?offset=<?=($pageOffset-100)?>&id=<?=$consoleId?>&name=<?=$console?>">Previous</a></li>
                <?php endif?>
                <?php if($pageOffset >= $count) : ?>
                    <li class="page-item disabled" id="previousButton"><a class="page-link" href="console.php">Next</a></li>
                <?php else : ?>
                    <li class="page-item" id="nextButton"><a class="page-link" href="console.php?offset=<?=($pageOffset+100)?>&id=<?=$consoleId?>&name=<?=$console?>">Next</a></li>
                <?php endif ?>
                </ul>
            </nav>
        </div>        

        <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/js/bootstrap.bundle.min.js" integrity="sha384-b5kHyXgcpbZJO/tY9Ul7kGkf1S0CWuKcCD38l8YkeH8z8QjE0GmW1gYU5S9FOnJ0" crossorigin="anonymous"></script>
    </body>    
</html>
<!-----------
 * Author: Derek Baert
 * Date: March 21, 2021
 * File: Results for a search by game name or platform
 ------------>

<?php
    session_start();

    if($_POST && empty($_POST['search']))
    {
        header('Location: index.php');
    }
    require 'header.php';
    require 'ProjectFunctions.php';    

    if($_GET)
    {
        $pageOffset = FILTER_INPUT(INPUT_GET, 'offset', FILTER_VALIDATE_INT);
        $search = FILTER_INPUT(INPUT_GET, 'search', FILTER_SANITIZE_STRING);
        $category = FILTER_INPUT(INPUT_GET, 'category', FILTER_SANITIZE_STRING);
    }

    if($_POST)
    {        
        $pageOffset = 0;
        $category = filter_input(INPUT_POST, 'category', FILTER_SANITIZE_STRING);
        $search = filter_input(INPUT_POST, 'search', FILTER_SANITIZE_STRING); 
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

    $body = '';
    $url = '';

    if($category == 'game')
    {
        $body = "fields id, name, cover.image_id, age_ratings.rating, first_release_date, genres.name, platforms.name; 
                    search \"{$search}\"; limit 24; offset {$pageOffset};";

        $url = 'https://api.igdb.com/v4/games';
    }
    else
    {
        $body = "fields id, name, platform_logo.image_id, summary, generation; 
                    search \"{$search}\"; 
                        limit 24; offset {$pageOffset};";

        $url = 'https://api.igdb.com/v4/platforms';
    }

    /*if($category == 'game')
    {
        $body = 
        "query games/count \"Count\"
        {
            search \"{$search}\";
        };

        query games \"Results\"
        {
            fields id, name, cover.image_id, age_ratings.rating, first_release_date, genres.name, platforms.name; 
                search \"{$search}\"; limit 24; offset {$pageOffset};
        };";
    }
    else
    {
        $body = 
        "query platforms/count \"Count\" 
        {
            search \"{$search}\";
        };
        
        query platforms \"Results\"
        {
            fields id, name, platform_logo.image_id, summary, generation; 
                search \"{$search}\"; 
                    limit 24; offset {$pageOffset};
        };";        
    }

    $url = 'https://api.igdb.com/v4/multiquery';*/
        
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
    $json = file_get_contents($url, false, $context);
    $results = json_decode($json, true);  

    $noResults = "";

    if(count($results) == 0)
    {
        if($_GET)
        {
            $noResults = "No more results for \"{$search}\"";
        }
        else if($_POST)
        {
            $noResults = "No results for \"{$search}\"";
        }
    }
?>

<div class="container" id="games">
    <?php if(count($results) != 0) : ?>
        <h2>Results for "<?=$search?>" </h2>
    <?php else : ?>
        <h2><?= $noResults ?></h2>
    <?php endif ?>
    <nav aria-label="Page navigation example">
        <ul class="pagination">
            <?php if($pageOffset == 0) :?>
                <li class="page-item disabled" id="previousButton"><a class="page-link" href="search.php">Previous</a></li>
            <?php else : ?>
                <li class="page-item" id="previousButton"><a class="page-link" href="search.php?offset=<?=($pageOffset-25)?>&search=<?=$search?>&category=<?=$category?>">Previous</a></li>
            <?php endif?>
                <li class="page-item" id="nextButton"><a class="page-link" href="search.php?offset=<?=($pageOffset+25)?>&search=<?=$search?>&category=<?=$category?>">Next</a></li>
        </ul>
    </nav>    
    <?php if($category == 'game') : ?>
        <?php $i = 1?>
        <?php foreach ($results as $game): ?>   
            <?php if(array_key_exists('first_release_date', $game)): ?>         
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
                        <form action="gamePage.php" method="post">  
                            <input type="hidden" id="ReviewId<?=$i?>" name="id" value="<?= $game['id'] ?>" />
                            <input type="submit" name="View Reviews" class="btn btn-primary" value="View Reviews"/>
                        </form>                
                    </div>
                </div>    
                <?php $i .= 1 ?>
            <?php endif ?>
        <?php endforeach ?>              
    <?php else : ?>
        <?php foreach ($results as $platform): ?>
            <div class="platform">
                <?php if(array_key_exists('platform_logo', $platform)) : ?>
                    <?php $image = 'https://images.igdb.com/igdb/image/upload/t_thumb/' . $platform['platform_logo']['image_id'] . '.jpg' ?>
                <?php else : ?>
                    <?php $image = 'placeholder.png' ?>
                <?php endif ?>
                <div class="thumbnail"><img src=<?= $image ?> alt="Boxart"></div>  
                <div class="quickdata">
                    <h4><?= $platform['name']?></h4>          
                </div>
                <div class="viewGames">   
                    <button onclick="location.href='console.php?id=<?=$platform['id']?>&name=<?=$platform['name']?>'" type="button" class="btn btn-primary">View Games</button>             
                </div>
            </div>
        <?php endforeach ?>
    <?php endif ?>
    <nav aria-label="Page navigation example">
        <ul class="pagination">
            <?php if($pageOffset == 0) :?>
                <li class="page-item disabled" id="previousButton"><a class="page-link" href="search.php">Previous</a></li>
            <?php else : ?>
                <li class="page-item" id="previousButton"><a class="page-link" href="search.php?offset=<?=($pageOffset-25)?>&search=<?=$search?>&category=<?=$category?>">Previous</a></li>
            <?php endif?>
                <li class="page-item" id="nextButton"><a class="page-link" href="search.php?offset=<?=($pageOffset+25)?>&search=<?=$search?>&category=<?=$category?>">Next</a></li>
        </ul>
    </nav> 
</div>     
        <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/js/bootstrap.bundle.min.js" integrity="sha384-b5kHyXgcpbZJO/tY9Ul7kGkf1S0CWuKcCD38l8YkeH8z8QjE0GmW1gYU5S9FOnJ0" crossorigin="anonymous"></script>
    </body>    
</html>
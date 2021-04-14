<?php
    require 'ProjectFunctions.php';
    require 'header.php';

    if($_GET)
    {        
        $pageOffset = $pageOffset = FILTER_INPUT(INPUT_GET, 'offset', FILTER_VALIDATE_INT);
    }
    else
    {
        $pageOffset = 0;
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

    $body = "fields id, name, platform_logo.image_id, summary, generation; 
        limit 24; offset {$pageOffset};";

    $url = 'https://api.igdb.com/v4/platforms';
        
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
?>
<div class="container" id="games">
    <h2>All Consoles</h2>
    <nav aria-label="Page navigation example">
        <ul class="pagination">
            <?php if($pageOffset == 0) :?>
                <li class="page-item disabled" id="previousButton"><a class="page-link" href="allConsoles.php">Previous</a></li>
            <?php else : ?>
                <li class="page-item" id="previousButton"><a class="page-link" href="allConsoles.php?offset=<?=($pageOffset-25)?>">Previous</a></li>
            <?php endif?>
                <li class="page-item" id="nextButton"><a class="page-link" href="allConsoles.php?offset=<?=($pageOffset+25)?>">Next</a></li>
        </ul>
    </nav> 
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
    <nav aria-label="Page navigation example">
        <ul class="pagination">
            <?php if($pageOffset == 0) :?>
                <li class="page-item disabled" id="previousButton"><a class="page-link" href="allConsoles.php">Previous</a></li>
            <?php else : ?>
                <li class="page-item" id="previousButton"><a class="page-link" href="allConsoles.php?offset=<?=($pageOffset-25)?>">Previous</a></li>
            <?php endif?>
                <li class="page-item" id="nextButton"><a class="page-link" href="allConsoles.php?offset=<?=($pageOffset+25)?>">Next</a></li>
        </ul>
    </nav> 
</div>     
        <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/js/bootstrap.bundle.min.js" integrity="sha384-b5kHyXgcpbZJO/tY9Ul7kGkf1S0CWuKcCD38l8YkeH8z8QjE0GmW1gYU5S9FOnJ0" crossorigin="anonymous"></script>
    </body>    
</html>
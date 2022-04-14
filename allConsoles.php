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

    // Start building query. This requires three parameters: The Content Type, my Twitch Client ID, and the Token which is stored as a session variable previously.
    $header = array
            (
                'Content-Type: application/application/x-www-form-urlencoded\r\n',
                'Client-ID: dnk3ybvozhyxj1fsck4crna182t1yy',
                'Authorization: Bearer ' .  $token
            );

    // The body section of the post request specifies what properties of each game or console need to be retrieved. 
    $body = "fields id, name, platform_logo.image_id, summary, generation; 
        limit 24; offset {$pageOffset};";

    // The $url variable states what category will be accessed through the API. In this example, it is pulling from the table of game consoles in the database
    $url = 'https://api.igdb.com/v4/platforms';
        
    // Now we can put this all together as an array to send in the post request and store the results in an array. 
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
    <!-- Now that we have the results stored in an array, we can loop through each result and use the data to create an entry on the page. !-->
<?php foreach ($results as $platform): ?>
        <div class="platform">
        <!-- First I check if there is an image link stored that i can use. If there is, I construct the link so it can be displayed in the entry. If not, then a placeholder is used. !-->
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
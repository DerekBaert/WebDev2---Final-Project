<?php
    session_start();
    require 'ProjectFunctions.php'; 
    require 'header.php';

    /* Upon first loading the page, the php checks if the user has a token from the IGDB API stored as a session variable. 
        Their API uses this token for authentification purposes. If a token is not set, or the token has expired, then it will send a post request to retrieve a token, and store both the token
        and the expiry date/time in session variables. */
    if(!isset($_SESSION['token']) || $_SESSION['expiry'] <= strtotime(date('Y/m/d')))
    {
        // Create array to send in initial post to gain an access token
        $data = array(
            'client_id' => 'dnk3ybvozhyxj1fsck4crna182t1yy',
            'client_secret' => 'jpfcm95gboogaf4c5cqa8v763vzqy7',
            'grant_type' => 'client_credentials'
        );   

        // Since the process of getting a token is used on each page, a function was created and stored in  "ProjectFunctions.php".
        $_SESSION['token'] = getToken($data, 'https://id.twitch.tv/oauth2/token');
        $_SESSION['expiry'] = (strtotime(date('Y/m/d')) + $_SESSION['token']['expires_in']);
    }
    
    // Now that we have a token stored as a session variable, let's store it as a local variable for easier reference.

    $token = $_SESSION['token']['access_token'];

    // Now we start building the query to retrieve the data from the database. This requires three parameters: The Content Type, my Twitch Client ID, and the Token which is stored as a session variable previously.
    $header = array
            (
                'Content-Type: application/application/x-www-form-urlencoded\r\n',
                'Client-ID: dnk3ybvozhyxj1fsck4crna182t1yy',
                'Authorization: Bearer ' .  $token
            );

    /* Next, we need to specify what data we want to retrieve. First, we specify what properties or columns we want. In this case, we are getting the games unique ID number, it's name, 
        the ID for the cover image, it's age rating, release dates, name of the genre, and name of the platforms it was released on. We also want to sort it from newest release to oldest, so the sort function is used as well
        Finally, for the main page, we only want to display games which were recently released, so we are filtering out games with release dates older than 2 weeks from today, 
        as well as games that haven't been released yet. As there are many games released, we are only limiting it to the 5 most recent, otherwise it might become too long during certain release windows.*/
    $body = 'fields id, 
                    name, 
                    cover.image_id, 
                    age_ratings.rating, 
                    first_release_date, 
                    genres.name, 
                    platforms.name; 
                        sort first_release_date desc; 
                            where first_release_date > ' . strtotime('-14 days') . ' & first_release_date < ' . strtotime(date(('Y-m-d'))) . '; 
                                limit 5;';
    

    // Now that the header and body of the post request have been constructed, it's time to put it all together in an array to send in the post request.
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

    // Now that the games are stored in an array, we make a call to my own datebase to retrieve the most recent reviews. This is thankfully much simpler as I don't need to go through an API.
    $query = "SELECT  r.id AS id, r.review AS review, r.score AS score, r.user_id AS user_id, r.game_id AS game_id, r.date_posted AS date_posted, u.username AS username 
                FROM reviews r JOIN user u ON u.id = user_id WHERE date_posted > " . strtotime('-14 days') . " AND visible = 1";
    $statement = $db->prepare($query); 
    $statement->execute();
?>

        <div class="container" id="indexContent">
            <div class="container index">
                <!-- With the results of our post request now stored in an array, we can iterate through an pull the data for each entry to use on our site. 
                    The program "Postman" was very useful here, as it allowed me to test some of my requests, as well as see how the array it returned was laid out. !-->
                <h2>New releases</h2>
                <?php foreach ($games as $game): ?>
                    <div class="game">
                        <!-- If an array key exists for the 'cover,' it uses the id for the image in a url for the image reference. 
                            If not, then a placeholder image saved in the same location as the .html file is used. !-->
                        <?php if(array_key_exists('cover', $game)) : ?>
                            <?php $image = 'https://images.igdb.com/igdb/image/upload/t_cover_big/' . $game['cover']['image_id'] . '.jpg' ?>
                        <?php else : ?>
                            <?php $image = 'placeholder.png' ?>
                        <?php endif ?>
                        <div class="thumbnail"><img src=<?= $image ?> alt="Boxart"></div> 
                        <!-- Since this is just a small preview on the main page, I wanted to cut down on clutter, so only the release date and average score are displayed here.!--> 
                        <div class="quickdata">
                            <h4><?= $game['name']?></h4>
                            <h5>Release Date: <?= date('F d, Y', $game['first_release_date'])?></h5>   
                            <h5>Average Score: <?=reviewAverage($game['id'], $db)?></h5>
                        </div>
                        <!-- Next, a button is added where the user can view the review page for the game itself, along with all of it's reviews. !-->
                        <div class="addReview"> 
                            <form action="gamePage.php?id=<?= $game['id'] ?>" method="post"> 
                                <input type="submit" name="View Reviews" class="btn btn-primary" value="View Reviews"/>
                            </form>                
                        </div> 
                    </div>                
                <?php endforeach ?>                
            </div>
            <div class="container index">
                <h2>Recent Reviews</h2> 
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
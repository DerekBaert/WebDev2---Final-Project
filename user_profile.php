<?php
    session_start();
    require 'header.php';

    $isUser = false;

    $userId = filter_input(INPUT_GET, 'user', FILTER_VALIDATE_INT);

    if(isset($_SESSION['user']))
    {
        if($_SESSION['user']['id'] === $userId)
        {
            $isUser = true;
        }        
    }    

    $query = "SELECT * FROM user JOIN user_images ON user_images.id = user.profile_picture WHERE user.id = " . $userId;
    $statement = $db->prepare($query); 
    $statement->execute();

    $row = $statement->fetchall();

    $reviewsQuery = "SELECT * FROM reviews WHERE user_id = " . $userId;
    $reviewsStatement = $db->prepare($reviewsQuery);
    $reviewsStatement->execute();
?>

<div class="container userInfo">
        <div class="userImage">
            <img src="profile_images/<?=$row[0]['medium']?>" alt="profile_image">
            <?php if($isUser) : ?>
                <input type="file"
            <?php endif ?>
        </div>
        <div class="userData">
            <h4><?=$row['0']['username']?></h4>    
            <p><span class="title">Reviews:</span> <?=$row['0']['number_of_reviews']?></p>
        </div>    
    </div>

<div class="reviews">
    <?php while($review = $reviewsStatement->fetch()) : ?>
        <div class="review">
            <div class= "reviewHeader">
                <h1><?=$review['score']?>/10</h1>
                <div>
                    <h6><?= date('F d, Y', strtotime($review['date_posted']))?></h6>
                </div>
            </div>                        
            <div class = "reviewContent">
                <?php if(strlen($review['review']) > 200) : ?>
                    <p><?= substr($review['review'], 0, 200)?>...</p> 
                <?php else : ?>   
                    <p><?=$review['review']?></p>
                <?php endif ?>                                                        
            </div>
            <div class="reviewFooter">
                <a href="fullReview.php?review=<?= $review['id']?>">See full review</a>                            
            </div>
        </div>        
    <?php endwhile?>
</div>    



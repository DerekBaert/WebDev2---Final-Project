<?php
    session_start();
    require 'header.php';
    require 'ProjectFunctions.php';
    use Gumlet\ImageResize;
    require 'ImageResize.php';    
    require 'ImageResizeException.php';

    $isUser = false;

    if($_GET)
    {
        $userId = filter_input(INPUT_GET, 'user', FILTER_VALIDATE_INT);
    }

    //$userId = filter_input(INPUT_POST, 'user', FILTER_VALIDATE_INT);

    if(isset($_SESSION['user']))
    {
        if(intval($_SESSION['user']['id']) === $userId)
        {
            $isUser = true;
        }        
    }        

    if($_POST)
    {        
        $image_upload = isset($_FILES['profile']) && ($_FILES['profile']['error'] === 0);
 
        if ($image_upload) 
        {
            $temp                 = explode(".", $_FILES["profile"]["name"]);     
            $image_filename       = round(microtime(true)) . '.' . end($temp);
            $temporary_path       = $_FILES['profile']['tmp_name'];
            $new_image_path       = file_upload_path($image_filename);            

            if (file_is_an_image($temporary_path, $new_image_path)) 
            {
                move_uploaded_file($temporary_path, $new_image_path);                 

                $withoutExt = preg_replace('/\\.[^.\\s]{3,4}$/', '', $image_filename);
                $fileExtension   = pathinfo($image_filename, PATHINFO_EXTENSION);

                $image = new ImageResize($new_image_path);
                $image->resizeToWidth(150);
                $image->save(file_upload_path($withoutExt . '_Medium' . '.' . $fileExtension));  

                $image = new ImageResize($new_image_path);
                $image->resizeToWidth(75);
                $image->save(file_upload_path($withoutExt . '_Thumbnail' . '.' . $fileExtension));  
                $imagePath = $withoutExt . '.' . $fileExtension;
                $validFile = true;
            }
            else
            {
                $validFile = false;
            }      

            if($validFile)
            {
                $imageFetch = "SELECT profile_picture, original, thumbnail, medium FROM user JOIN user_images ON user_images.id = user.profile_picture WHERE user.id = :UserId";
                $statement = $db->prepare($imageFetch);
                $statement->bindValue(':UserId', $userId); 
                $statement->execute();

                $oldImages = $statement->fetchall();

                $thumbnail = "{$withoutExt}_Thumbnail.{$fileExtension}";
                $medium = "{$withoutExt}_Medium.{$fileExtension}";
                $pictureId = $oldImages[0]["profile_picture"];

                echo $pictureId;

                $updateQuery = "UPDATE user_images SET original=:Original, thumbnail=:Thumbnail,  medium=:Medium WHERE id = :PictureId";
                $UpdateStatement = $db->prepare($updateQuery); 
                $UpdateStatement->bindValue(':Original', $imagePath);
                $UpdateStatement->bindValue(':Thumbnail', $thumbnail);
                $UpdateStatement->bindValue(':Medium', $medium);
                $UpdateStatement->bindValue(':PictureId', $pictureId);
                $UpdateStatement->execute();

                $_SESSION['user']['profile_picture'] = $thumbnail; 

                unlink("profile_images/{$oldImages[0]['original']}");
                unlink("profile_images/{$oldImages[0]['thumbnail']}");
                unlink("profile_images/{$oldImages[0]['medium']}");

                ?>
                <script type="text/javascript">
                    window.location.href = "user_profile.php?user=<?=$userId?>";
                </script>
                <?php
            }            
        }
    }

    $query = "SELECT * FROM user JOIN user_images ON user_images.id = user.profile_picture WHERE user.id = :UserId";
    $statement = $db->prepare($query);
    $statement->bindValue(':UserId', $userId); 
    $statement->execute();

    $row = $statement->fetchall();

    $reviewsQuery = "SELECT * FROM reviews WHERE user_id = :UserId";
    $reviewsStatement = $db->prepare($reviewsQuery);
    $reviewsStatement->bindValue(':UserId', $userId); 
    $reviewsStatement->execute();

    $validFile = false;
    //echo $row[0]["profile_picture"];
    //var_dump($_POST);
?>

<div class="container userInfo">
        <div class="userImage">
            <img src="profile_images/<?=$row[0]['medium']?>" alt="profile_image">
            <?php if($isUser) : ?>
            <form method='post' action="user_profile.php?user=<?=$userId?>" enctype="multipart/form-data">
                <label for="image">Upload New Profile Picture:</label>
                <input type="file" id="profile" name="profile">
                <input type="hidden" id="user" name="user" value=<?=$userId?>>
                <input type="submit" value="Submit" id="submitPhoto">
            </form>
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



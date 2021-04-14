<?php
    session_start();
    require 'ProjectFunctions.php';

    $returnPage = $_SERVER["HTTP_REFERER"];

    if($_GET)
    {
        $id = filter_input(INPUT_GET, 'review', FILTER_VALIDATE_INT);
    }

    if($_POST)
    {
        if($_POST['command'] == 'Update')
        {
            $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
            $review = filter_input(INPUT_POST, 'review', FILTER_SANITIZE_STRING);
            $returnPage = filter_input(INPUT_POST, 'returnPage', FILTER_SANITIZE_STRING);

            if(!isset($db))
            {
                $db = connect();
            }

            $score = filter_input(INPUT_POST, 'score', FILTER_VALIDATE_INT);

            $query = "UPDATE reviews SET review = :Review, score=:Score WHERE id = :ID";
            $statement = $db->prepare($query); 
            $statement->bindValue(':Review', $review);
            $statement->bindValue(':Score', $score);
            $statement->bindValue(':ID', $id);
            $statement->execute(); 

            header("location:{$returnPage}");
            exit(0);
        }
        else if($_POST['command'] == 'Delete')
        {
            $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

            if(!isset($db))
            {
                $db = connect();
            }

            if($id)
            {
                $query = "DELETE FROM reviews WHERE id = :ID";
                $statement = $db->prepare($query);
                $statement->bindValue(':ID', $id);
                $statement->execute();
            }
                        
            header("Location: user_profile.php?user={$_SESSION['user']['id']}");
            exit(0);
        }
        
    }

    require 'header.php';
?>

<form class='newReview' method='post' action="edit.php">
    <label for="review">Review:</label>
    <textarea id="review" name="review" rows='8'></textarea>
    <div class='reviewScore'>
        <select name="score" id="score">
            <option value="1">1</option>
            <option value="2">2</option>
            <option value="3">3</option>
            <option value="4">4</option>
            <option value="5">5</option>
            <option value="6">6</option>
            <option value="7">7</option>
            <option value="8">8</option>
            <option value="9">9</option>
            <option value="10">10</option>
        </select>
        <p>/10</p>
    </div>
    <input type="hidden" value="<?=$id?>" name="id">
    <input type="hidden" value="<?=$returnPage?>" name="returnPage">
    <input type="submit" name="command" value="Update" />
    <input type="submit" name="command" value="Delete" onclick="return confirm('Are you sure you wish to delete this post?')" />
</form>
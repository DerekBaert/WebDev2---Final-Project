<?php 
    session_start();
    
    

    if($_POST)
    {
        $gameId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        $review = filter_input(INPUT_POST, 'review', FILTER_SANITIZE_STRING);
        $score = $_POST['score'];

        $query = "INSERT INTO reviews (user_id, game_id, review, score) values (:UserId, :GameId, :Review, :Score)";
        $statement = $db->prepare($query); 
        $statement->bindValue(':UserId', $_SESSION['user']['id']);
        $statement->bindValue(':GameId', $gameId);
        $statement->bindValue(':Review', $review);
        $statement->bindValue(':Score', $score);
        $statement->execute(); 

        $query = "UPDATE user SET number_of_reviews = number_of_reviews + 1 WHERE id = " . $_SESSION['user']['id'];
        $statement = $db->prepare($query); 
        $statement->execute();

        header('Location: ' . $_SERVER['HTTP_REFERER']);
    }

    require 'header.php';
?>

<form class='newReview' method='post'>
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
    <input type="submit" value="Submit Review" id="submitReview">
</form>
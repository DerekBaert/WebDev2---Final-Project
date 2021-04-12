<?php
    session_start();
    require 'ProjectFunctions.php';
    require 'header.php';
    
    if($_GET)
    {
        $reviewId = filter_input(INPUT_GET, 'review', FILTER_VALIDATE_INT);
    }

    if($_POST)
    {
        $reviewId = filter_input(INPUT_POST, 'reviewId', FILTER_VALIDATE_INT);
        $reviewNotes = filter_input(INPUT_POST, 'notes', FILTER_VALIDATE_INT);

        $updateQuery = "UPDATE reviews SET visible = 0, admin_notes = :Notes WHERE id = :ID";
        $updateStatement= $db->prepare($updateQuery);
        $updateStatement->bindValue("ID", $reviewId);
        $updateStatement->bindValue("Notes", $reviewNotes);
        $updateStatement->execute();
        
        $count = $updateStatement->rowCount();
    }

?>

<form class='delist' method="post">
    <label for="notes">Reason for Delisting:</label>
    <textarea id="notes" name="notes" rows='8'></textarea>
    <input type="hidden" id="reviewId" name="reviewId" value="<?=$reviewId?>">
        <?php if($_POST) : ?>
            <?php if($count != 0) :?>
                <h6 class="invalidLogin">Delist successful.</h6>
            <?php else: ?>
                <h6 class="invalidLogin">Delist unsuccessful.</h6>
                <button type="submit" class="btn btn-outline-success">Submit</button>
            <?php endif ?>
        <?php else : ?>
            <button type="submit" class="btn btn-outline-success">Submit</button>
        <?php endif ?>      
</form>
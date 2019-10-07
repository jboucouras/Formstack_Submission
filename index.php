<?php
/*
This application was created to submit to Formstack by Jason Boucouras (myself) as part of my job application to be a Junior Software Developer at the Fisher's Formstack location.
This application is designed to help cover bands (such as wedding bands) manage the songs in their repertoire. Music is very near and dear to me, and I wanted to make a project I was passionate about. 
The user can store their documents (song records) using this application. The keys available are Song Name, Artist Name, Tempo in BPM, Key of Song, and Date Last performed.
For example, let's say user Kevin Malone is keeping track of all the songs that his wedding band, Scrantonicity, knows. Kevin could enter:
The name of the song, who the original artist is, what BPM Kevin's band performs it at, the key Kevin's band performs it in, and when Kevin's band last performed the song. 
Using this information, Kevin has a good insight on developing Scrantonicity's setlist at their next gig.   
*/
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Cover Band Setlist Manager</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
</head>
<body>
    <?php // requires the inclusion of process.php?>
    <?php require_once 'process.php'; ?>
    
    <?php
        //controls display of various messages
        if (isset($_SESSION['message'])): 
    ?>
    <div class="alert alert-<?=$_SESSION['msg-type']?>">
        <?= $_SESSION['message'];?>
    <?php
        unset($_SESSION['message']);
    ?>
    </div>
    <?php endif; ?>


    <div class="row justify-content-center">
        <h1>Cover Band Setlist Manager</h1>
    </div>
    
    
    <div class="row justify-content-center">
        <form action="process.php" method="POST">
            <input type="hidden" name="id" value="<?php echo $id; ?>">
            <div class="form-group">
                <label for="songname">Song Name</label>
                <input type="text" name="songname" class="form-control" id="songname" placeholder="Enter song name here" value="<?=$songname?>">
            </div>
            <div class="form-group">
                <label for="artistname">Artist Name</label>
                <input type="text" name="artistname" class="form-control" id="artistname" placeholder="Enter artist name here" value="<?=$artistname?>">
            </div>
            <div class="form-group">
                <label for="tempo">Tempo in BPM</label>
                <input type="number" name="tempo" class="form-control" id="tempo" placeholder="999" value="<?=$tempo?>">
            </div>
            <div class="form-group">
                <label for="songkey">Key of Song</label>
                <input type="text" name="songkey" class="form-control" id="songkey" placeholder="Enter song key here" value="<?=$songkey?>">
            </div>
            <div class="form-group">
                <label for="datelastperformed">Date Last Performed</label>
                <input type="date" name="datelastperformed" class="form-control" id="datelastperformed" placeholder="" value="<?=$datelastperformed?>">
            </div>
            <div class="form-group">
                <?php if ($is_update):?>
                    <input type="submit" name="update" class="btn btn-info" value="Update">
                <?php else: ?>
                    <input type="submit" name="submit" class="btn btn-primary" value="Submit">
                <?php endif; ?>
        </div>
    </form>
    </div>
    <div class="container">
    <?php
        //connects to the database and saves a query for all columns in the documents table to variable $result in order to display existing documents on the page
        $mysqli = new mysqli('localhost', 'root', 'root', 'songmanager') or die(mysqli_error($mysqli));
        $result = $mysqli->query("SELECT * from documents") or die($mysqli_error($mysqli));
    ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Document Name</th>
                    <th>Creation Date</th>
                    <th>Date Last Modified</th>
                    <th>Date Last Exported</th>
                    <th colspan="4">Action</th>
                </tr>
            </thead>
        <?php
            //while loop to display the existing documents on the page using fetch_assoc to fetch a result row
            while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['documentname']; ?></td>
                    <td><?= $row['datecreated']; ?></td>
                    <td><?= $row['lastmodified']; ?></td>
                    <td><?= $row['lastexported']; ?></td>
                    <td>
                        <a href="index.php?edit=<?= $row['documentID']; ?>"
                        class="btn btn-info">Edit</a>
                        <a href="process.php?export=<?= $row['documentID']; ?>"
                        class="btn btn-info">Export</a>
                        <a href="process.php?delete=<?= $row['documentID']; ?>"
                        class="btn btn-danger">Delete</a>
                    </td>
                </tr>
                <?php endwhile; ?>
        </table>    
 
    </div>
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
</body>
</html>
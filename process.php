<?php

session_start();
$mysqli = new mysqli('localhost', 'root', 'root', 'songmanager') or die(mysqli_error($mysqli));

//declares variables to blank, 0, and false values so that they can be manipulated and changed later to fit the circumstance
$is_update = FALSE;
$id = 0;
$songname = "";
$artistname = "";
$tempo = "";
$songkey = "";
$datelastperformed = "";


//process for submitting a new song/document
if (isset($_POST['submit'])){

    //preparing variables necessary. the text values have to have apostrophes replaced so that they don't cause errors with MySQL
    $key1 = "song_name";
    $key2 = "artist_name";
    $key3 = "tempo";
    $key4 = "song_key";
    $key5 = "date_last_performed";
    $value1= $_POST['songname'];
    $value1= str_replace("'", "\'", $value1);
    $value2 = $_POST['artistname'];
    $value2 = str_replace("'", "\'", $value2);
    $value3 = $_POST['tempo'];
    $value4 = $_POST['songkey'];
    $value4 = str_replace("'", "\'", $value4);
    $value5 = $_POST['datelastperformed'];
    
    //inserts document name and metadata into documents table for display
    $mysqli->query("INSERT INTO documents (documentname) VALUES ('$value1')") or die(mysqli_error($mysqli));

    //saves documentID to use as the foreign key for the keyvalue table
    $documentID = mysqli_insert_id($mysqli);

    //all queries for the keyvalue table
    $mysqli->query("INSERT INTO keyvalue (documentID, documentkey, documentvalue) VALUES ('$documentID', '$key1', '$value1')") or die(mysqli_error($mysqli));
    $mysqli->query("INSERT INTO keyvalue (documentID, documentkey, documentvalue) VALUES ('$documentID', '$key2', '$value2')") or die(mysqli_error($mysqli));
    $mysqli->query("INSERT INTO keyvalue (documentID, documentkey, documentvalue) VALUES ('$documentID', '$key3', '$value3')") or die(mysqli_error($mysqli));
    $mysqli->query("INSERT INTO keyvalue (documentID, documentkey, documentvalue) VALUES ('$documentID', '$key4', '$value4')") or die(mysqli_error($mysqli));
    $mysqli->query("INSERT INTO keyvalue (documentID, documentkey, documentvalue) VALUES ('$documentID', '$key5', '$value5')") or die(mysqli_error($mysqli));

    //posts a message letting the user know they successfully posted the song/document
    $_SESSION['message'] = "Song has been entered into the setlist manager.";
    $_SESSION['msg-type'] = "success";
    header("location: index.php");
}

//process for deleting a song/document
if (isset($_GET['delete'])){

    //interprets which song/document the user is trying to delete
    $id = $_GET['delete'];

    //query to delete the record
    $mysqli->query("DELETE FROM documents WHERE documentID=$id") or die(mysqli_error($mysqli)) or die(mysqli_error($mysqli));

    //posts a message letting the user know they deleted the song/document
    $_SESSION['message'] = "Song has been deleted from the setlist manager.";
    $_SESSION['msg-type'] = "danger";
    header("location: index.php");
}

//process for editing the song/document
if (isset($_GET['edit'])){
    //interprets which song/document the user is trying to edit
    $id = $_GET['edit'];

    //adds queries to variables, then uses fetch_array to turn the variable value into a usable value for the inputs to display the song/document that is being edited
    $songname = $mysqli->query("SELECT documentvalue FROM keyvalue WHERE documentID=$id AND documentkey='song_name'") or die(mysqli_error($mysqli));
    $songname = mysqli_fetch_array($songname)[0];
    $artistname = $mysqli->query("SELECT documentvalue FROM keyvalue WHERE documentID=$id AND documentkey='artist_name'") or die(mysqli_error($mysqli));
    $artistname = mysqli_fetch_array($artistname)[0];
    $tempo = $mysqli->query("SELECT documentvalue FROM keyvalue WHERE documentID=$id AND documentkey='tempo'") or die(mysqli_error($mysqli));
    $tempo = mysqli_fetch_array($tempo)[0];
    $songkey = $mysqli->query("SELECT documentvalue FROM keyvalue WHERE documentID=$id AND documentkey='song_key'") or die(mysqli_error($mysqli));
    $songkey = mysqli_fetch_array($songkey)[0];
    $datelastperformed = $mysqli->query("SELECT documentvalue FROM keyvalue WHERE documentID=$id AND documentkey='date_last_performed'") or die(mysqli_error($mysqli));
    $datelastperformed = mysqli_fetch_array($datelastperformed)[0];

    //posts a message letting the user know they are now editing the song/document
    $_SESSION['message'] = "You are now editing $songname.";
    $_SESSION['msg-type'] = "warning";
    $is_update = TRUE;
}

//process for updating the song/document
if (isset($_POST['update'])){
    //interprets which song/document the user is trying to update
    $id = $_POST['id'];
    
    //preparing variables necessary. the text values have to have apostrophes replaced so that they don't cause errors with MySQL
    $songname = $_POST['songname'];
    $songname = str_replace("'", "\'", $songname);
    $artistname = $_POST['artistname'];
    $artistname = str_replace("'", "\'", $artistname);
    $tempo = $_POST['tempo'];
    $songkey = $_POST['songkey'];
    $songkey = str_replace("'", "\'", $songkey);
    $datelastperformed = $_POST['datelastperformed'];
    
    //all queries for updating the keyvalue table
    $mysqli->query("UPDATE keyvalue SET documentvalue='$songname' WHERE documentID=$id AND documentkey='song_name'") or die(mysqli_error($mysqli));
    $mysqli->query("UPDATE keyvalue SET documentvalue='$artistname' WHERE documentID=$id AND documentkey='artist_name'") or die(mysqli_error($mysqli));
    $mysqli->query("UPDATE keyvalue SET documentvalue='$tempo' WHERE documentID=$id AND documentkey='tempo'") or die(mysqli_error($mysqli));
    $mysqli->query("UPDATE keyvalue SET documentvalue='$songkey' WHERE documentID=$id AND documentkey='song_key'") or die(mysqli_error($mysqli));
    $mysqli->query("UPDATE keyvalue SET documentvalue='$datelastperformed' WHERE documentID=$id AND documentkey='date_last_performed'") or die(mysqli_error($mysqli));

    //queries to update the document name as well as the last modified metadata
    $mysqli->query("UPDATE documents SET documentname='$songname' WHERE documentID=$id") or die(mysqli_error($mysqli));
    $mysqli->query("UPDATE documents SET lastmodified=CURRENT_TIMESTAMP WHERE documentID=$id") or die(mysqli_error($mysqli));

    //posts a message letting the user know they successfully updated the song/document
    $_SESSION['message'] = "Song has been updated.";
    $_SESSION['msg-type'] = "success";
    header("location: index.php");
}

//process for exporting the song/document
if (isset($_GET['export'])){

   //interprets which song/document the user is trying to export
   $id = $_GET['export'];

   //queries to select for exporting
   $documentname = $mysqli->query("SELECT documentname FROM documents WHERE documentID=$id") or die(mysqli_error($mysqli));
   $result = $mysqli->query("SELECT documentkey, documentvalue FROM keyvalue WHERE documentID=$id") or die(mysqli_error($mysqli));
   $datecreated = $mysqli->query("SELECT datecreated FROM documents WHERE documentID=$id") or die(mysqli_error($mysqli));
   //updates the $datecreated variable to be able to be used in the $times variable below
   $datecreated = mysqli_fetch_array($datecreated)[0];
   $lastmodified = $mysqli->query("SELECT lastmodified FROM documents WHERE documentID=$id") or die(mysqli_error($mysqli));
   //updates the $lastmodified variable to be able to be used in the $times variable below
   $lastmodified = mysqli_fetch_array($lastmodified)[0];

   //if statement to run if we pulled available data
    if ($result->num_rows > 0){
        $delimiter = ",";
        $filename = mysqli_fetch_array($documentname)[0] . ".csv";

        //sets the file pointer
        $f = fopen('php://output', 'w') or die("Unable to open file");
        
        //sets date created and last modified metadata at top of file
        $times = array($datecreated, $lastmodified);
        fputcsv($f, $times, $delimiter);

        //sets the column headers
        $fields = array('key', 'value');
        fputcsv($f, $fields, $delimiter);
        
        //sets the row data in the file using fputcsv
        while($row = $result->fetch_assoc()){
            $linedata = array($row['documentkey'], $row['documentvalue']);
            fputcsv($f, $linedata, $delimiter);
        }

        //headers to ensure download
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '";');
        fpassthru($f);
    }
    //updates the last exported metadata field
    $mysqli->query("UPDATE documents SET lastexported=CURRENT_TIMESTAMP WHERE documentID=$id");   
}

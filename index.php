
<?php
include 'config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
body {
font-family: Arial, sans-serif;
}
form {
max-width: 400px;
margin: 0 auto;
padding: 20px;
border: 1px solid #ccc;
background-color: #f9f9f9;
border-radius: 5px;
}
label {
display: block;
margin-bottom: 5px;
font-weight: bold;
}
input[type="text"], input[type="file"] {
width: 95%;
padding: 10px;
margin-bottom: 10px;
border: 1px solid #ccc;
border-radius: 5px;
}
input[type="submit"] {
background-color: #007bff;
color: white;
padding: 10px 20px;
border: none;
border-radius: 5px;
cursor: pointer;
}
input[type="submit"]:hover {
background-color: #0056b3;
}
table {
width: 100%;
border-collapse: collapse;
margin-top: 20px;
}
th, td {
border: 1px solid #ddd;
padding: 8px;
text-align: left;
}
th {
background-color: #f2f2f2;
}
img {
padding: 5px;
max-width: 200px;
max-height: 200px;
padding: 10px;
}
</style>
</head>
<body>

    <form action="" method="post" enctype="multipart/form-data">
         <label for="catname">Name</label>
         <input name="name"  type="text" class="form-control" id="catname" placeholder="Name" required><br>
         <label for="metadescription">Multipleimage Upload</label><br>
         <input type="file" class="form-control upload-btn" placeholder="File" name="img[]" id="attach" multiple/><br>
         <input type="submit" name="submit" value="Submit">
    </form>

<?php
    // Check if the form is submitted
if (isset($_POST['submit'])){
    // Retrieve form data
    $name = $_POST['name'];
    $filenames = $_FILES['img']['name'];
    $tmp_names = $_FILES['img']['tmp_name'];
    $folder = [];
    $flagUnsupportedFormat = false; 
    // Flag to track unsupported formats
    // Loop through each file and move it to the respective folder
    foreach ($filenames as $key => $filename) {
        $tmp_name = $tmp_names[$key];
        $folder[] = "./uploads/" . $filename;
        // Check if the file is an image
        $check = getimagesize($tmp_name);
        if ($check === false) {
            echo "File '$filename' is not an image.<br>";
            continue;
        // Skip this file and move to the next
        }
        // Check file format
        $allowedFormats = ["jpg", "jpeg", "png", "gif","webp","jfif"];
        $imageFileType = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        if (!in_array($imageFileType, $allowedFormats)) {
            echo "File '$filename' has an unsupported format. Only JPG, JPEG, PNG, and GIF files are allowed.<br>";
            $flagUnsupportedFormat = true; 
         // Set the flag
            continue;
         // Skip this file and move to the next
        }
        // Check file size (optional, set your desired size)
        $maxFileSize = 5 * 1024 * 1024; 
        // 5MB
        if ($_FILES['img']['size'][$key] > $maxFileSize) {
            echo "File '$filename' is too large. Maximum size allowed is 5MB.<br>";
            continue; 
        // Skip this file and move to the next
        }
        move_uploaded_file($tmp_name, $folder[$key]);
    }

    if (!$flagUnsupportedFormat) {
        $sql = "INSERT INTO images ( name , img ) VALUES ('$name', '" . implode(",", $folder) . "')";
        if ($conn->query($sql) !== TRUE) {
            echo "Error inserting record: " . $conn->error;
        }
    }
}
?>
<?php
// Display MultipleImages
// Retrieve data from the table
$sql = "SELECT * FROM images";
$result = $conn->query($sql);
$i = 1;
if ($result->num_rows > 0) {
    // Output the data in a table format
    echo "<table class='table'>
    
            <tr>
                <th>Id</th>
                <th>Name</th>
                <th>Image</th>
            </tr>";

    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                  <td>" . $i . "</td>
                  <td>" . $row["name"] . "</td>
                  <td>";

                  $imagePaths = explode(",", $row["img"]);
            
                    foreach ($imagePaths as $imagePath){
                        echo "<img src='" . $imagePath . "' alt='Image' width='200' height='200'>";
                    }

            echo "</td>

             </tr>";

        $i++;

    }
    echo "</table>";
} else{
    echo "No data found";
}

$conn->close();
?>

<script>
        if ( window.history.replaceState ) {
            window.history.replaceState( null, null, window.location.href );
        }
</script> 
</body>
</html>
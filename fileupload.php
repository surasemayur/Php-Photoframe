 <?php include("conn.php");
 //last upload image display code - added by mayurs
$sqllastupload = "SELECT * FROM images ORDER BY id DESC limit 1";
$res_lastuploadresult = mysqli_query($conn,  $sqllastupload);
$lastuploadimg =false;
if (mysqli_num_rows($res_lastuploadresult) > 0) {
    while ($images1 = mysqli_fetch_assoc($res_lastuploadresult)) {
            $lastuploadimg =$images1['image_url'];
    }
} 

if (isset($_POST['submit']) && isset($_FILES['my_image'])) {
    /*echo "<pre>";
    print_r($_FILES['my_image']);
    echo "</pre>";*/

    $img_name = $_FILES['my_image']['name'];
    $img_size = $_FILES['my_image']['size'];
    $tmp_name = $_FILES['my_image']['tmp_name'];
    $error = $_FILES['my_image']['error'];

    if ($error === 0) {
        if ($img_size > 125000) {
            $em = "Sorry, your file is too large.";
            header("Location: mtest.php?error=$em");
        }else {
            $img_ex = pathinfo($img_name, PATHINFO_EXTENSION);
            $img_ex_lc = strtolower($img_ex);
            $em = "Successfully Uploaded.";

            $allowed_exs = array("jpg", "jpeg", "png"); 

            if (in_array($img_ex_lc, $allowed_exs)) {
                $new_img_name = uniqid("IMG-", true).'.'.$img_ex_lc;
                $img_upload_path = 'uploads/'.$new_img_name;
                move_uploaded_file($tmp_name, $img_upload_path);

                // Insert into Database
                $sql = "INSERT INTO images(image_url) 
                        VALUES('$new_img_name')";
                mysqli_query($conn, $sql);
                 header("Location: mtest.php?success=$em");
            }else {
                $em = "You can't upload files of this type";
                header("Location: mtest.php?error=$em");
            }
        }
    }else {
        $em = "unknown error occurred!";
        header("Location: mtest.php?error=$em");
    }

}?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Canvas Preview Assignment</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css" integrity="sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ" crossorigin="anonymous">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: space-between;
            padding: 20px;
        }
        .container {
            display: flex;
            width: 100%;
        }
        .preview-container {
          max-height: 500px;
          max-width: 500px;
          border: 30px solid #ccc;
          border-image-slice: 10%;
          border-image-width: 10%;
          height: auto;
           width: auto;
          border-image-repeat: stretch;
        }

        .control-panel {
            width: 50%;
            padding: 10px;
        }
        .control-panel select, .control-panel input {
            width: 100%;
            padding: 8px;
            margin-bottom: 15px;
        }
        #canvas {
            border: 1px solid #ccc;
            width: auto;
            margin-top: -300px;
            margin-left: 30px;
            margin-bottom: 30px;
            height: auto;
        }
        .frame-options img {
            width: 60px;
            height: 60px;
            cursor: pointer;
        }
        .image-preview img {
            width: 100%;
            max-height: auto;
            cursor: pointer;
        }
    </style>
</head>
<body>

<div class="container">
    <!-- Preview (Left Side) -->
    <div class="preview-container">
        <div style="width:auto;height: auto;
            padding: 258px 230px 158px 30px;
            border: 1px solid #ccc;
            background-color: red;">
            <canvas id="canvas" style=" background-color: unset;"></canvas>
        </div>
        
    </div>

    <!-- Control Panel (Right Side) -->
    <div class="control-panel">
        <label for="sizeInput1"><strong>Step 1 - Enter your Photo Width x Height : </strong></label>
        <input type="text" id="sizeInput1" placeholder="Width" style="width:70px">&nbsp;&nbsp;x&nbsp;&nbsp;<input type="text" id="sizeInput2" style="width:70px" placeholder="Height">
        <br/>
        <label for="imageLayer"><strong>Step 2 - Choose Your Photo or Upload your own Photo : </strong></label>
        <div class="image-preview"><br/>
            <?php 
             
              $sql = "SELECT * FROM images ORDER BY id DESC";
              $res = mysqli_query($conn,  $sql);
                if(mysqli_num_rows($res) > 0) {
                    while ($images = mysqli_fetch_assoc($res)) {  
                        ?>
                        <img src="uploads/<?=$images['image_url']?>" alt="Nature 1" style='background-color: #ececec; border: 2px solid #ccc;width: 100px;height: 100px;' onclick="changeImage('uploads/<?=$images['image_url']?>')"/>
                    <?php 
                    } 
                }?>
            <!-- <input type="file" id="imageLayer" onchange="uploadImage(event)"/> -->
        </div>
        <br/>
        <br/>
        <form action=" " method="post" enctype="multipart/form-data">
                <input type="file" class="form-control" name="my_image" onchange="uploadImage(event)"/><br/>
                <input type="submit" name="submit"value="Upload" class="btn btn-primary btn-mini"/>
        </form>
        <?php if (isset($_GET['error'])){
                            ?>
                <span  class="alert alert-msg" style="color:red;margin-top: 100px;">&nbsp;&nbsp;&nbsp;<?php echo $_GET['error']; ?></span><br/><br/><br/>
                    <?php
                }elseif (isset($_GET['success'])){
                    ?>
                    <span class="alert alert-msg" style="color:darkgreen;margin-top: 100px;">&nbsp;&nbsp;<?php echo $_GET['success']; ?></span>
                    <?php
                } ?>
        <br/><br/>
        <label for="frameLayer"><strong>Step 3 - Choose Your Photo Frame : </strong></label>
        <div class="frame-options"><br/>
            <img src="photoframes/image1.jpg" alt="Frame 1" onclick="addFrame('photoframes/image1.jpg')">
            
            <img src="photoframes/image3.jpg" alt="Frame 3" onclick="addFrame('photoframes/image3.jpg')">
            <img src="photoframes/image4.jpg" alt="Frame 4" onclick="addFrame('photoframes/image4.jpg')">
        </div>
    </div>
</div>

<script>
// Canvas Setup
let canvas = document.getElementById('canvas');
let ctx = canvas.getContext('2d');

// Default Values
let currentImage = null;
let currentFrame = null;

// Change Canvas Size based on User Input
document.getElementById('sizeInput1').addEventListener('input', function(event) {
    const size = event.target.value;
    if (size.length >= 1) {
        const width = parseInt(size);
        if (!isNaN(width) ) {
            canvas.width = width;
            drawCanvas();
        }
    }
});
document.getElementById('sizeInput2').addEventListener('input', function(event) {
    const size = event.target.value;
    if (size.length >=1) {
        const height = parseInt(size);
        if ( !isNaN(height)) {
            canvas.height = height;
            drawCanvas();
        }
    }
});

// Change Image Layer
function changeImage(imageSrc) {
    currentImage = new Image();
    currentImage.onload = drawCanvas;
    currentImage.src = imageSrc;
}

// Upload Image from File Input
function uploadImage(event) {
    let reader = new FileReader();
    reader.onload = function(e) {
        currentImage = new Image();
        currentImage.onload = drawCanvas;
        currentImage.src = e.target.result;
    };
    reader.readAsDataURL(event.target.files[0]);
}

// Add Frame to the Canvas
function addFrame(frameSrc) {
    currentFrame = new Image();
   
     $('div.preview-container').css('border-image-source', 'url('+frameSrc+')');
      $('div.preview-container').css('border-image-repeat', 'stretch');
    //currentFrame.onload = drawCanvas;
    //currentFrame.src = frameSrc;
}

// Draw Canvas with Layers
function drawCanvas() {
    ctx.clearRect(0, 0, canvas.width, canvas.height);

    // Draw Image if set
    if (currentImage) {
        ctx.drawImage(currentImage, 0, 0, canvas.width, canvas.height);
    }

    // Draw Frame if set
    if (currentFrame) {
        const frameSize = 50;
        ctx.drawImage(currentFrame, canvas.width - frameSize - 10, canvas.height - frameSize - 10, frameSize, frameSize);
    }
}

// Initialize with Default Canvas Size
drawCanvas();

$(document).keypress(function(e) {
    $('.alert').hide();
    });
</script>

</body>
</html>

<?php 
include "conn.php";

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
		    header("Location: index.php?error=$em");
		}else {
			$img_ex = pathinfo($img_name, PATHINFO_EXTENSION);
			$img_ex_lc = strtolower($img_ex);

			$allowed_exs = array("jpg", "jpeg", "png"); 

			if (in_array($img_ex_lc, $allowed_exs)) {
				$new_img_name = uniqid("IMG-", true).'.'.$img_ex_lc;
				$img_upload_path = 'uploads/'.$new_img_name;
				move_uploaded_file($tmp_name, $img_upload_path);

				// Insert into Database
				$sql = "INSERT INTO images(image_url) 
				        VALUES('$new_img_name')";
				mysqli_query($conn, $sql);
				header("Location: view.php");
			}else {
				$em = "You can't upload files of this type";
		        header("Location: fileupload.php?error=$em");
			}
		}
	}else {
		$em = "unknown error occurred!";
		header("Location: index.php?error=$em");
	}

}?>
<!DOCTYPE html>
<html>
<head>
	<title>Image Upload Using PHP</title>
	 <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css" integrity="sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ" crossorigin="anonymous">
	<style>
		body {
			display: flex;
			justify-content: center;
			align-items: center;
			flex-wrap: wrap;
			min-height: 100vh;
		}
		.alb {
			width: 100px;
			height: 100px;
			padding: 5px;
		}
		.alb img {
			width: 100%;
			height: 100%;
		}
		a {
			text-decoration: none;
			color: black;
		}
		/* Split the screen in half */
		.split {
		  height: 100%;
		  width: 50%;
		  position: fixed;
		  z-index: 1;
		  top: 0;
		  overflow-x: hidden;
		  padding-top: 20px;
		}

		/* Control the left side */
		.left {
		  left: 0;
		  
		}

		/* Control the right side */
		.right {
		  right: 0;
		  
		}

		/* If you want the content centered horizontally and vertically */
		.centered {
		  position: absolute;
		  top: 50%;
		  left: 50%;
		  transform: translate(-50%, -50%);
		  text-align: center;
		}
		div#frames {
	            width: 500px;
	            height: 400px;
	            border: 1px solid black;
	        }
	</style>
</head>
<body>
	<?php if (isset($_GET['error'])): ?>
		<p><?php echo $_GET['error']; ?></p>
	<?php endif ?>
    
     <div class="container col-sm-12">
	     	<div class="split left  col-sm-6">
		  <div class="centered" style="background-color:red;" id="frames">
		    <img src="uploads/<?= $lastuploadimg; ?>" id="selectedphotos" style="margin-top: 100px; width: 150px;padding: 10px;" alt="<?= $lastuploadimg; ?>">
		  </div>
		</div>
		<div class="split right col-sm-6">
	   		<label>
	   			<strong>Step 1 - Enter your Photo Width x Height : 
	   			</strong>&nbsp;&nbsp;&nbsp;&nbsp;</label> 
	   			<input type="text" name="width" placeholder="Enter Width" id="photoofwidth" value="">&nbsp;&nbsp;&nbsp;&nbsp; 
	   			<input type="text" name="height"  placeholder="Enter Height" id="photoofheight" value="">
		   	<br/><br/>
			<label>
				<strong>Step 2 - Choose Your Photo or Upload your own Photo : 
				</strong>&nbsp;&nbsp;&nbsp;&nbsp;
				<?php 
			          $sql = "SELECT * FROM images ORDER BY id DESC";
			          $res = mysqli_query($conn,  $sql);
				   if (mysqli_num_rows($res) > 0) {
			          	while ($images = mysqli_fetch_assoc($res)) {  ?>
			             
			             <div class="alb">
			             	<img src="uploads/<?=$images['image_url']?>" style='background-color: #ececec; border: 2px solid #ccc;' title="<?=$images['image_url']?>" name="" id=""/>
			             </div>
			          		
			    	   <?php } }?><br/>
			</label>
			 <form action=" " method="post" enctype="multipart/form-data">
			    <input type="file" class="form-control" name="my_image"/><br/>
			    <input type="submit" name="submit"value="Upload" class="btn btn-primary"/>
		     	</form><br/>
		     	<label>
	   			<strong>Step 3 - Choose Your Photo Frame : 
	   			</strong>&nbsp;&nbsp;&nbsp;&nbsp;</label> <br/><br/>
	   			<?php
	   				$dir = 'photoframes/'; 
					$images = glob($dir . "/*.{jpg,jpeg,png,gif}", GLOB_BRACE);
					foreach($images as $image) {
					    echo '<img src="' . $image . '" id="'.$image.'" alt="Image" style="width:200px; margin:10px;">'; 
					}
	   			 ?>
		   	<br/><br/><br/>
		</div>
	</div>
	<script type="text/javascript">
	
		//image width change code
		const photoofwidth = document.getElementById('photoofwidth');
		photoofwidth.addEventListener('keydown', (event) => {
			width1 = event.target.value;
			if(width1 !=0){
				let element = document.getElementById('selectedphotos');
				element.style.width = width1+'px';
			}
		});

              nameofwidth.addEventListener('keypress', (event) => {
              	width2 = event.target.value;
			if(width1 !=0){
				let element = document.getElementById('selectedphotos');
				element.style.width = width1+'px';
			}
		});

		//image height change code
		//image width change code
		const photoofheight = document.getElementById('photoofheight');
		

		photoofheight.addEventListener('keydown', (event) => {
			height1 = event.target.value;
			if(height1 !=0){
				let element1 = document.getElementById('selectedphotos');
				element1.style.height = height1+'px';
			}
		});

              photoofheight.addEventListener('keypress', (event) => {
              	height1 = event.target.value;
			if(height1 !=0){
				let element1 = document.getElementById('selectedphotos');
				element1.style.height = height1+'px';
			}
		});
	
</script>
</body>
</html>
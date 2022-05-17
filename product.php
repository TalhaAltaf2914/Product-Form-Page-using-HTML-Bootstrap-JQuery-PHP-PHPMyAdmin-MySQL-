
<?php
error_reporting(0);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <script src="https://ajax.googleapis.com/ajax/libs/
jquery/3.6.0/jquery.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css" rel="stylesheet">
    
    <script type = "text/javascript">
        //jquery starts here
        function deleteRow(row){
            console.log(row + "=>row " + row);
            $.post('product.php', {row: row}, function(updatedTableHTML){
                    // console.log("msg: "+ updatedTableHTML);
                    
                    location.reload();
                    // $('table').html(updatedTableHTML);

                });
        }

        function editProduct(rowNum, actionSelected){

            if(actionSelected == 1){
                document.getElementById('prodID').value = document.getElementById('id'+ rowNum).innerHTML;
                document.getElementById('name').value = document.getElementById('name'+ rowNum).innerHTML;
                document.getElementById('color').value = document.getElementById('color'+ rowNum).innerHTML;
                document.getElementById('size').value = document.getElementById('size'+ rowNum).innerHTML;
                document.getElementById('img').src = document.getElementById('img'+ rowNum).src;
                // document.getElementById('ship').value = document.getElementById('shipping'+ rowNum).innerHTML;
            
                //check shipping checkbox if shipping was previously checked
                if(document.getElementById('shipping'+ rowNum).innerHTML == 1)
                    document.getElementById('ship').checked = true;
                else
                    document.getElementById('ship').checked = false;
                //check appropriate radio button
                if(document.getElementById('warranty'+ rowNum).innerHTML == 0)
                    document.getElementById('noWarranty').checked = true;
                else
                    document.getElementById('days').checked = true;

                
                console.log("Shipping: "+ document.getElementById('shipping'+ rowNum).innerHTML);
                

            }
            
        }
        
    </script>
    <link rel="stylesheet" href="product.css">
</head>
<body class="bg-light">
    <header>
        <center><h1 class="h1">Product Page</h1>
    <hr style="color: black; width: 350px; border: 1px solid black"></center>
    </header>
    <form class="container" action="" method="POST" enctype="multipart/form-data">
        <div class="setFlexSide row" style="margin-top:20px;">
            <div id="idDiv" class="col">
                <!-- <div>Product ID</div> -->
                
                <label for="prodID" class="form-label">Product ID</label>
                <input class="form-control" required id="prodID" type="text" name="prodID">
                 <!-- style="width: 300px;"-->
            </div>

            <div id="nameDiv" class="col">
                <!-- <div>Product Name</div> -->
                <label for="name" class="form-label" >Product Name</label>
                <input class="form-control" required id="name" type="text" name="name">
            </div>
        </div>
        
        <div class="setFlexSide row" style="margin-top: 20px;">
            <div id="colorDiv" class="col">
                <!-- <div>Color</div> -->
                
                <label for="color" class="form-label">Color</label>
                <select class="form-control" required id="color" type="text" name="color">
                    <option value="Red">Red</option>
                    <option value="Black">Black</option>
                    <option value="Orange">Orange</option>
                </select>
            </div>

            <div id="sizeDiv" class="col">
                <!-- <div>Size</div> -->
                <label for="size" class="form-label">Size</label>
                <select class="form-control" required id="size" type="text" name="size">
                    <option value="S">S</option>
                    <option value="M">M</option>
                    <option value="L">L</option>
                </select>
            </div>
        </div>

        <div id="imageDiv" class="row d-grid mx-auto" style="margin-top:30px; margin-bottom: 20px;">
            <label for="img" class="form-label">Product Image:</label>
            <input class="form-control" required type="file" id="img" name="img" accept="image/*">
            
        </div>

        <div id="shipDiv" class="form-check form-check-inline" style="margin-top: 25px;">
            <input class="form-check-input" type="checkbox" name="ship" value="1" id="ship"><label class="form-check-label" for="ship">Free Shipping</label>
        </div>

        <p style="margin-top:30px;"><u>Warranty:-</u></p>
        <div id="warrantyDiv" class="form-check  form-check-inline">
            <input class="form-check-input"  required type="radio" name="days" value="15" id="days"><label class="form-check-label" for="days">15 days</label>
        </div>
        <div class="form-check  form-check-inline">
            <input class="form-check-input"  required type="radio" name="days" value="0" id="noWarranty"><label class="form-check-label" for="noWarranty">No Warranty</label>
        </div>

        <div class="d-grid gap-2 col-4 mx-auto" style="margin-top: 30px; margin-bottom:30px;">
            <input class="btn btn-dark" type="submit" id="submitBtn" name="submitBtn">
        </div> 

    </form>


    
    <?php
        if(isset($_POST['submitBtn'])){
            $id = $_POST['prodID'];
            if($id < 0){
                $id = $id * -1;
            }
            
            $name = $_POST['name'];
            $color = $_POST['color'];
            $size = $_POST['size'];
            $img = $_POST['img'];
            $ship = $_POST['ship'];
            $warranty = (int) $_POST['days'];

            
            $imageFile = addslashes(file_get_contents($_FILES["img"]["tmp_name"]));
            
            
            
            $con = mysqli_connect('localhost', 'root', '');
            if(!$con){
                die('Error while connecting to DB...');
            }

            $db = mysqli_select_db($con, 'product');
            if(!$db){
                die('Error while selecting db');
            }
            
            $tupleExists = false;
            //checking if tuple with entered id already exists
            $query = "select * from products;";
            $result = mysqli_query($con, $query);
            if(!$result){
                die('Error while executing query');
            }
            while($row = mysqli_fetch_array($result))
            {
                if($row['ID'] == $id){
                    $tupleExists = true;
                    break;
                }

            }
            // echo "<br>I am $tupleExists";
            if($tupleExists){
                $query = "update products set ID = '$id', NAME = '$name', 
                COLOR = '$color', SIZE = '$size', 
                SHIPPING = '$ship', WARRANTY = '$warranty', 
                IMAGE = '$imageFile' where ID = '$id'";
                // echo "<script>showAlert();</script>";
            }
            else{
                
                $query= "insert into products VALUES ('$id','$name','$color','$size','$ship','$warranty', '$imageFile');";
             
            }

            $result = mysqli_query($con, $query);
            if(!$result){
                die('Error while executing query');
            }

   

            $query = "select * from products;";

            $result = mysqli_query($con, $query);
            if(!$result){
                die('Error while executing query');
            }
            
            echo "<br><table class='table table-striped table-hover' >
            <tr class='table-dark'>
            <th scope='col'>ID</th>
            <th scope='col'>Name</th>
            
            <th scope='col'>Color</th>
            
            <th scope='col'>Size</th>
            
            <th scope='col'>Shipping</th>
            
            <th scope='col'>Warranty</th>
            
            <th scope='col'>Image</th>
            
            <th scope='col'>Actions</th>

            </tr>";
            
            $editStr = "Edit";
            $remStr = "Remove";
            
            $i = -1;
            while($row = mysqli_fetch_array($result))
            {
                
                $i++;
                echo "<tr id='$i'>";
                    echo "<th scope='row' id='id$i' value='$i'>" . $row['ID'] . "</th>";
                    echo "<td id='name$i'>" . $row['NAME'] . "</td>";
                    echo "<td id='color$i'>" . $row['COLOR'] . "</td>";
                    echo "<td id='size$i'>" . $row['SIZE'] . "</td>";
                    echo "<td id='shipping$i' value='" . $row['SHIPPING'] . "'>" . $row['SHIPPING'] . "</td>";
                    echo "<td id='warranty$i' value='" . $row['WARRANTY'] . "'>" . $row['WARRANTY'] . "</td>";
                   
                    echo "<td id=''><img id='img$i' src='data:image;base64, ".  base64_encode($row['IMAGE'] )  . "' alt='Image Here' style='width:100px; height: 100px;'/></td>'";
                    
                    // echo "<td>" ."<input type='button'  onclick ='" . "editProduct($i," . "1)" ."' name ='edit$i' id='$i' value='Edit'> / <input  type='button' value='Remove' name ='remove$i' id='$i'>"  . "</td>";
                    // echo "<td>" ."<input type='button'  onclick ='" . "editProduct($i," . "1)" ."' name ='edit$i' id='".$row['ID']."' value='Edit'> / <button  value='Remove' name ='remove$i' id='". $row['ID']."'></button>";
                    // echo "<td>" ."<input type='image' src='pencil-icon.png' style='width:30px; height: 30px' onclick ='" . "editProduct($i," . "1)" ."' name ='edit$i' id='".$row['ID']."' value='Edit'> / <button  value='Remove' name ='remove$i' id='". $row['ID']."'></button>";
                    echo "<td>" ."<input type='image' src='pencil-icon.png' style='width:30px; height: 30px' onclick ='" . "editProduct($i," . "1)" ."' name ='edit$i' id='".$row['ID']."' value='Edit'> / <input onclick='deleteRow(".$row['ID'].")' type='image' src='trash-bin-icon.png' style='width:30px; height: 30px' value='". $row['ID']."' name ='remove$i' id='removeBtn'>";
                    
                echo "</tr>";
                // onclick ='" . "editProduct($i," . "0)" ."'
                
            }
            echo "</table>";

            mysqli_close($con);


        }

        if(isset($_POST['row'])){
            $con = mysqli_connect('localhost', 'root', '');
            if(!$con){
                die('Error while connecting to DB...');
            }
    
            $db = mysqli_select_db($con, 'product');
            if(!$db){
                die('Error while selecting db');
            }
    
            
            $query = "delete from products where id = '" . $_REQUEST['row'] . "';";
            $result = mysqli_query($con, $query);
            if(!$result){
                die('Error while executing query');
            }

            // $tableHTML = "<table class='table table-striped table-hover' ><tr class='table-dark'>
            // <th scope='col'>ID</th>
            // <th scope='col'>Name</th>
            
            // <th scope='col'>Color</th>
            
            // <th scope='col'>Size</th>
            
            // <th scope='col'>Shipping</th>
            
            // <th scope='col'>Warranty</th>
            
            // <th scope='col'>Image</th>
            
            // <th scope='col'>Actions</th>

            // </tr>";
            
            // $query = "select * from products;";
            // $result = mysqli_query($con, $query);
            // if(!$result){
            //     die('Error while executing query');
            // }
            
            // $i = -1;
            // while($row = mysqli_fetch_array($result))
            // {
                
            //     $i++;
            //     $tableHTML+= "<tr id='$i'>";
            //     $tableHTML+= "<th scope='row' id='id$i' value='$i'>" . $row['ID'] . "</th>";
            //     $tableHTML+= "<td id='name$i'>" . $row['NAME'] . "</td>";
            //     $tableHTML+= "<td id='color$i'>" . $row['COLOR'] . "</td>";
            //     $tableHTML+= "<td id='size$i'>" . $row['SIZE'] . "</td>";
            //     $tableHTML+= "<td id='shipping$i' value='" . $row['SHIPPING'] . "'>" . $row['SHIPPING'] . "</td>";
            //     $tableHTML+= "<td id='warranty$i' value='" . $row['WARRANTY'] . "'>" . $row['WARRANTY'] . "</td>";
                   
            //     $tableHTML+= "<td id=''><img id='img$i' src='data:image;base64, ".  base64_encode($row['IMAGE'] )  . "' alt='Image Here' style='width:100px; height: 100px;'/></td>'";
                    
                    
            //     $tableHTML+= "<td>" ."<input type='image' src='pencil-icon.png' style='width:30px; height: 30px' onclick ='" . "editProduct($i," . "1)" ."' name ='edit$i' id='".$row['ID']."' value='Edit'> / <input onclick='deleteRow(".$row['ID'].")' type='image' src='trash-bin-icon.png' style='width:30px; height: 30px' value='". $row['ID']."' name ='remove$i' id='removeBtn'>";
                    
            //     $tableHTML+= "</tr>";
                   
            // }
            // $tableHTML += "</table>";
        
            mysqli_close($con);

            // echo $tableHTML;
        }
    
    ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>
</html>
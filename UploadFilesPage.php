<?php 

require_once('dbConnection.php');
require_once('checkValidations.php');
require_once('user.php');
require_once('vendor/autoload.php');
//require_once('plot_cluster.php');
use Phpml\Clustering\KMeans;

$db_conn = createDBConnection();
$_SESSION['db_conn'] = $db_conn;
$_SESSION['kmeansTestResult']='';
//$fileName = checkFileName();
//$fileContent = checkUploadedFile();
//$userName =  $_SESSION['userName'];

if (isset($_POST['kmeans']))
{ 
 $modelname = mysql_entities_fix_string($db_conn, $_POST['modelName']);
 $callId = $_POST['radioEntry']; 
   if($callId == 'manualEntry') {
    manualKmeans();
  }
  else{
    fileKmeans();
  }
}
if (isset($_POST['emcluster']))
{ 
 $modelname = mysql_entities_fix_string($db_conn, $_POST['modelName']);
 $callId = $_POST['radioEntry']; 
   if($callId == 'manualEntry') {
    manualEMCluster();
  }
  else{
    fileEMCluster();
  }
}
if (isset($_POST['predict']))
{ 
  $callId = $_POST['radioEntryTest']; 
  if ($callId == 'manualEntryTest') {
    manualTest();
  } else {
    fileTest();
  }
}


fetch_models();


if (isset($_POST['logout']))
{ 
  logout();
}


echo <<<_END

<!DOCTYPE html>
<html>
<head>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style>
    * {
      box-sizing: border-box;
    }

    .column {
      float: left;
      width: 50%;
      padding: 10px;
      height: 300px; 
    }

    .row:after {
      content: "";
      display: table;
      clear: both;
    }
    .signup {
      border:1px solid #999999; font: normal 14px helvetica; color: #444444;
    }
  </style>
  <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css" />
  <script>

   function displayGraphButton() {
    alert("hello");
      var graphBut = document.getElementById("showGraph");
      if (graphBut.style.display === "none"){
          graphBut.style.display = "block";
      } else {
       graphBut.style.display = "none";
     }
   }
    function fileForm() {
      var radioButton = document.getElementById("fileEntry");
      var text = document.getElementById("fileDiv");
      var manualDiv = document.getElementById("manualDiv");
      if (radioButton.checked == true){
        text.style.display = "block";
        manualDiv.style.display = "none";
      } else {
       text.style.display = "none";
     }
   }
   function manualForm() {
    var radioButton = document.getElementById("manualEntry");
    var text = document.getElementById("manualDiv");
    var fileDiv = document.getElementById("fileDiv");
    if (radioButton.checked == true){
      fileDiv.style.display = "none";
      text.style.display = "block";
    } else {
     text.style.display = "none";
   }
 }
 function fileFormTest() {
  var radioButton = document.getElementById("fileEntryTest");
  var text = document.getElementById("fileDivTest");
  var manualDiv = document.getElementById("manualDivTest");
  if (radioButton.checked == true){
    text.style.display = "block";
    manualDiv.style.display = "none";
  } else {
   text.style.display = "none";
 }
}
function manualFormTest() {
  var radioButton = document.getElementById("manualEntryTest");
  var text = document.getElementById("manualDivTest");
  var fileDiv = document.getElementById("fileDivTest");
  if (radioButton.checked == true){
    fileDiv.style.display = "none";
    text.style.display = "block";
  } else {
   text.style.display = "none";
 }
}
</script>
</head>
<body>

  <h2>Hello and Welcome: 
_END;
 echo $_SESSION['userName'];
echo <<<_END
 </h2>

  <div class="row" style = "margin-left: 2%;margin-right: 2%">
    <div class="column">
      <h2>Train Model</h2>
      <form name="form" role="form" method="post" enctype="multipart/form-data" action="UploadFilesPage.php">
        <label for="modelName">Input Model Name</label>
        <input type="text" name="modelName" id="modelName" class="form-control"  required />
        


        <div class="form-group">
          <input type="radio" name="radioEntry" id="fileEntry" checked="checked" value="fileEntry" onchange="fileForm()"> Upload a file<br>
          <input type="radio" name="radioEntry" id="manualEntry" value="manualEntry" onchange="manualForm()"> Enter Data manually<br>
        </div>




        <div id="manualDiv" style="display:none">
          <label for="inputData"> Enter Train Data</label><br/>
          <textarea rows="4" cols="50" name="inputData" id="inputData"></textarea><br/>
        </div>


        <div class="form-group pass_show" id="fileDiv">
          <label for="file_upload">Select File (Text File only)</label>
          <input type="file" name="file_upload" id="file_upload" class="form-control" />
        </div>


        <div class="form-actions">
          <button type="submit" name="kmeans" id="kmeans" class="btn btn-primary">Kmeans</button>
          <button type="submit" name="emcluster" id="emcluster"class="btn btn-primary">EMCluster</button>
        </div>


      </form>
    </div>

    <!-- test-->
    <div class="column" >
      <h2>Test Model</h2>
      <form name="form" role="form" method="post" enctype="multipart/form-data" action="UploadFilesPage.php">
        <div class="form-group">
          <label for="testModels">List of available trained models</label><br>
          <select name="testModels" id="testModels">
            <!-- <option >Choose one</option>--><!--selected="selected" -->
_END;
            
            $models = fetch_models();
            foreach($models as $item){
            ?>
            <option value="<?php echo $item; ?>" selected ="selected"><?php echo $item; ?></option>
              <?php
            }
            
            echo <<<_END
          </select>
        </div>
        <h3>Test Data</h3>
        <form name="form" role="form" method="post" enctype="multipart/form-data" action="UploadFilesPage.php">
          <div class="form-group">
            <input type="radio" name="radioEntryTest" id="fileEntryTest" checked="checked" value="fileEntryTest" onchange="fileFormTest()"> Upload a test file<br>
            <input type="radio" name="radioEntryTest" id="manualEntryTest" value="manualEntryTest" onchange="manualFormTest()"> Enter test Data manually<br>
          </div>




          <div id="manualDivTest" style="display:none">
            <label for="inputDataTest"> Enter Test Data</label><br/>
            <textarea rows="4" cols="50" name="inputDataTest" id="inputDataTest"></textarea><br/>
          </div>


          <div class="form-group pass_show" id="fileDivTest">
            <label for="file_uploadTest">Select File (Text File only)</label>
            <input type="file" name="file_uploadTest" id="file_uploadTest" class="form-control" />
          </div>


          <div class="form-actions">
            <button type="submit" name="predict" id="predict" class="btn btn-primary" >Predict</button>
            <button type="submit" name="showGraph" id="showGraph" class="btn btn-primary" onclick="window.open('http://localhost/CS174Cluster/plot_cluster.php');">show Graph</button>
          </div>

        </form>
        <div><h2>Cluster output Data</h2>
          <div>
_END;
         
          if($_SESSION['kmeansTestResult']){
            echo "<br>Test points in Cluster 1: <br>";
            print_r($_SESSION['kmeansTestResult'][0]);
            echo "<br>Test points in Cluster 2: <br>";
            print_r($_SESSION['kmeansTestResult'][1]);
          }
        echo <<<_END
        </div>
          <div id="chartContainer" name="chartContainer" style="height: 370px; width: 100%;"></div>
          <script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>
        </div>
      </form>
    </div>
  </div>

<form name="logoutform" role="form" method="post" enctype="multipart/form-data" action="UploadFilesPage.php">
  <div class="form-actions">
      <button type="submit" name="logout" id = "logout" class="btn btn-primary" style ="position:absolute; top:0; right:0;">Logout</button>
  </div>
</form> 
</body>
</html>
_END;

if($db_conn) 
{
      mysqli_close($db_conn);
}

function manualTest() {
  $db_conn = $_SESSION['db_conn'];
  $data = json_decode(mysql_entities_fix_string($db_conn, $_POST['inputDataTest']));
  $modelName_type = $_POST['testModels'];
  $modelDetails = explode("_", $modelName_type);
  $centroidPoints = json_decode(getCentroidPoints($modelDetails[0], $modelDetails[1]));
  $result = kmeans_test($data, $centroidPoints);
}

function fileTest() {
  $db_conn = $_SESSION['db_conn'];
  $modelName_type = $_POST['testModels'];

  $modelDetails = explode("_", $modelName_type);
  $centroidPoints = json_decode(getCentroidPoints($modelDetails[0], $modelDetails[1]));

  $fileName =$_FILES['file_uploadTest']['name'];
  $file_content = file_get_contents($_FILES['file_uploadTest']['tmp_name']);
  $file_content = mysql_entities_fix_string($db_conn, $file_content);

  //print_r($file_content);

  $data = json_decode($file_content);

  $result = kmeans_test($data, $centroidPoints);

  $clusterPoints1 = $result[0];
  $clusterPoints2 = $result[1]; 
}



function manualKmeans(){
  $db_conn = $_SESSION['db_conn'];
  $data = json_decode(mysql_entities_fix_string($db_conn, $_POST['inputData']));
  $file_content = kmeans_train($data);
}

function getCentroidPoints($model_type, $model_name) {
  $db_conn = $_SESSION['db_conn'];
  $username = $_SESSION['userName'];
  $query = "SELECT model_data from models_table WHERE model_type = '$model_type' AND model_name = '$model_name' AND user_name = '$username'";
  
  $result = $db_conn->query($query);
  if(!$result) die("Select from database failed: " . $db_conn->error);
  else {
    $rows = $result->num_rows;
    $output = array();
    for ($j = 0 ; $j < $rows ; ++$j){
      $result->data_seek($j);
      $row = $result->fetch_array(MYSQLI_ASSOC);
      array_push($output, $row['model_data']);
    }
    $result->close();
      //$db_conn->close();
     // return $output;
  }
  return $output[0];
    //print_r($output[0]);
}

function fileKmeans(){
    $db_conn = $_SESSION['db_conn'];
    if (isset($_POST['modelName']) && $_POST['modelName'] != "" && $_FILES) { //
      $file_name = mysql_entities_fix_string($db_conn, $_FILES['file_upload']['name']);
      $_SESSION['file_name'] = $file_name;
      //$content = mysql_entities_fix_string($db_conn, $_FILES['file_upload']);
      //print_r($content);
      switch ($_FILES['file_upload']['type']) {
        case 'text/plain':  $ext = 'txt'; break;
        default:      $ext = ''; break;
      }
      if($ext) {
        $data = json_decode(mysql_entities_fix_string($db_conn, file_get_contents($file_name)), true);
        kmeans_train($data);
      } else {
        echo "$file_name is not acceptable. Only upload text files.";
      }
    }
    //echo "<br>here2<br>";
    //display_when_logged_in($db_conn, $username);
    // destroy_session_and_data($username);
  }

  function manualEMCluster(){
    $db_conn = $_SESSION['db_conn'];
    echo "<br> manual EM Cluster <br>";
    $data = json_decode(mysql_entities_fix_string($db_conn, $_POST['inputData']));
    //emcluster_train($data);
    echo "em cluster train code is attached in EM.php but could not complete due to jar errors";
  }


  function fileEMCluster(){
    echo "<br> file EM CLuster <br>";
    $db_conn = $_SESSION['db_conn'];
    if (isset($_POST['modelName']) && $_POST['modelName'] != "" && $_FILES) { //
      $file_name = mysql_entities_fix_string($db_conn, $_FILES['file_upload']['name']);
      $_SESSION['file_name'] = $file_name;
      //$content = $_FILES['file_upload'];
      //print_r($content);
      switch ($_FILES['file_upload']['type']) {
        case 'text/plain':  $ext = 'txt'; break;
        default:      $ext = ''; break;
      }
      if($ext) {
        $data = json_decode(mysql_entities_fix_string($db_conn, file_get_contents($file_name)), true);
        //emcluster_train($data);
        echo "em cluster train code is attached in EM.php but could not complete due to jar errors";

      } else {
        echo "$file_name is not acceptable. Only upload text files.";
      }
    }
  }
  
 function emcluster_train($points){
  $em = new EM($points,2,10);///clusters, iters
  $emPoints = $em->run();
  if($emPoints){
    store_model('emcluster', $_POST['modelName'], json_encode($emPoints), $_SESSION['userName']);
    //echo "$output";
  }
}

function emcluster_test(){

}

function kmeans_train($points) { 
  $clusterCount = 2;
  $kMeans = new KMeans($clusterCount);
  $centroidPoints = [];
       // resolve $clusterCount clusters
  $clusters = $kMeans->cluster($points);
  //file_put_contents("output_clusters.txt",json_encode($clusters));
  foreach ($clusters as $key => $cluster) {
    $centroid = $cluster['centroid'];
    foreach ($centroid as $keys => $value) {
      $centroidPoints[$key][$keys] = $value;
    }
  }
  if($centroidPoints){
    store_model('kmeans', $_POST['modelName'], json_encode($centroidPoints), $_SESSION['userName']);
    echo "<br> Successfully trained and saved model in DB <br>";
  }
  //return $centroidPoints;
}

function kmeans_test($points, $centroidPoints) {
  $result[0] = array();
  $result[1] = array();
  foreach ($points as $key => $value) {
    //print_r($value[0]);
    $minValue = 100000;
    $minCentroid = 0;
    foreach ($centroidPoints as $centroidKeys => $centroidValues) {
      $distance = sqrt(($value[0] - $centroidValues[0])^2 + ($value[1] - $centroidValues[1])^2);
      if ($distance < $minValue) {
        $minValue = $distance;
        $minCentroid = $centroidKeys;
      }
    }
    $values = array($value[0], $value[1]);
    array_push($result[$minCentroid], $values); 
  }
  $_SESSION['kmeansTestResult'] = $result;
  return $result;
}

function destroy_session_and_data($username) {
  session_start();
  $_SESSION = array();
    //setcookie('userName', $userName, time() - 2592000);
    // unset($_COOKIE['username']);
  session_destroy();
}
 

function store_model($modelType, $modelName, $content, $username) {
  $db_conn = $_SESSION['db_conn'];
  $modelType = mysql_entities_fix_string($db_conn, $modelType);
  $modelName = mysql_entities_fix_string($db_conn, $modelName);
  $content = mysql_entities_fix_string($db_conn, $content);
  $username = mysql_entities_fix_string($db_conn, $username);
  $query = "INSERT INTO models_table (model_type, model_name, model_data, user_name) VALUES ('$modelType', '$modelName', '$content', '$username')";
  $result = $db_conn->query($query);
  if(!$result) die("Insert into database failed: " . $db_conn->error);
  //$db_conn->close();
}

function get_model_data($modelName, $modelType) {
  $userName = $_SESSION['userName'];
  $db_conn = $_SESSION['db_conn'];
  $query = "SELECT model_data FROM models_table WHERE user_name= '$userName' AND model_type = '$modelType' AND model_name = '$modelName'";
  $result =  $db_conn->query($query);
  if(!$result) die("model retrieval from db failed: " . $db_conn->error);
  elseif ($result) {
    $rows = $result->num_rows;
    $output = array();
    for ($j = 0 ; $j < $rows ; ++$j){
      $result->data_seek($j);
      $row = $result->fetch_array(MYSQLI_ASSOC);
      array_push($output, $row['model_data']);
    }
    $result->close();
      //$db_conn->close();
    return $output;
  }
}

function fetch_models() {
  $userName = $_SESSION['userName'];
  $db_conn = $_SESSION['db_conn'];
  $query = "SELECT CONCAT(model_type,'_', model_name) AS val FROM models_table WHERE user_name= '$userName'";
    //SELECT CONCAT(model_type,'_', model_name) FROM models_table WHERE user_name= 'qwerty'; 
  $result =  $db_conn->query($query);
  if(!$result) die("select * from database failed: " . $db_conn->error);
  elseif ($result) {
    $rows = $result->num_rows;
    $output = array();
    for ($j = 0 ; $j < $rows ; ++$j){
      $result->data_seek($j);
      $row = $result->fetch_array(MYSQLI_ASSOC);
      array_push($output, $row['val']);
    }
    $result->close();
      //$db_conn->close();
    return $output;
  }
  return $result;
}

function mysql_entities_fix_string($connection, $string) 
  {   
    return htmlentities(mysql_fix_string($connection, $string));
  }
function mysql_fix_string($connection, $string) 
  {
    if (get_magic_quotes_gpc()) 
      $string = stripslashes($string);
    return $connection->real_escape_string($string);
  }


function logout()
{
    if($db_conn) 
    {
      mysqli_close($db_conn);
    }
    session_destroy();
    header('Location:http://localhost/CS174Cluster/index.php');
}

?>
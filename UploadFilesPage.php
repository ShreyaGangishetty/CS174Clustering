<?php 
require_once('dbConnection.php');
require_once('checkValidations.php');
require_once('user.php');
require 'vendor/autoload.php';
require_once 'dbConnection.php';
use Phpml\Clustering\KMeans;

$db_conn = createDBConnection();
$_SESSION['db_conn'] = $db_conn;
//$fileName = checkFileName();
//$fileContent = checkUploadedFile();
//$userName =  $_SESSION['userName'];
if (isset($_POST['kmeans']))
{ 
 $modelname = $_POST['modelName'];
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
 $modelname = $_POST['modelName'];
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
  //echo "inside correct place";
  $selected_model = $_POST['testModels'];
  $selected_modelArray = explode ("_", $selected_model);  
  
  $modelType= $selected_modelArray[0];
  $modelName= $selected_modelArray[1];
  $trained_model = get_model_data($modelName, $modelType);
  //$test_input=;  
  if($_POST['radioEntryTest']=='fileEntryTest'){
    $_SESSION['file_nameTest'] = $_FILES['file_uploadTest']['name'];
    $file_name = $_FILES['file_uploadTest']['name'];
    $content = $_FILES['file_uploadTest'];
    //print_r($content);
    switch ($_FILES['file_uploadTest']['type']) {
      case 'text/plain':  $ext = 'txt'; break;
      default:      $ext = ''; break;
    }
    if($ext) {
      $testData = json_decode(file_get_contents($file_name), true);
      $output = kmeans_test($testData, json_decode($trained_model[0]));
      echo "$output";
    } else {
      echo "$file_name is not acceptable. Only upload text files.";
    }
  }
  else if($_POST['radioEntryTest']=='manualEntryTest'){
    $output = kmeans_test($_POST['inputDataTest'], json_decode($trained_model[0]));
    echo "$output"; 
  }
  else {
    echo "no radio button selected";
  }
  /*
    get the selected drop don menu
    split it 
    database call to fetch the model data (modelname, username, model type)
    //call test kmeans
    display result
  */
  }

  function manualKmeans(){
    echo "<br> manual K means <br>";
    $data = json_decode($_POST['inputData']);
    $file_content = kmeans_train($data);
  }

  function fileKmeans(){
    echo "<br> file K means <br>";
    //$db_conn = $_SESSION['db_conn'];
    if (isset($_POST['modelName']) && $_POST['modelName'] != "" && $_FILES) { //
      $_SESSION['file_name'] = $_FILES['file_upload']['name'];
      $file_name = $_FILES['file_upload']['name'];
      $content = $_FILES['file_upload'];
      print_r($content);
      switch ($_FILES['file_upload']['type']) {
        case 'text/plain':  $ext = 'txt'; break;
        default:      $ext = ''; break;
      }
      ECHO "$ext";
      if($ext) {
        $data = json_decode(file_get_contents($file_name), true);
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
    echo "<br> manual EM Cluster <br>";
  }

  function fileEMCluster(){
    echo "<br> file EM CLuster <br>";
  }
  


  function kmeans_train($points) { 
    $clusterCount = 2;
    $kMeans = new KMeans($clusterCount);
    $centroidPoints = [];
       // resolve $clusterCount clusters
    $clusters = $kMeans->cluster($points);
    file_put_contents("output_clusters.txt",json_encode($clusters));
    foreach ($clusters as $key => $cluster) {
      $centroid = $cluster['centroid'];
      foreach ($centroid as $keys => $value) {
        $centroidPoints[$key][$keys] = $value;
      }
    }
    if($centroidPoints){
      store_model('kmeans', $_POST['modelName'], json_encode($centroidPoints), $_SESSION['userName']);
    }
      //return $centroidPoints;
  }

  function kmeans_test($points, $centroidPoints) {
    foreach ($points as $key => $value) {
      $minValue = 100000;
      $minCentroid = 0;
      //echo "<br>helloo";
      //print_r($centroidPoints);
      //print_r($centroidPoints[0]);

      foreach ($centroidPoints as $centroidKeys => $centroidValues) {
        $distance = sqrt(($value[0] - $centroidValues[0])^2 + ($value[1] - $centroidValues[1])^2);
        if ($distance < $minValue) {
          $minValue = $distance;
          $minCentroid = $centroidKeys;
        }
      }
      //echo "$minCentroid";

    }
  }
  function destroy_session_and_data($username) {
    session_start();
    $_SESSION = array();
    //setcookie('userName', $userName, time() - 2592000);
    // unset($_COOKIE['username']);
    session_destroy();
  }

  function mysql_entities_fix_string($conn, $string) {
    return htmlentities(mysql_fix_string($conn, $string));
  }

  function mysql_fix_string($conn, $string) {
    if (get_magic_quotes_gpc()) $string = stripcslashes($string);
    return $conn->real_escape_string($string);
  } 
  function store_model($modelType, $modelName, $content, $username) {
    $db_conn = $_SESSION['db_conn'];
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
  fetch_models();
  //$db_conn->close();
  ?>
  <!DOCTYPE html>
  <html>
  <head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
      * {
        box-sizing: border-box;
      }

      /* Create two equal columns that floats next to each other */
      .column {
        float: left;
        width: 50%;
        padding: 10px;
        height: 300px; /* Should be removed. Only for demonstration */
      }

      /* Clear floats after the columns */
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

  <h2>Hello and Welcome: <?php echo $_SESSION['userName'];?></h2>

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
            <option >Choose one</option><!--selected="selected" -->
            <?php
            $models = fetch_models();
            // Iterating through the product array
            //echo "hello";
            //print_r($models);
            //echo count($models);
            foreach($models as $item){
              ?>
              <option value="<?php echo $item; ?>"><?php echo $item; ?></option>
              <?php
            }
            ?>
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
            <button type="submit" name="predict" id="predict" class="btn btn-primary">Predict</button>
          </div>

        </form>

      </form>
    </div>
  </div>
</body>
</html>

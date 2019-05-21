<?php
require 'vendor/autoload.php';
use MathPHP\Probability\Distribution\Multivariate;
use MathPHP\LinearAlgebra\Matrix;

class EM{
 
  var
  $iterations,
  $number_of_sources,
  $X,
  $mu,
  $pi,
  $cov,
  $reg_cov,
  $XY;


  function __construct($X, $number_of_sources, $iterations){
		$this->iterations = $iterations;
        $this->number_of_sources = $number_of_sources;
        $this->X = $X;
        $this->mu = array();
        $this->pi = array();
        $this->cov = array();
        $this->XY = array();
  }

  
  function setDbConn($dbConn) {
    $this->dbConn = $dbConn;
  }

	public function logout(){
		session_destroy();
	}

	public function is_logged_in(){
		if(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true){
			return true;
		}
  }

  function Identity($num) 
 { 
    $row; $col; $matrix;
      
    for ($row = 0; $row < $num; $row++) 
    { 
        for ($col = 0; $col < $num; $col++) 
        { 
            // Checking if row is  
            // equal to column  
            if ($row == $col) 
            $matrix[$row][$col]=1; 
            else
            $matrix[$row][$col]=0;
        }  
        //echo"\n"; 
    } 
    return $matrix; 
 } 
 
 //CHECK HERE
  function reshape($array_input, $x, $y)
  {	
	 $array_output = array();
	 if(count($array_input)==0 || $x*$y != count($array_input)*count($array_input[0]))
		return $array_input;
	 $count = 0;
	 for($i=0; $i<count($array_input); $i++)
	 {
		 for($j=0; $j<count($array_input[0]); $j++)
		 {
			 $array_output[count/$y][count%$y]=$array_input[$x][$y];
			 $count+=1;
		 }
	 }
	 return $array_output;
  }
  
  function meshGridCreate(array $X_firstCol, array $X_secondCol)
  {
	  //$meshGrid = new Matrix();
	  //$x_matrix = new Matrix();
	  //$y_matrix = new Matrix();
	  /** old
	  $meshGrid = array();
	  $x_matrix = array();
	  $y_matrix = array();
	  $rows = max(count($X_secondCol),count($X_firstCol)); //7
	  for($i=0; $i<$rows; $i++)
	  {
		  $x_matrix[$i] = $X_firstCol; //[[45678],[45678],[45678],[45678],[45678],[45678],[45678]]
	  }
	  for($i=0; $i<$rows; $i++)
	  {
		  $y_matrix[$i] = $X_secondCol;
	  }
	  //$y_matrix = $y_matrix->transpose();
	  $y_matrix = $this->transpose_2D($y_matrix);
	  $meshGrid[0] = $x_matrix;
	  $meshGrid[1] = $y_matrix;
	  //$x_matrix = array_fill(0, count($X_firstCol), 0); //45678 
	  //$y_matrix = array_fill(0, count($X_secondCol), 0); // 1234567 ***/
	  
	  $meshGrid = array();
	  
	  for($i=0;$i<count($X_firstCol);$i++)
	  {
		  for($j=0;$j<count($X_secondCol);$j++)
		  {
			  array_push($meshGrid, array($X_firstCol[$i], $X_secondCol[$j]));
		  }
	  }
	  
	  return $meshGrid;
  }
  
  function transpose_2D($array_one) {
    $array_two = [];
    foreach ($array_one as $key => $item) {
        foreach ($item as $subkey => $subitem) {
            $array_two[$subkey][$key] = $subitem;
        }
    }
    return $array_two;
  }
  
  //CHECK HERE
  function mul_2_Array($arr1, $arr2)
  {
	$aRows = count($arr1);
    $bCols = count($arr2[0]);
    $aCols = count($arr1[0]);	
	$res = array();
    for ($i = 0; $i < $aRows; $i++) 
    { 
        for ($j = 0; $j < $bCols; $j++) 
        { 
            $res[$i][$j] = 0; 
            for ($k = 0; $k < $aCols; $k++) 
                $res[$i][$j] += $mat1[$i][$k] *  
                                $mat2[$k][$j]; 
        } 
    }
	return $res;	
  }
  
  public function run()
  {
		$identity_matrix = $this->Identity(count($this->X[0]));
		$num = count($identity_matrix);
		for ($row = 0; $row < $num; $row++) 
		{ 
			for ($col = 0; $col < $num; $col++) 
			{ 
			   //CHECK HERE
				$this->reg_cov[$row][$col]= 1e-6 * $identity_matrix[$row][$col];
			}  
		} 
		$X_firstCol = array_column($this->X, 0);
		$X_secondCol = array_column($this->X, 1);
		sort($X_firstCol);
		sort($X_secondCol);
		$meshGrid = $this->meshGridCreate($X_firstCol, $X_secondCol);
		echo "<br>";
		print_r("printing X");
		echo "<br>";
		print_r($this->X);
		echo "<br>";
		print_r("printing meshgrid");
        echo "<br>";
		print_r($meshGrid);
		echo "<br>";
		$XY = $meshGrid;
		
		echo "<br>";
		print_r("xy_arr: ");
		print_r($XY);
		
		
        //""" 1. Set the initial mu, covariance and pi values"""
		
		$x_firstCol = array();
		foreach(range(1,$this->X) as $r)
		{
			array_push($x_firstCol, $this->X[$r][0]);
		}
		$this->mu =  array();
		foreach (range(1,$this->number_of_sources) as $row) {
		 foreach (range(1,count($this->X[0])) as $col) {
		  $this->mu[$row][$col] = mt_rand(min($x_firstCol),max($x_firstCol));
		 }
		}
		
	
		
		foreach(range(1,count($this->cov)) as $arr)
		{
			foreach(range(1,count($this->cov[$arr])) as $row)
			{
				foreach(range(1,count($this->cov[$arr][$row])) as $col)
				{
					$this->cov[$arr][$row][$col] = 0;
					if($row == $col)
					{
						$this->cov[$arr][$row][$col] = 5;
					}
				}
			}
		}
		
		$fraction = 1/$this->number_of_sources;
		$this->pi = array_fill(0, $this->number_of_sources, $fraction);
		$log_likelihoods = array();
		
      
		
		//E Step PHP
		foreach(range(1,$this->iterations) as $itr)
		{
			$r_ic = array_fill(count($this->X), count($this->cov), 0);
			$range_r_ic = array();
			foreach(range(1,count($r_ic[0])) as $r)
			{
				$range_r_ic[$r] = $r;
			}
			$zip = array_map(null, $this->mu, $this->cov, $this->pi, $range_r_ic);
			foreach(range(1,count($zip)) as $z)
			{
				 echo "zip array is: ".$z;
				 //CHECK THIS
				 $m = $zip[$z][0];
				 $co = $zip[$z][1];
				 $p = $zip[$z][2];
				 $r =  $zip[$z][3];
				 //$co = $co + $this->reg_cov;
				 $co = new Matrix();
				 echo "data type before multivariate normal";
				 echo "m is: ".gettype($m);
				 echo "cov is: ".gettype($co);
				 $mn = new Multivariate\Normal($m, $co);
				 $pdf_value = $mn->pdf($this->X);
				 $array_sum_norm = array(); 			 
				 $zip_c = array_map(null, $this->pi, $this->mu, $this->cov+$this->reg_cov);
				 $idx_sum = 0;
				 foreach(range(1,count($zip_c)) as $z_c)
				 {
					 //CHECK THIS
					 //$pi_c = $z_c[0];
					 //$mu_c = $z_c[1];
					 //$cov_c = $z_c[2];
					 $pi_c = $zip_c[0][$z_c];
					 $mu_c = $zip_c[1][$z_c];
					 $cov_c = $zip_c[2][$z_c];
					 $mn_c = new Multivariate\Normal($mu_c, $cov_c);
					 $pdf_c_value = $mn_c->pdf($this->X);
					 $array_sum_norm[$idx_sum] = $pdf_c_value * $pi_c;
					 $idx_sum+=1;
				 }			 
				 $sum_normal = array_sum($array_sum_norm);
				 //array_column($r_ic, $r)
				 $r_ic[][$r] = $p * $pdf_value / $sum_normal;
			}
		}
	
			//M Step PHP
			$this->mu = array();
			$this->cov = array();
			$this->pi = array();
			$log_likelihoods = array();
			$X_subMean = $this->X;
			foreach(range(1,count($this->X)) as $r)
			{
				foreach(range(1,count($this->X[$r])) as $c)
				{
					$X_subMean[$r][$c] = $this->X - $mu_c;
				}
			}
			
			foreach(range(1,count($r_ic[0])) as $idx_r_ic)
			{
				$m_c = array_sum(array_column($r_ic, $idx_r_ic));				
				$r_ic_shaped = $this->reshape(array_column($r_ic, $idx_r_ic), count($this->X), 1);
				$mu_c = (1/m_c)*array_sum($r_ic_shaped);
				array_push($this->mu, $mu_c);
				$mul_T = $this->transpose_2D($this->mul_2_Array($r_ic_shaped, $X_subMean));
				//$mul_T = $this->transpose_2D($mul);
				//CHECK HERE
				$dot_value = $this->dot($mul_T, $X_subMean);
				$cov_value = ((1/$m_c) * $dot_value)+$this->reg_cov;
				array_push($this->cov, $cov_value);
				array_push($this->pi, $m_c/array_sum($r_ic));
			}
			
			$arr_range_cov = range(0,count($this->cov));
			$arr_range_mu = range(0,count($this->mu));
			$zip_piMuCov = array_map(null, $this->pi, $arr_range_mu, $arr_range_cov);
			$mn_kij = array();
			$idx_mn_kij = 0;
			$arr_sum_kij = array();
			$idx_arr_sum_kij = 0;
			foreach(range(1,count($zip_piMuCov)) as $z_piMuCov)
			{
				//CHECK HERE
				$k = $zip_piMuCov[0][$z_piMuCov];
				$i = $zip_piMuCov[1][$z_piMuCov];
				$j = $zip_piMuCov[2][$z_piMuCov];				
				$mn_kij[$idx_mn_kij] = new Multivariate\Normal($this->mu[i], $this->cov[j]);
				$pdf_value_kij = $mn_kij[$idx_mn_kij]->pdf($this->X);
				$arr_sum_kij[$idx_arr_sum_kij] = $k * $pdf_value_kij;
				$idx_mn_kij+=1;
				$idx_arr_sum_kij+=1;
			}
			$sum_norm_kij = array_sum($arr_sum_kij);
			$log_likelihoods = array_push($log_likelihoods, log($sum_norm_kij));
			
	  print1DArray($log_likelihoods);
      return $log_likelihoods;	  
	 
  }
  
    function print1DArray($array)
    {
		foreach($array as $key => $value)
		{
			echo $key." has the value". $value;
		}
    }
	
	function print2DArray($array)
    {
		foreach(range(1,count($array)) as $r)
		{
			foreach(range(1,count($array[$r])) as $c)
			{
				echo " , ".$array[$r][$c];
			}
			//echo "<br>";
		}
    }
	function getAuthenticatedUser($connection, $user)
	  {	
		  
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

}
$num_sources=2;
$iters = 100;
$X = array(array(-1.9, -1.15266043e+00),
 array( 1.34486774e+01,  1.59852848e+01),
 array(-2.01324733e+01,  3.34683133e-01),
 array(-1.54749721e+01,  4.56804516e+00),
 array( 1.25196957e+01,  1.93705900e+01));
$em = new EM($X, $num_sources, $iters);
$em->run();
?>

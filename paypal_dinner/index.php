<?php 
require('config.php');
?>
<!DOCTYPE html>
<html>
<head>
</head>
<body>

<header>
<h1><a style="text-decoration:none;color: #009CDE;" href="index.php">EMPLOYEE LIST</a></h1>
</header>

<nav>
<br>
<br>
</nav>

<section>
<div style="float:left;width: 72%;height:390px;">

<div style="float:left;">
<h1>Employee List</h1>
<form method="GET" name="search_form" action="index.php" >
<input type="text" name="search_val" id="search_val" />
<input class="button" id="search_submit" type="submit" value="Search">
</form>
</div>
<table style="width:700px;clear:both;">
<?php
	//get from database
	$servername = DB_HOST;
	$dbname = DB_NAME;
	$dbusername = DB_USER;
	$password = DB_PASS;
	$tablename = 'employee';
	$status = 1;

	$conn = new PDO("mysql:host=$servername;dbname=$dbname", $dbusername, $password);
	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	// check if user exists and has status =1 in database

	 // Find out how many items are in the table
	 //condition for search
	if(isset($_GET['search_val'])) {
		$username = $_GET['search_val'];
		$statement = $conn->prepare("SELECT count(*) as cnt from employee WHERE username LIKE :username");
		$statement->bindValue(':username',  '%' . $username . '%');
		
	} else {
		$statement = $conn->prepare("SELECT count(*) as cnt from employee");
	}
	
	$statement->execute();
	$result = $statement->fetch(PDO::FETCH_ASSOC);
	$total = $result['cnt'];
	// echo $total;exit;
   // How many items to list per page
    $limit = 9;

    // How many pages will there be
    $pages = ceil($total / $limit);

    // What page are we currently on?
    $page = min($pages, filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT, array(
        'options' => array(
            'default'   => 1,
            'min_range' => 1,
        ),
    )));

    // Calculate the offset for the query
    $offset = ($page - 1)  * $limit;

    // Some information to display to the user
    $start = $offset + 1;
    $end = min(($offset + $limit), $total);

    // The "back" link
    $prevlink = ($page > 1) ? '<a href="?page=1" title="First page">&laquo;</a> <a href="?page=' . ($page - 1) . '" title="Previous page">&lsaquo;</a>' : '<span class="disabled">&laquo;</span> <span class="disabled">&lsaquo;</span>';

    // The "forward" link
    $nextlink = ($page < $pages) ? '<a href="?page=' . ($page + 1) . '" title="Next page">&rsaquo;</a> <a href="?page=' . $pages . '" title="Last page">&raquo;</a>' : '<span class="disabled">&rsaquo;</span> <span class="disabled">&raquo;</span>';

   

    // Prepare the paged query
	//condition for search
	if(isset($_GET['search_val'])) {
		$username = $_GET['search_val'];
		$offset = 0;
		$stmt = $conn->prepare('SELECT * FROM employee WHERE username LIKE :username ORDER BY id LIMIT :limit OFFSET :offset');
		$stmt->bindValue(':username',  '%' . $username . '%');
	} else {
		$stmt = $conn->prepare('SELECT * FROM employee ORDER BY id LIMIT :limit OFFSET :offset');
	}
	

    // Bind the query params
    $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();

    // Do we have any results?
    if ($stmt->rowCount() > 0) {
        // Define how we want to fetch the results
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $iterator = new IteratorIterator($stmt);

        // Display the results
       $res = '<tr><th>S.No</th><th>Name</th><th>Status</th><th>Draw Number</th></tr>';
	foreach($iterator as $key=>$value) {
		// echo '<pre>';
		// print_r($value);exit;
		$id			= $value['id'];
		$username	= $value['username'];
		$status		= $value['status'];
		$draw_no	= $value['draw_no'];
		$res.= "<tr><td>$id</td><td>$username</td><td>$status</td><td>$draw_no</td></tr>";
	}
		echo $res. '</table></div><br>';
		echo '<div id="reader" style="float:left;width:300px;height:250px;margin-top: 84px;"></div></div>';
		 // Display the paging information
		echo '<div style="float:left;width:100%;" id="paging"><p>', $prevlink, ' Page ', $page, ' of ', $pages, ' pages, displaying ', $start, '-', $end, ' of ', $total, ' results ', $nextlink, ' </p></div><div style="float:left;margin-right: 136px;" id="user_luckyno">-- Scanning in Progress --</div>';

    } else {
		$res = '';
        echo $res.'<p style="  float: left;clear: both; display: block;height: 294px;">No results could be displayed.</p>';
    }
	
	
	?>

 <script src="js/jquery.min.js"></script> 
 <script src="js/html5-qrcode.js"></script> 
 <script src="js/html5-qrcode.js"></script> 
 <script src="js/jsqrcode-combined.min.js"></script> 
 <script src="js/jquery.playSound.js"></script> 
<link rel="stylesheet" type="text/css" href="style.css">

 <script>
  $('#reader').html5_qrcode(function(data){
        //ajax call to send the data to authenticate.php
		
		$.ajax({
				type: "GET",
				url: "authenticate.php",
				data: "name="+data,
				dataType: "text",
				// cache : false,
				success: function(data){
					// alert(data);return false;
					var res = data.split('~');
					 // alert(res[1]);return false;
					 // 2 for success, user exists in db or user successfully updated
					if(res[1] == '2') {
						$.playSound('sounds/success');
					} else{
						$.playSound('sounds/error');
					}	
					$('#user_luckyno').show();
					$('#user_luckyno').html(res[0]);
				} ,
				
				error: function(xhr, status, error) {
					alert(error);
				},
			}); 
					
					
    },
    function(error){
       //alert(error);
    }, function(videoError){
      //alert(videoError);
    }
);
</script>


</section>

</body>
</html>

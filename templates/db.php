<?php  
	$host = DB_HOST;
	$user = DB_USER;
	$pass = DB_PASSWORD;
	$db = DB_NAME;
	function _htmlspecialchars($r){
			return array_map(function($val){
				return htmlspecialchars($val); 

			}, $r);
		}
	$con = new mysqli($host, $user, $pass, $db);
	
	$tables = $con->query('show tables');
	$tbls= [];
?>	
	<div class="my-db-wrap">
		<div class="loading">
			<div class="container">
				<p>Please Wait!</p>
			</div>
		</div>
		<div id="editform" class="modal fade" role="dialog">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title">Update Record</h4>
					</div>
					<div class="modal-body">
						<form class="update-rec" method="post">
							<div class="field-wrap"></div>	
							<div class="btn-wrap ">
								<div class="row">
									<div class="col-sm-12">
										<input type="submit" name="submit" value="Update" class="btn btn-primary pull-right">
									</div>
								</div>
							</div>
						</form>
					</div>
				</div>

			</div>
		</div>
		<div class="plg-headeer">
			<h2>My DB</h2>
		</div>
		<form  method="post" class="my-db-form" >
			<div class="my-db">
				<div class="db-side">
					<ul>
						<?php 
							$tbl1 = '';
							foreach ($tables as $key => $table) { 
							if($key == 0) 
								$tbl1 = $table['Tables_in_'.$db];	
						?>
							<li class="<?php echo $table['Tables_in_'.$db] == @$_GET['tbl'] ? 'active' : '' ?>">
								<a href="?page=my-db&tbl=<?php echo $table['Tables_in_'.$db] ?>"><?php echo $table['Tables_in_'.$db] ?></a>
							</li>
						<?php }	 ?>						
					</ul>
				</div>
				<div class="db-content">
					<table class="table table-striped">
						<thead>
							<tr>
								<th><input type="checkbox" name="check_all"></th>
								<th>Edit</th>
								<th>Delete</th>
								<?php 	 
									$tbl = @$_GET['tbl'] != '' ? $_GET['tbl'] : $tbl1;
									$columns = $con->query('SHOW COLUMNS FROM '.$tbl);
										// var_dump('SHOW COLUMNS FROM '.$tbl);
									foreach ($columns as $key => $column) { 	
										$fColums[] = $column['Field'];
								?>
									<th><?php echo $column['Field'] ?></th>
								<?php 	} ?>
							</tr>
						</thead>
						<tbody tblname="<?php echo $tbl ?>">
		<?php 	 
							$sError = '';
							$mQuery = explode(' ', @$_POST['query'])[0] == 'select' ? $_POST['query'] : 'SELECT * FROM '.$tbl;
							if(@$_POST['submit'] ==  'Run'){
								if ($_POST['query'] != '') {
									if(!$con->query($_POST['query'])){
										$sError = mysqli_error($con);
									}
								}	
							}
							if(@$_POST['submit'] ==  'Delete'){
								$ids = implode(',', $_POST['id'][array_keys($_POST['id'])[0]]);
								$con->query('DELETE FROM '.$tbl.' WHERE '.array_keys($_POST['id'])[0].'  IN ( '.$ids.' )');
							}
							$rows = $con->query($mQuery);
							if ($rows->num_rows == 0) {
		?>
								<tr>
									<td colspan="<?php echo count($fColums)+3 ?>"><h5>Record not found</h5></td>
								</tr>
		<?php 								
							}
							else{
								foreach ($rows as $key => $row) {
							?>								
							<tr rec-id-key="<?php echo $fColums[0] ?>" rec-id="<?php echo $row[$fColums[0]] ?>">
								<?php if(isset($row[$fColums[2]])){ ?>
									<td><input type="checkbox" name="id[<?php echo $fColums[0] ?>][]" value="<?php echo $row[$fColums[0]] ?>" class="rec-c"></td>
									<td><a href="#" class="edit">Edit</a></td>
									<td><a href="javascript:;" class="delete">Delete</a></td>
									<?php 	
										foreach ($fColums as $key2 => $fColum) {									
											$str = htmlspecialchars($row[$fColum]);
									?>	
										<td><span colname="<?php echo $fColum ?>"><?php echo $str ?></span></td>
									<?php } ?>
								<?php }else{	 ?>			
									<td colspan="<?php echo count($fColums)+1 ?>"><h5>Record not found</h5></td>
									<?php break; ?>
								<?php } ?>
							</tr>
							<?php 	} ?>
						<?php } ?>
						</tbody>
					</table>
				</div>
				<div class="query-box-wrap">
					<?php if($sError) { ?>
						<div class="error">
							<h5><?php echo $sError; ?></h5>
						</div>
					<?php } ?>
					<div class="q-text">
						<textarea class="form-control" placeholder="Write Query" name="query" ><?php echo @$_POST['query'] ?></textarea>	
					</div>
					<div class="q-btn">
						<input type="submit" name="submit" class="btn btn-primary run" value="Run">
						<input type="submit" name="submit" class="btn btn-primary run" value="Delete">
					</div>
				</div>
			</div>
		</form>
	</div>
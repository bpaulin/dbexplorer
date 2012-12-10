<?php
error_reporting(E_ALL ^ E_NOTICE);

require_once('config.php');
require_once('dbexplorer.class.php');

$keyValue = '';
if (isset($_POST['value'])) {
	$keyValue = $_POST['value'];
}
$fieldValue = '';
if (isset($_POST['field'])) {
	$fieldValue = $_POST['field'];
}
$strictValue = 1;
if (isset($_POST['strict'])) {
	$strictValue = $_POST['strict'];
}


$dbe = new DbExplorer();

$dbe->connect($host, $user, $pass, $base, $table);
$dbe->describeTable();


$db = new mysqli($host, $user, $pass, $base);

$keyValue = '';
if (isset($_POST['value'])) {
	$keyValue = $_POST['value'];
}
$fieldValue = '';
if (isset($_POST['field'])) {
	$fieldValue = $_POST['field'];
}
$strictValue = 1;
if (isset($_POST['strict'])) {
	$strictValue = $_POST['strict'];
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>bp-db</title>
    <link href="//netdna.bootstrapcdn.com/twitter-bootstrap/2.2.2/css/bootstrap-combined.min.css" rel="stylesheet">
</head>
<body>

<h1>
	bp-db
	<small><a href='https://bitbucket.org/bpaulin/bp-db'>bitbucket.org</a></small>
</h1>

<form method='post' action='index.php' class='form-inline'>
<?php if ( $result = $db->query("DESCRIBE `$base`.`$table`") ): ?>
<?php
    $primaryKeys = Array();
?>
	<select name='field'>
	<?php while ( $row = $result->fetch_assoc() ): ?>
		<option value='<?php echo htmlspecialchars($row['Field'], ENT_QUOTES); ?>'<?php echo ($row['Field']==$fieldValue)?'selected':'' ?>>
			<?php echo htmlspecialchars($row['Field'], ENT_QUOTES)?>
		</option>
        <?php 
        if ($row['Key']=='PRI')
        {
            $primaryKeys[] = $row['Field'];
        }
        ?>
	<?php endwhile;?>
	</select>
	<select name='strict'>
		<option value='0' <?php echo ($strictValue=='0')?'selected':'' ?>>LIKE</option>
		<option value='1' <?php echo ($strictValue=='1')?'selected':'' ?>>=</option>
	</select>
	<?php endif; ?>
	<input type='text' name='value' value='<?php echo htmlspecialchars($keyValue, ENT_QUOTES); ?>'/>
	<input type='submit' value='check' class="btn"/>
</form>
<?php
if ($strictValue){
	$compare = "='{$db->escape_string($keyValue)}'";
}
else {
	$compare = "LIKE '%{$db->escape_string($keyValue)}%'";
}
$query = "SELECT * 
		  FROM `$base`.`$table` 
		  WHERE {$db->escape_string($fieldValue)} $compare";
?>
<?php  if ( $result = $db->query($query) ): ?>
<?php $i = 0; ?>
<div class="accordion" id="accordion2">
	<?php while ( $row = $result->fetch_assoc() ): ?>
	<?php $i++; ?>
	<div class="accordion-group">
		<div class="accordion-heading">
			<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapse<?php echo $i;?>">
				<?php if ($primaryKeys): ?>
                    <?php foreach($primaryKeys as $primaryKey): ?>
                        | <?php echo $primaryKey ?>: <?php echo $row[$primaryKey] ?>
                    <?php endforeach; ?>
                <?php else: ?>
                    Record #<?php echo $i;?>
                <?php endif; ?>
			</a>
		</div>
		<div id="collapse<?php echo $i;?>" class="accordion-body collapse">
			<div class="accordion-inner">
		        <table  class="table table-condensed table-hover table-striped table-bordered">
		        <?php foreach( $row as $key=>$value ): ?>
		            <tr>
		                <td><?php echo $key ?></td>
		                <td><?php echo htmlspecialchars($value, ENT_QUOTES); ?></td>
		            </tr>
		        <?php endforeach; ?>
		        </table>
			</div>
		</div>
	</div>
	<?php endwhile;?>
</div>
<?php endif; ?>
<?php
	var_dump($dbe->select($fieldValue, $strictValue, $keyValue));
?>
<script src="http://code.jquery.com/jquery-latest.js"></script>
<script src="//netdna.bootstrapcdn.com/twitter-bootstrap/2.2.2/js/bootstrap.min.js"></script>
</body>
</html>

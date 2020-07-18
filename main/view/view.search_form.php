<form method="get" id="search">
	<input type="hidden" name="action" value="search">
	<input type="text" id="searchbar" name="search" <?php 
		if(!empty($_REQUEST['search'])){
			echo 'value="'.htmlentities($_REQUEST['search']).'"';
		}
	?> placeholder="Search...">
	<button id="searchsubmit" type="submit">Search</button><br><br>
	<input id="advanced" type="checkbox" value="1"><label class="inline" for="advanced"> Advanced Options</label>
	<fieldset class="variable-section" data-id="advanced" data-value="1">
		<label for="rpp">Results Per Page</label><input id="rpp" type="text" pattern="[0-9]*" name="rpp" value="<?php
		if(empty($_GET['rpp'])){
			$_GET['rpp'] = 25;
		}
		echo $_GET['rpp']; ?>">
		<label for="orderby">Order By</label>
		<input type="text" id="orderby" name="orderby" value="<?php echo $_REQUEST['orderby']; ?>" placeholder="Column Name">
	</fieldset>
</form>
<?php if(empty($_REQUEST['search'])){
	//$_SESSION['pagination'] = '';
}
echo $_SESSION['pagination']; ?>

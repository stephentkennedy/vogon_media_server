<?php 
$search_cols = [
	'data_id' => 'ID',
	'data_name' => 'Name',
	'data_content' => 'Content',
	'data_type' => 'Type',
	'data_parent' => 'Parent',
	'data_status' => 'Status',
	'create_date' => 'Creation Date',
	'last_edit' => 'Edit Date',
	'user_key' => 'User ID',
	'all' => 'Everything'
];
if(empty($_GET['search'])){
	$search = '';
}else{
	$search = $_GET['search'];
}
if(empty($_GET['search_col'])){
	$search_col = 'all';
}else{
	$search_col = $_GET['search_col'];
}
if(empty($_GET['limit'])){
	$limit = 25;
}else{
	$limit = (int)$_GET['limit'];
}
if(empty($_GET['order_by'])){
	$order_by = 'data_id';
}else{
	$order_by = $_GET['order_by'];
}
if(empty($_GET['dir'])){
	$dir = 'ASC';
}else{
	$dir = $_GET['dir'];
}
if(empty($_GET['offset'])){
	$offset = 0;
}else{
	$offset = $_GET['offset'];
}


$pageination = '';
if($page > 2){
	$pageination .= '<a title="First Page" href="'.build_slug('', [
		'offset' => 0,
		'search' => $search,
		'search_col' => $search_col,
		'limit' => $limit,
		'order_by' => $order_by,
		'dir' => $dir
	], 'data_browser').'">[<</a> ';
}
if($page > 1){
	$pageination .= '<a title="Prev Page" href="'.build_slug('', [
		'offset' => $offset - $limit,
		'search' => $search,
		'search_col' => $search_col,
		'limit' => $limit,
		'order_by' => $order_by,
		'dir' => $dir
	], 'data_browser').'"><</a> ';
}
$pageination .= $page;
if($page < $pages - 1){
	$pageination .= ' <a title="Next Page" href="'.build_slug('', [
		'offset' => $offset + $limit,
		'search' => $search,
		'search_col' => $search_col,
		'limit' => $limit,
		'order_by' => $order_by,
		'dir' => $dir
	], 'data_browser').'">></a>';
}
if($page < $pages - 2){
	$pageination .= ' <a title="Last Page" href="'.build_slug('', [
		'offset' => (($pages - 1) * $limit),
		'search' => $search,
		'search_col' => $search_col,
		'limit' => $limit,
		'order_by' => $order_by,
		'dir' => $dir
	], 'data_browser').'">>]</a>';
}
?>
<style>
.flex-resize{
	min-width: 48px;
	transition: min-width 0.2s linear;
}
.flex-focus{
	min-width: 65vw;
}
.flex-resize.result-four{
	max-height: 52px;
}
@media only screen and (min-width: 1200px){
	.flex-focus{
		min-width: 35vw;
	}
}
</style>
<header><h1>Data Browser</h1></header>
<form method="get">
	<label>Search</label>
	<input type="text" name="search" value="<?php echo $search; ?>">
	<label>Column</label>
	<select name="search_col">
		<?php 
			foreach($search_cols as $name => $label){
				echo '<option value="'.$name.'"';
				if($name == $search_col){
					echo ' selected';
				}
				echo '>'.$label.'</option>';
			}
		?>
	</select>
	<label>Limit</label>
	<input type="number" name="limit" value="<?php echo $limit; ?>">
	<input type="hidden" name="offset" value="0">
	<label>Order By</label>
	<select name="order_by">
		<?php 
			foreach($search_cols as $name => $label){
				if($name == 'all'){
					continue;
				}
				echo '<option value="'.$name.'"';
				if($name == $order_by){
					echo ' selected';
				}
				echo '>'.$label.'</option>';
			}
		?>
	</select>
	<label>Direction</label>
	<select name="direction">
		<?php 
			$array = [
				'ASC' => 'Ascending',
				'DESC' => 'Descending'
			];
			foreach($array as $name => $label){
				echo '<option value="'.$name.'"';
				if($name == $dir){
					echo ' selected';
				}
				echo '>'.$label.'</option>';
			}
		?>
	</select>
	<button type="submit">Search</button>
</form><br><br>
<?php echo $pageination; ?>
<article id="data-browser-main" class="flex results">
	<div class="result-row result-header flex-row">
		<span data-col="result-one" class="flex-resize result-one">ID</span>
		<span data-col="result-two" class="flex-resize result-two flex-focus">Name</span>
		<span data-col="result-three" class="flex-resize result-three">Slug</span>
		<span data-col="result-four" class="flex-resize result-four">Content (summary)</span>
		<span data-col="result-five" class="flex-resize result-five">Type</span>
		<span data-col="result-six" class="flex-resize result-six">Parent</span>
		<span data-col="result-seven" class="flex-resize result-seven">Status</span>
		<span data-col="result-eight" class="flex-resize result-eight">Created</span>
		<span data-col="result-nine" class="flex-resize result-nine">Edited</span>
		<span data-col="result-ten" class="flex-resize result-ten">User Key</span>
		<span data-col="result-eleven" class="flex-resize result-eleven"></span>
	</div>
<?php foreach($rows as $r){
	if(empty($r['data_content'])){
		$r['data_content'] = '';
	}
	echo '<form class="result-row flex-row" method="post">
	<input type="hidden" name="id" value="'.$r['data_id'].'">';
	echo '<span data-col="result-one" class="flex-resize result-one"><input type="text" name="data_id" value="'.$r['data_id'].'"></span>
		<span data-col="result-two" class="flex-resize result-two flex-focus"><input type="text" name="data_name" value="'.$r['data_name'].'"></span>
		<span data-col="result-three" class="flex-resize result-three"><input type="text" name="data_slug" value="'.$r['data_slug'].'"></span>
		<span data-col="result-four" class="flex-resize result-four">'.substr(strip_tags($r['data_content']),0,200).'</span>
		<span data-col="result-five" class="flex-resize result-five"><input type="text" name="data_type" value="'.$r['data_type'].'"></span>
		<span data-col="result-six" class="flex-resize result-six"><input type="text" name="data_parent" value="'.$r['data_parent'].'"></span>
		<span data-col="result-seven" class="flex-resize result-seven"><input type="text" name="data_status" value="'.$r['data_status'].'"></span>
		<span data-col="result-eight" class="flex-resize result-eight">'.nice_date($r['create_date']).'</span>
		<span data-col="result-nine" class="flex-resize result-nine">'.nice_date($r['last_edit']).'</span>
		<span data-col="result-ten" class="flex-resize result-ten">'.$r['user_key'].'</span>
		<span data-col="result-eleven" class="flex-resize result-eleven"><button type="submit">Update</button></span>';
	echo '</form>';
}?>
</article>
<?php echo $pageination; ?>
<script type="text/javascript">
	$(document).ready(function(){
		$('.flex-resize').click(function(){
			if(!$(this).hasClass('flex-focus')){
				var col = $(this).data('col');
				$('.flex-resize').removeClass('flex-focus');
				$('.flex-resize.'+col).addClass('flex-focus');
			}
		});
	});
</script>
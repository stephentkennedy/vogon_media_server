<?php 
	if(!isset($genres)){
		$genres = [];
	}
	if(!isset($title)){
		$title = '';
	}
	if(!isset($genre)){
		$genre = false;
	}
	if(!isset($director)){
		$director = '';
	}
	if(!isset($release)){
		$release = '';
	}
	if(!isset($starring)){
		$starring = '';
	}
	if(!isset($desc)){
		$desc = '';
	}
	if(!isset($location)){
		$location = '';
	}
	if(!isset($poster)){
		$poster = '';
	}
?>
<form method="post">
	<input type="hidden" name="action" value="save-film-meta">
	<?php if(isset($id)){
		echo '<input type="hidden" name="id" value="'.$id.'">';
	} ?>
	<label for="title">Title</label>
	<input id="title" type="text" name="title" value="<?php echo $title; ?>">
	<?php if(empty($series)){ ?>
	<label for="genre">Genre</label>
	<select id="genre" name="genre">
		<?php
			foreach($genres as $g){
				$selected = '';
				if($g['data_id'] == $genre){
					$selected = ' selected';
				}
				echo '<option value="'.$g['data_id'].'"'.$selected.'>'.$g['data_name'].'</option>';
			}
		?>
	</select>
	<?php } ?>
	<label for="director">Director</label>
	<input type="text" id="director" name="director" value="<?php echo $director; ?>">
	<label for="release">Year of Release</label>
	<input type="text" id="release" name="release" value="<?php echo $release; ?>">
	<label for="starring">Starring</label>
	<input type="text" id="starring" name="starring" value="<?php echo $starring; ?>">
	<label for="desc">Description</label>
	<textarea id="desc" name="desc"><?php echo $desc; ?></textarea>
	<label for="location">File Location</label>
	<input id="location" type="text" name="location" value="<?php echo $location; ?>">
	<label for="poster">Poster Location</label>
	<input id="poster" type="text" name="poster" value="<?php echo $poster; ?>">
	<button type="submit" class="button"><i class="fa fa-floppy-o"></i> Save</button>
</form>
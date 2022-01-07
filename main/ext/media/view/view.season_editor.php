<?php 
//debug_d($members);
?>
<style>
	#unsorted{
		max-height: calc(100vh);
		overflow: auto;
		position: sticky;
		top: 0;
	}
	.col{
		padding-left: 10px;
	}
	.season + .season{
		margin-top: 1rem;
	}
	.season input{
		display: inline-block;
		width: calc(100% - 2rem);
	}
	.season .episode-container{
		border: 1px solid rgba(var(--rgb-main-accent), 1);
		padding: 5px;
		min-height: 2rem;
	}
	a.episode{
		display: block !important;
	}
	.sort-help{
		height: 1rem;
		width: 6rem;
		border: 1px dashed white;
		background-color: #000;
	}
	#save-contain{
		text-align: right;
	}
</style>
<header><h1><?php echo $title; ?>: Season Editor <a href="<?php echo build_slug('view/'.get_slug_part(2), [], 'media'); ?>">Back</a></h1></header>
<div class="row">
	<div id="unsorted" class="col col-three">
		<h2>Unsorted</h2>
		<div class="sortable">
		<?php foreach($members['loose'] as $r){
			echo '<a class="episode" data-id="'.$r['data_id'].'">'.$r['data_name'].'</a>';
		} ?>
		</div>
	</div>
	<div id="seasons" class="col col-seven">
		<h2>Seasons <a class="add"><i class="fa fa-plus"></i></a></h2>
		<?php foreach($members['seasons'] as $s){
			echo '<div class="season"><input class="name" value="'.$s['name'].'"><div class="episode-container sortable">';
			foreach($s['episodes'] as $ord => $r){
				echo '<a class="episode" data-id="'.$r['data_id'].'">'.$r['data_name'].'</a>';
			}
			echo '</div></div>';
		}?>
	</div>
	<div style="margin-top: 10px;" id="save-contain" class="col col-ten"><strong>Note:</strong> It may take a few minutes to save this data.<br><br><button id="save"><i class="fa fa-fw fa-floppy-o"></i> Save</button></div>
</div>
<script type="text/javascript">
	var season = {};
	season.seed = '<div class="season"><input class="name" value="Season"><a class="remove"><i class="fa fa-times"></i></a><div class="episode-container sortable"></div></div>';
	season.add = function(){
		var s = $(season.seed);
		s.appendTo('#seasons');
		s.find('.remove').click(function(){
			$(this).parent().remove();
		});
		s.find('.sortable').sortable({
			containment: '#content',
			appendTo: '#content',
			connectWith: '.sortable',
			helper: 'clone'
		});
		$('.sortable').sortable('refresh');
	}
	season.init = function(){
		$('#seasons .add').click(season.add);
		$('.sortable').sortable({
			containment: '#content',
			appendTo: '#content',
			connectWith: '.sortable',
			helper: 'clone'
		});
		$('#save').click(function(){
			$(this).find('i').removeClass('fa-floppy-o').addClass('fa-spinner').addClass('fa-spin').attr('disabled', 'true');
			var seasons = $('#seasons .season');
			console.log(seasons);
			var saveSeasons = [];
			seasons.each(function(i){
				var name = $(seasons[i]).find('.name').val();
				var eTemp = $(seasons[i]).find('.episode');
				var episodes = [];
				eTemp.each(function(j){
					episodes.push($(eTemp[j]).data('id'));
				});
				saveSeasons.push({
					name: name,
					episodes: episodes
				});
			});
			var saveButton = $(this).find('i');
			$.post('<?php echo build_slug("ajax/ajax_save_season/media"); ?>', {season: saveSeasons}, function(content){
				saveButton.removeClass('fa-spin').removeClass('fa-spinner').addClass('fa-floppy-o').attr('disabled', 'false');
				var w = aPopup.newWindow(content
				);
			});
		});
	}
	$(document).ready(season.init);
</script>
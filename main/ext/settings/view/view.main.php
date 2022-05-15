<?php //debug_d($settings);
	$tabs = '';
	$divs = '';
	foreach($settings as $s){
		if(empty($s['form'])){
			continue;
		}
		$friendly = str_replace('_', ' ', $s['name']);
		$tabs .= '<li class="settings-tab button" data-id="'.$s['name'].'">'.ucwords($friendly).'</li>';
		$divs .= '<div class="settings-content" id="'.$s['name'].'">'.$s['form'].'</div>';
	}
?>
<header>
	<h1>Settings</h1>
</header>
<ul id="settings_tabs">
	<?= $tabs; ?>
</ul>
<?= $divs ?>
<style>
	#settings_tabs{
		margin-bottom: 1rem;
	}
	.settings-tab + .settings-tab{
		margin-left: 0.5rem;
	}
	.settings-content{
		display: none;
		padding: 1rem;
		border: 1px solid var(--shadows);
	}
	.settings-content.active{
		display: block;
	}
</style>
<script type="text/javascript">
	var settings_page = {
		current_tab: false,
		get_vars: function(){
            var url = new URL(window.location);
            var s = url.search;
            if(s.length == 0){
                return {'page': 0};
            }
            s = s.split('&');
            s[0] = s[0].replace('?', '');
            var params = {};
            for(var i = 0; i < s.length; i++){
                var temp = s[i].split('=');
                params[temp[0]] = decodeURI(temp[1]);
            }
            return params;
        },
		push_history: function(tab){
            var url = '?tab=' + tab;
            var data = {
                'format': 'HTML',
                'tab': tab,
            };
            history.pushState(data, '', url);
        },
        pop_history: function(e){
            if(
                e.state == null
                || e.state.tab != undefined
            ){
                var params = settings_page.get_vars();
                var tab = params.tab;
            }else{
                var tab = e.state.tab;
            }
            settings_page.active_tab(tab);
        },
		active_tab: function(tab, push){
			if(push == undefined){
				push = false;
			}
			if(tab == settings_page.current_tab){
				return;
			}
			$('.settings-tab').removeClass('active');
			$('.settings-tab[data-id="'+tab+'"]').addClass('active');
			$('.settings-content').removeClass('active');
			$('#'+tab).addClass('active');
			if(push == true){
				settings_page.push_history(tab);
			}
		}
	};
	$(document).ready(function(){
		$('.settings-tab').click(function(){
			var id = $(this).data('id');
			settings_page.active_tab(id, true);
		});
		var params = settings_page.get_vars();
        var tab = params.tab;
		if(tab == undefined){
			tab = 'framework';
		}
		window.onpopstate = settings_page.pop_history;
		settings_page.active_tab(tab);
	});
</script>
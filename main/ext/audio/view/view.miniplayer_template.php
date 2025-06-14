<div class="mini-player">
    <header class="mini-player-header">
        <span class="shadow"></span>
        <span class="controls">
            <i class="fa fa-window-maximize toggle"></i>
            <i class="fa fa-cog option"></i>
            <i class="fa fa-plus exclusive-fullscreen"></i>
            <i class="fa fa-expand fullscreen"></i>
            <!--i class="fa fa-times close"></i-->
        </span>
    </header>
    <info>
        <a target="_blank" data-target="popup" class="mini-player-label title"></a><a target="_blank" data-target="popup" class="mini-player-label album"></a><a target="_blank" data-target="popup" class="mini-player-label band"></a>
    </info>
    <canvas></canvas>
    <audio class="current-track">
        <source class="current-source" onerror="miniplayer.error"></source>
    </audio>
    <div class="mini-player-audio-controls">
        <i class="fa fa-heart-o favorite"></i>
        <input type="range" class="seek" value="0" max="" />
        <span class="mini-player-seek-counter hidden"></span>
        <span class="mini-player-counter">0:00 / 0:00</span><br>
        <i class="fa fa-random  fa-fw mini-player-shuffle disable"></i>
        <i class="fa fa-step-backward fa-fw  mini-player-prev disable"></i>
        <i class="fa fa-play fa-fw  mini-player-play"></i>
        <i class="fa fa-step-forward fa-fw  mini-player-next disable"></i>
        <i class="fa fa-retweet fa-fw  mini-player-loop disable"></i>
        <span class="mini-one">1</span>
    </div>
    <i class="fa fa-clock-o sleep-timer"></i>
    <div class="mini-player-playlist"></div>
</div>
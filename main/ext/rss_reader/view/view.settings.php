<h2>RSS Reader Settings</h2>
<form action="?ext={{ext_name}}&form=timeout" method="post">
    <label for="rss_timeout">Feed Timeout (In Seconds)</label>
    <small>This is how long the server will wait for a response from a single RSS feed before moving on. If you are having a hard time getting RSS data on a poor connection raising this value could help, but it will extend how long your pageload can take if an external server is unresponsive.</small>
    <input type="number" value="<?= $rss_timeout; ?>" name="rss_timeout" id="rss_timeout" min="1">
    <button type="submit"><i class="fa fa-floppy-o"></i> Save</button>
</form>
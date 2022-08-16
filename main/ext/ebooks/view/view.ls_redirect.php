<script type="text/javascript">
    var url = new URL(window.location);
    var ls = window.localStorage;
    var old_search = ls.getItem('ebook_search');
    url.search = old_search;
    window.location = url.href;
</script>
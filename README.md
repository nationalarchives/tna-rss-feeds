# tna-rss-feeds

For testing - displays TNA blog RSS feed via a shortcode and caches data using Transients API

Use shortcode : 

[tna-rss] or [tna-rss url='http://blog.nationalarchives.gov.uk/feed/' number=9]

For PHP usage :
```
<?php tna_rss ( $rssUrl, $url, $rssTitle, $image, $id ) ?>
```

eg.
```
<?php tna_rss ( 'http://blog.nationalarchives.gov.uk/feed/', 'http://blog.nationalarchives.gov.uk/', 'Our blo', 'yes', 'home-1' ) ?>
```

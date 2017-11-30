# PHP URL Finder

Find and replace URLs in HTML and CSS documents. 


Example input HTML:

```HTML
<html>
<body>
  <img src="http://domain.tld/img/a.jpg" >
  <div style="background-image: url(./c.jpg);"></div>
  <style>
    .bg-img { background-image: url(../images/h.jpg); }
  </style>
  <script src="https://cdn.tld/angular.min.js"></script>
  <link rel="stylesheet" type="text/css" href="/styles/f.css" />
  ...
```

Find all jpegs on our domain and move them to /images/

```PHP
$documentUrl = 'http://domain.tld/products/all.html';
$finder = UrlFinder::create($html, $documentUrl);

foreach ($finder->find('*domain.tld/*.jpg') as $url) {
  $newpath = '/images/' . $url->path->filename();
  $url
    ->replacePath($newpath)
    ->makeAbsolute()
    ->clearHost();
}

$finder->getDocument(); // returns the updated HTML string
```

The result:

```HTML
<html>
<body>
  <img src="/images/a.jpg" >
  <div style="background-image: url(/images/c.jpg);"></div>
  <style>
    .bg-img { background-image: url(/images/h.jpg); }
  </style>
  <script src="https://cdn.tld/angular.min.js"></script>
  <link rel="stylesheet" type="text/css" href="/styles/f.css" />
  ...
```

The UrlFinder takes care of proper quoting of URLs in 
attributes, url-notations in style-attributes and url-
notation within style-tags.


Using the fluid collection interface:

```PHP
$urls = $finder
  ->find('*') // matches the entire absolute URL
  ->matchHost('*')
  ->matchPath('*')
  ->onlyHttps()
  ->matchFilenameNot('*.less');
  // etc.
  
$urls->count();
$urls->toArray();
$urls->first();
foreach($urls as $url) {}
```


Updating URLs:  

```PHP
$url->query->set('text', 'value');
$url->clear( Url::CREDENTIALS );
```

See https://github.com/timostamm/url-builder for documentation 
of the URL object.

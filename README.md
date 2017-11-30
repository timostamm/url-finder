# PHP URL Finder

Find and replace URLs in HTML and CSS documents. 

Example: Find all jpegs on our domain and move them to /images/

```PHP
$html = <<<HTML
<html>
<body>
  <img src="http://domain.tld/img/a.jpg" >
  <div style="background-image: url(./c.jpg);"></div>
  <style>
    .bg-img { background-image: url(../images/h.jpg); }
  </style>
  <script src="https://cdn.tld/angular.min.js"></script>
  <link rel="stylesheet" type="text/css" href="/styls/f.css" />
HTML;

$finder = UrlFinder::create($html, 'http://domain.tld/products/all.html');

foreach ($finder->find('*domain.tld/*.jpg') as $url) {
  $newpath = '/images/ . $url->path->filename();
  $url->replacePath($newpath);
}

$finder->getDocument(); // returns the updated HTML string
```


Finding URLs via the fluid collection:

```PHP
$finder
  ->find('*') // matches the entire absolute URL
  ->matchHost('*')
  ->matchPath('*')
  ->onlyHttps()
  ->matchFilenameNot('*.less')
```


Updating URLs:  

```PHP
$url->query->set('text', 'value');
$url->clear( Url::CREDENTIALS );
```

See https://github.com/timostamm/url-builder for documentation 
of the URL object.

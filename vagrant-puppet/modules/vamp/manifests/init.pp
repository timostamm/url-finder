class vamp {
  class {'vamp::base': }
  class {'vamp::imagemagick': }
  
  if $::php == '7' {
  	class {'vamp::php7': }
  } elsif $::php == '5' {
    class {'vamp::php5': }
  }
  
  class {'vamp::phpdocumentor': }
  class {'vamp::phpunit': }
  class {'vamp::composer': }
  class {'vamp::git': }
  
}

class vamp::phpdocumentor{
    exec{ 'pear install phpdocumentor':
      require => [Package['php-pear'],Exec['pear update','pear auto_discover']],
      creates => '/usr/bin/phpdoc',
      command => '/usr/bin/pear install --alldeps pear.phpdoc.org/phpdocumentor-alpha',
    }
}

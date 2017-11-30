class vamp::phpunit{
   package{ 'phpunit':
     ensure => absent,
   }
   exec{ 'pear install phpunit':
     command => '/usr/bin/wget https://phar.phpunit.de/phpunit.phar && chmod +x phpunit.phar && mv phpunit.phar /usr/local/bin/phpunit',
     creates => '/usr/local/bin/phpunit',
     require => [Package['phpunit','php-pear']],
     timeout     => 1800,
   }
}
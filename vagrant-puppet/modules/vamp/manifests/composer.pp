class vamp::composer{
	
    exec{ 'composer installer':
	    creates => '/tmp/installer',
	    command => '/usr/bin/curl -sS -o /tmp/installer https://getcomposer.org/installer',
	    require => Package['php'],
    }
    file{ '/tmp/installer':
	    owner => 'root',
	    mode=> '777',
	    require=> Exec['composer installer'],
    }
	exec{ 'composer php':
		creates => '/tmp/composer.phar',
		command => '/usr/bin/php /tmp/installer --install-dir=/tmp/',
		user => "vagrant",
		environment => ["HOME=/home/vagrant"],
		require=> [File['/tmp/installer'],Exec['composer installer'], Package['unzip']]
    }
    package{ 'unzip':
    		ensure => latest,
    		require => Exec['update']
    }
    exec{ 'composer move':
	    command => '/bin/mv /tmp/composer.phar /usr/local/bin/composer',
	    require=> Exec['composer php'],
    }
    exec{ 'composer config':
	    command => "/usr/local/bin/composer config -g github-oauth.github.com $::github_access_token", 
	    user => "vagrant",
	    environment => ["HOME=/home/vagrant"],
	    require=> Exec['composer move'],
    }
     
}

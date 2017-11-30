class vamp::imagemagick{
  package{ 'imagemagick':
    require => Exec['update'],
    ensure => present,
  }
}

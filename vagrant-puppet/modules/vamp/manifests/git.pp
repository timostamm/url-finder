class vamp::git{
  package { 'git':
    ensure => latest,
    require => Exec['update'],
  }
}
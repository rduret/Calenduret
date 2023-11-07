<?php
namespace Deployer;

require 'recipe/symfony.php';

// Config

set('repository', 'git@github.com:rduret/advent-calendars.git');
//set('http_user', 'www-data');
add('shared_files', []);
add('shared_dirs', []);
add('writable_dirs', []);

// Hosts
host('production')
    ->set('hostname', '62.72.16.63')
    ->set('remote_user', 'rduret')
    ->set('deploy_path', '~/htdocs/calenduret.com');

// Hooks
task('build', function () {
    cd('{{release_path}}');
    run('npm install');
    run('npm run build');
});

after('deploy:update_code', 'build');
after('deploy:vendors', 'database:migrate');
after('deploy:failed', 'deploy:unlock');

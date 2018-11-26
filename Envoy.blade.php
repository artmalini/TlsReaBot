@setup
    //web-server user
    $user = 'xjsenokw';

    $timezone = 'Europe/Kyiv';

    //path to the directory on web-server
    $path = '/public_html/thunderreg';

    $current = $path . '/current';

    //repo (github repository)
    $repo = 'git@github.com:artmalini/TlsReaBot.git';

    $branch = 'master';

    //Directory and files with chmod 775
    $chmod = [
        'storage/logs'
    ];

    $date = new DateTime('now', new DateTimeZone($timezone));
    $release = $path . '/releases/' . $date->format('YmdHis');
@endsetup

@servers(['production' => $user . '@176.111.49.5'])

@task('clone', ['on' => $on])
    mkdir -p {{ $release }}

    git clone --depth 1 -b {{ $branch }} "{{ $repo }}" {{ $release }}

    echo "#1 - Repository has been cloned"
@endtask

{{-- runs fresh installation --}}
@task('composer', ['on' => $on])
    composer self-update

    cd {{ $release }}

    composer install --no-interaction --no-dev --prefer-dist

    echo "#2 - Composer dependencies has been installed"
@endtask

{{-- update composer, then runs a fresh installation --}}
@task('artisan', ['on' => $on])
    cd {{ $release }}

    ln -nfs {{ $path }}/.env .env;
    chgrp -h www-data .env;

    php artisan config:clear

    php artisan migrate
    php artisan clear-compiled --env=production;
    php artisan optimize --env=production;

    echo "#3 - Production dependencies has been installed"
@endtask

{{--set permission for various files and directories--}}
@task('chmod', ['on' => $on])
    chgrp -R www-data {{ $release }};
    chmod -R ug+rwx {{ $release }};

    @foreach($chmods as $file)
        chmod -R 775 {{ $release }}/{{ $file }}
        chown -R {{ $user }}:www-data {{ $release }}/{{ $file }}

        echo "Permissions has been set for {{ $file }}"
    @endforeach
    echo "#4 - Permissions has been set"
@endtask

@task('update_symlinks)
    ln -nfs {{ $release }} {{ $current }};;
    chgrp -h www-data {{ $current }};

    echo "#5 - Symlink has been set"
@endtask

{{-- Run all deployment task --}}
@macro('deploy', ['on' => 'production'])
    clone
    composer
    artisan
    chmod
    update_symlinks
@endmacro
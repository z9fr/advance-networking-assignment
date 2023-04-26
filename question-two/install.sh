#!/bin/bash

function validate_web_root {
    if [ ! -d "$1" ]; then
        echo "path you enterd does not exist."
        exit 1
    fi
}

function install_dependencies {
    # apperely to make opendaylight work we need java but the issue is
    # to set this up it has a dependacy for xml and its apperely removed from jdk 11 
    # and we need to setup jdk 8 for this
    sudo apt-get update
    sudo apt-get install curl openjdk-8-jdk mininet apache2 firefox gpac x264 ffmpeg -y
}

function download_and_configure_dash {
    wget https://github.com/Dash-Industry-Forum/dash.js/archive/refs/heads/development.zip -o /tmp
    unzip /tmp/development.zip
    mv /tmp/dash.js-development "$1/dash.js"

}

function generate_sample_videos {
    mkdir /tmp/output
    pushd /tmp/output
    ffmpeg -i sample_video.mp4 -vf "scale=852:480" -c:v libx264 -crf 23 -c:a copy sample_video_resized.mp4
    ffmpeg -i sample_video_resized.mp4 -c:v libx264 -crf 23 -c:a copy sample_video_resized_720p.mp4
    ffmpeg -i sample_video_resized.mp4 -vf scale=-1:480 -c:v libx264 -crf 23 -c:a copy sample_video_resized_480p.mp4
    ffmpeg -i sample_video.mp4 -vf scale=-1:360 -c:v libx264 -crf 23 -c:a copy sample_video_resized_360p.mp4
    MP4Box -dash 2000 -frag 2000 -rap -profile dashavc264:live -out /tmp/output/manifest.mpd sample_video_resized_720p.mp4#video sample_video_resized_480p.mp4#video sample_video_resized_360p.mp4#video sample_video_resized.mp4#audio
    mv /tmp/output "$1/dash.js"
    wget https://cdnjs.cloudflare.com/ajax/libs/dashjs/4.0.1/dash.all.min.js -O "$1/dash.js/dash.all.min.js"
    popd
}


function setup_webfiles {
    sed -i "s/{IP_ADDR}/$2/g" www/index.html
    cp -r www/* "$1/dash.js"
}

function configure_apache {
    cat > /etc/apache2/sites-available/000-default.conf <<EOF
<VirtualHost *:80>
    DocumentRoot $1
    <Directory $1>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
EOF
    sudo systemctl restart apache2
}

if [ "$#" -ne 1 ]; then
    echo "web root is required"
    exit 1
fi

WEB_ROOT="$1"
IP_ADDR=$(hostname -I | awk '{print $1}')

validate_web_root "$WEB_ROOT"
install_dependencies
download_and_configure_dash "$WEB_ROOT"
setup_webfiles $WEB_ROOT $IP_ADDR
generate_sample_videos "$WEB_ROOT"
configure_apache "$WEB_ROOT"

echo "setup is complete."
echo "run topology.py to start the topology."
echo "visit http://$IP_ADDR/dashjs/index.html to see the dashjs player."
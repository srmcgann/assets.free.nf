#!/bin/bash
git pull
cd /home/scottmcgann4/assets.free.nf
cp /home/scottmcgann4/assets.free.nf/dist/. /home/scottmcgann4/assets.free.nf/dist_public/. -r
php /home/scottmcgann4/assets.free.nf/push_dir.php
git add .
git commit -m 'sync'
git push

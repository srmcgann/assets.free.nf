#!/bin/bash
git pull
cd ~/assets.free.nf
cp ~/assets.free.nf/dist/. ~/assets.free.nf/dist_public/. -r
php ~/assets.free.nf/push_dir.php
git add .
git commit -m 'sync'
git push

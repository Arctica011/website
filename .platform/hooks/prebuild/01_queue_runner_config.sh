#!/bin/bash
sudo touch /etc/systemd/system/laravel_worker.service
sudo chmod 777 /etc/systemd/system/laravel_worker.service
sudo cat > /etc/systemd/system/laravel_worker.service << EOF
            # Laravel queue worker using systemd
            # ----------------------------------
            #
            # /lib/systemd/system/queue.service
            #
            # run this command to enable service:
            # systemctl enable queue.service

            [Unit]
            Description=Laravel queue worker

            [Service]
            User=webapp
            Group=webapp
            StandardOutput=file:/var/log/worker.log
            StandardError=file:/var/log/worker.error.log
            Restart=always
            ExecStart=/usr/bin/nohup /usr/bin/php /var/app/current/artisan queue:work --daemon

            [Install]
            WantedBy=multi-user.target
EOF
sudo chmod 755 /etc/systemd/system/laravel_worker.service

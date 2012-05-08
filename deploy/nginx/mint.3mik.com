server {
	listen 80;
	server_name   mint.3mik.com ;
	root /var/www/vhosts/mint.3mik.com/htdocs  ;
	index index.php index.html ;

	server_name_in_redirect  off;
	try_files $uri $uri/ /index.php?q=$uri&$args;
	
	# for vhost location php context we need to set 
	# Document root explicitly, otherwise we get
	# NO INPUT file specified error

    location ~* \.(js|css|png|jpg|jpeg|gif)$ {
        expires 30d ;
        break ;
    }		
	
	location ~ \.php$ {
        fastcgi_read_timeout 600 ;
		fastcgi_pass 127.0.0.1:9100 ;
	}  

}


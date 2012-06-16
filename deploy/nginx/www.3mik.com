server {
	listen 80;
	server_name   mint.3mik.com ;
	root /var/www/vhosts/www.3mik.com/htdocs  ;
	index index.php index.html ;

	server_name_in_redirect  off;
	try_files $uri $uri/ /index.php?q=$uri&$args;
	
    location ~* \.(js|css|png|jpg|jpeg|gif)$ {
        expires 30d ;
        break ;
    }		
	
	location ~ \.php$ {
        try_files $uri =404;
        fastcgi_read_timeout 600 ;
		fastcgi_pass 127.0.0.1:9100 ;
	}  

}


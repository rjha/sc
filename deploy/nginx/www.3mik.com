server {
    listen 80 ;
    server_name 3mik.com ;
    rewrite ^(.*) http://www.3mik.com$1 permanent;
}

server {
	listen 80;
    error_page 503 @503 ;
    #return 503 ;
	server_name   mint.3mik.com ;
	root /var/www/vhosts/www.3mik.com/htdocs  ;
	index index.php index.html ;

	server_name_in_redirect  off;
    #rewrite rule for auto versioning
    rewrite ^/(css|js)/(.*)\.t(\d+)\.(css|js)$  /$1/$2.$4 ;

	try_files $uri $uri/ /index.php?q=$uri&$args;
	
    location @503 {
        #avoid redirect to built-in 503.
        try_files /site/503.html =503;
    }

    location ~* \.(js|css|png|jpg|jpeg|gif|ico)$ {
        expires 30d ;
        break ;
    }		
	
	location ~ \.php$ {
        try_files $uri =404;
        fastcgi_read_timeout 600 ;
		fastcgi_pass 127.0.0.1:9100 ;
	}  

}


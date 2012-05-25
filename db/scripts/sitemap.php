#!/usr/bin/php
<?php 

    include('sc-app.inc');
    include(APP_CLASS_LOADER);
    include(WEBGLOO_LIB_ROOT . '/com/indigloo/error.inc');

    use \com\indigloo\mysql as MySQL;
    set_error_handler('offline_error_handler');

    function write_on_disk($fileName,$content) {
        $fp = fopen($fileName, 'w');
        fwrite($fp, $content);
        fclose($fp);
    }

    function create_index() {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' ;
        $xml .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"> </sitemapindex>';
        $xmlDoc  = new SimpleXMLElement($xml);
        //$maps = array(0=>'groups', 1=>'items', 2=>'categories',3 =>'locations');
        $maps = array(0=>'groups', 1=>'items');

        foreach($maps as $map){
            $loc = "http://www.3mik.com/sitemap_%s.xml" ;
            $loc = sprintf($loc,$map);

            $siteNode = $xmlDoc->addChild('sitemap');
            $siteNode->addChild('loc',$loc);
            //http://www.w3.org/TR/NOTE-datetime/
            $siteNode->addChild('lastmod',date('Y-m-d'));
        }


        $xmlString = $xmlDoc->asXML(); 
        //write to file
        write_on_disk('sitemap.xml',$xmlString);
    }

    function create_groups($mysqli) {
        //get 5000 latest groups
        $xml = '<?xml version="1.0" encoding="UTF-8"?> ' ;
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">  </urlset>' ;
        $xmlDoc  = new SimpleXMLElement($xml);

        $sql = "select g.token from sc_group_master g order by g.id desc limit 5000 ";
        $rows = MySQL\Helper::fetchRows($mysqli, $sql);

        foreach($rows as $row) {
            $loc = "http://www.3mik.com/group/%s";
            $loc = sprintf($loc,$row['token']);
            $urlNode = $xmlDoc->addChild('url');
            $urlNode->addChild('loc',$loc);
            $urlNode->addChild('lastmod',date('Y-m-d'));
        }

        $xmlString = $xmlDoc->asXML(); 
        //write to file
        write_on_disk('sitemap_groups.xml',$xmlString);
    }

    function create_items($mysqli) {
        //get 5000 latest items
        $xml = '<?xml version="1.0" encoding="UTF-8"?> ' ;
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">  </urlset>' ;
        $xmlDoc  = new SimpleXMLElement($xml);

        $sql = "select p.pseudo_id from sc_post p order by p.id desc limit 5000 ";
        $rows = MySQL\Helper::fetchRows($mysqli, $sql);

        foreach($rows as $row) {
            $loc = "http://www.3mik.com/item/%s";
            $loc = sprintf($loc,$row['pseudo_id']);
            $urlNode = $xmlDoc->addChild('url');
            $urlNode->addChild('loc',$loc);
            $urlNode->addChild('lastmod',date('Y-m-d'));
        }

        $xmlString = $xmlDoc->asXML(); 
        //write to file
        write_on_disk('sitemap_items.xml',$xmlString);
    }


    $mysqli = MySQL\Connection::getInstance()->getHandle();
    create_index();
    sleep(2);
    create_groups($mysqli);
    sleep(2);
    create_items($mysqli);

   ?>

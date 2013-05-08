<?php
	
	require_once("../lib/arc2/ARC2.php");
	
	$config = array(
	   /* db */
	   'db_host' => 'localhost',
	   'db_name' => 'rdfstore',
	   'db_user' => 'root',
	   'db_pwd' => '',
	   /* store name */
	   'store_name' => 'rdfstore',
	   /* instructinos accepted */
	   'endpoint_features' => array(
		 'select', 'construct', 'ask', 'describe', 
		 'load', 'insert', 'delete','dump'     
	   ),
	 
	   'endpoint_timeout' => 60, /* not implemented in ARC2 preview */
	   'endpoint_max_limit' => 0, /* optional */
	 );
				
	
	$ep = ARC2::getStoreEndpoint($config);
	
	 if (!$ep->isSetUp()) {
   $ep->setUp(); /* create MySQL tables */
 }
 
 //$ep->query("LOAD <http://bibotools.googlecode.com/svn/bibo-ontology/tags/1.3/bibo.xml.owl>");
 
		$ep->handleRequest();
		$ep->sendHeaders();
		echo $ep->getResult(); 
		
	
?>
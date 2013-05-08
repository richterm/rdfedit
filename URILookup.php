<?php
	require_once("lib/arc2/ARC2.php");

	if(($_REQUEST['uri']) && ($_REQUEST['uri']!=''))
	{
		$uri=trim($_REQUEST['uri']);
		if(substr($uri, 0, 9)=='urn:isbn:')
		{
			$ch = curl_init();
			
			$isbn=substr(str_replace("-", "", $uri), 9);
			
			curl_setopt($ch, CURLOPT_URL, "http://xisbn.worldcat.org/webservices/xid/isbn/" . $isbn . "?method=getMetadata&format=json&fl=*");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

			$result=json_decode(curl_exec($ch));
			if($result->stat=='ok')
			{
				$fields=array('title' => 'http://purl.org/dc/terms/title' , 'author' => 'http://purl.org/dc/terms/creator', 'publisher' => 'http://purl.org/dc/terms/publisher', 'ed' => 'http://purl.org/ontology/bibo/edition', 'year' => 'http://purl.org/dc/terms/date');
				
				$output="";
				foreach($result->list as $item)
				{
					$item=(array)$item;
					foreach(array_keys($fields) as $field)
					{
						if($item[$field])
						{
							$triple="<urn:isbn:" . $isbn . "> <" . $fields[$field] . "> \"" . trim($item[$field]) . "\" .";
							$output=$output . $triple;
						}
					}
					$output=$output . "<urn:isbn:" . $isbn . "> <http://purl.org/dc/terms/bibliographicCitation> \"" . trim($item['title']) . " / " . trim($item['author']) . ". - " . trim($item['ed']) . ". - " . trim($item['city']) . " : " . trim($item['publisher']) . ", " . trim($item['year']) . ". - ISBN " . $isbn . "\" .";
				}
				$parser = ARC2::getRDFParser();
				$parser->parse("", $output);
				if($_REQUEST['format']=="json")
				{
					header('Content-type: application/json');
					print('');
					print($parser->toRDFJSON($parser->getTriples()));
				}
				else
				{
					header('Content-type: application/rdf+xml');
					print('');
					print($parser->toRDFXML($parser->getTriples()));
				}
			}
			else
			{
				if($_REQUEST['format']=="json")
				{
					header('Content-type: application/json');
					print('');
					print('{ }');
				}
				else
				{
					header('Content-type: application/rdf+xml');
					print('');
					print('<rdf:RDF xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"></rdf:RDF>');
				}
			}
			
			curl_close($ch);
		}
		else
		{
			$ch = curl_init();

			curl_setopt($ch, CURLOPT_URL, $uri);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/rdf+xml'));
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

			$result=curl_exec($ch);
			//print($result);
			
			$parser = ARC2::getRDFParser();
			$parser->parse("", $result);
			if($_REQUEST['format']=="json")
			{
				header('Content-type: application/json');
				print('');
				print($parser->toRDFJSON($parser->getTriples()));
			}
			else
			{
				header('Content-type: application/rdf+xml');
				print('');
				print($parser->toRDFXML($parser->getTriples()));
			}
			
			curl_close($ch);
		}
	}
?>
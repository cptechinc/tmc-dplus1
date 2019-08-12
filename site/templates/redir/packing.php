<?php
	// Figure out page request method, then grab needed inputs
	$requestmethod = $input->requestMethod('POST') ? 'post' : 'get';
	$action = $input->$requestmethod->text('action');
	$dplusdb = $modules->get('DplusOnlineDatabase')->db_name;

	// Set up filename and sessionID in case this was made through cURL
	$filename  = ($input->$requestmethod->sessionID) ? $input->$requestmethod->text('sessionID') : session_id();
	$sessionID = ($input->$requestmethod->sessionID) ? $input->$requestmethod->text('sessionID') : session_id();

	switch ($action) {
		case 'start-packing':
			$ordn = $input->$requestmethod->text('ordn');
			$data = array("DBNAME=$dplusdb", "STARTPACK", "ORDERNBR=$ordn");
			$url = new Purl\Url($input->$requestmethod->text('page'));
			$url->query->set('ordn', $ordn);
			$session->loc = $url->getUrl();
			$session->remove('cartoncount');
			break;
		case 'get-pack-notes':
			$ordn = $input->$requestmethod->text('ordn');
			$data = array("DBNAME=$dplusdb", "LQNOTE=SORD", "KEY1=$ordn", "KEY2=0");
			break;
		case 'save-packing-box':
			$ordn = SalesOrder::get_paddedordernumber($input->$requestmethod->text('ordn'));
			$box = $input->$requestmethod->int('box');
			$data = array("DBNAME=$dplusdb", 'PACKCARTON', "ORDERNBR=$ordn", "CARTON=$box");
			$packquery = WhseitempackQuery::create()->filterBySessionidOrder($sessionID, $ordn);
			$packquery->filterByCarton($box);
			$packquery->groupBy(array('itemid', 'lotserial'));
			$packed_items = $packquery->find();

			foreach ($packed_items as $packed) {
				$data[] = "LINENBR=$packed->linenumber|ITEM=$packed->itemid|LOTSER=$packed->lotserial|QTY=$packed->qty";
			}

			if ($input->$requestmethod->page) {
				$session->loc = $input->$requestmethod->text('page');
			}
			break;
		case 'finish-pack':
			$ordn = $input->$requestmethod->text('ordn');
			$data = array("DBNAME=$dplusdb", 'FINISHORDER', "ORDERNBR=$ordn");

			if ($input->$requestmethod->page) {
				$session->loc = $input->$requestmethod->text('page');
			} else {
				$url = new Purl\Url($pages->get('pw_template=whse-packing')->url);
				$url->query->set('ordn', $ordn);
				$url->query->set('finish', 'true');
				$session->loc = $url->getUrl();
			}
			break;
		case 'print-packing':
			$ordn = $input->$requestmethod->text('ordn');
			$data = array("DBNAME=$dplusdb", 'PRINTPACKING', "ORDERNBR=$ordn");

			if (LabelPrintSessionQuery::create()->filterBySessionid(session_id())->count()) {
				$labelsession = LabelPrintSessionQuery::create()->findOneBySessionid(session_id());
			} else {
				$labelsession = new LabelPrintSession();
				$labelsession->setSessionid(session_id());
				$labelsession->setItemid($ordn);
			}
			// PRINT INVOICE
			if (strtoupper($input->get->text('print-invoice')) == 'Y') {
				$labelsession->setPrinterBox($input->$requestmethod->text('invoice-printer'));
			}

			// PRINT PACK TICKET
			if (strtoupper($input->get->text('print-packticket')) == 'Y') {
				$labelsession->setPrinterMaster($input->$requestmethod->text('packticket-printer'));
			}
			$labelsession->save();

			if ($input->$requestmethod->page) {
				$session->loc = $input->$requestmethod->text('page');
			} else {
				$url = new Purl\Url($pages->get('pw_template=whse-packing')->url);
				$session->loc = $url->getUrl();
			}
			break;
	}

	if (!empty($data)) {
		write_dplusfile($data, $filename);
		$http = new WireHttp();
		$http->get("127.0.0.1/cgi-bin/".$config->cgis['warehouse']."?fname=$filename");
	}

	if (!empty($session->get('loc')) && !$config->ajax) {
		$session->redirect($session->loc);
	}

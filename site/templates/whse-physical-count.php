<?php
	$whsesession = WhsesessionQuery::create()->findOneBySessionid(session_id());
	$warehouse   = WarehouseQuery::create()->findOneByWhseid($whsesession->whseid);
	$html = $modules->get('HtmlWriter');

	$page->formurl = $page->parent('template=warehouse-menu')->child('template=redir')->url;

	if ($input->get->scan) {
		$scan = $input->get->text('scan');
		$page->scan = $scan;
		$page->title = "Find Item Inquiry for $scan";

		$query_phys = WhseitemphysicalcountQuery::create();
		$query_phys->filterBySessionid(session_id());
		$query_phys->filterByScan($scan);

		if ($query_phys->count() == 1) {
			$physicalitem = $query_phys->findOne();
			$page->body = $config->twig->render('warehouse/inventory/physical-count/physical-count-form.twig', ['page' => $page,'item' => $physicalitem]);
		} elseif ($query_phys->count() > 1) {

		} else {
			if ($input->get->q) {
				$q = $input->get->text('q');
				$item_master = ItemMasterItemQuery::create();
				$item_master->filterByItemid($q);
				$item_master->count();

				if ($item_master->count()) {
					$itemID = $q;
					$physicalitem = new Whseitemphysicalcount();
					$physicalitem->setSessionid(session_id());
					$physicalitem->setRecno(0);
					$physicalitem->setScan($scan);
					$physicalitem->setitemid($itemID);
					$physicalitem->setType($item_master->get_itemtype($itemID));
					$physicalitem->save();
					$session->redirect($page->url."?scan=$scan");
				} else {
					$page->title = "No results found for ''$q'";
					$page->formurl = $page->url;
					$page->body .= $config->twig->render('warehouse/inventory/physical-count/itemid-search-form.twig', ['page' => $page]);
				}

			} else {
				$page->title = "No results found for ''$scan'";
				$page->formurl = $page->url;
				$page->body .= $config->twig->render('warehouse/inventory/physical-count/itemid-search-form.twig', ['page' => $page]);
			}
		}
	} else {
		$page->body = $config->twig->render('warehouse/inventory/physical-count/item-search-form.twig', ['page' => $page]);
	}

	$bins = BincntlQuery::create()->get_warehousebins($whsesession->whseid)->toArray();
	$jsconfig = array('warehouse' => array('id' => $whsesession->whseid, 'binarrangement' => $warehouse->get_binarrangementdescription(), 'bins' => $bins));
	$page->body .= $config->twig->render('util/js-variables.twig', ['variables' => array('warehouse' => $jsconfig)]);
	$config->scripts->append(hash_templatefile('scripts/lib/jquery-validate.js'));
	$config->scripts->append(hash_templatefile('scripts/warehouse/physical-count.js'));

	include __DIR__ . "/basic-page.php";
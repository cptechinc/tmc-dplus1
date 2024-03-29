<?php
	use ItemsearchQuery, Itemsearch;

	$module_ii = $modules->get('MiiPages');
	$module_ii->init_iipage();
	$html = $modules->get('HtmlWriter');

	if ($input->get->itemID) {
		$itemID = $input->get->text('itemID');
		$query = ItemsearchQuery::create();
		$query->filterActive();
		$query->filterByOrigintype([Itemsearch::ORIGINTYPE_VENDOR, Itemsearch::ORIGINTYPE_ITEM]);
		$query->filterByItemid($itemID);

		if ($query->count()) {
			$page->title = "Item Information: $itemID";
			$item = ItemMasterItemQuery::create()->findOneByItemid($itemID);
			$itempricing = ItemPricingQuery::create()->findOneByItemid($itemID);
			$module_json = $modules->get('JsonDataFiles');
			$json = $module_json->get_file(session_id(), 'ii-stock');

			$toolbar = $config->twig->render('items/ii/toolbar.twig', ['page' => $page, 'item' => $item]);
			$description = $config->twig->render('items/ii/item/description.twig', ['item' => $item, 'page' => $page]);
			$itemdata = $config->twig->render('items/ii/item/item-data.twig', ['item' => $item, 'itempricing' => $itempricing]);

			if ($module_json->had_succeeded()) {
				$stock = $config->twig->render('items/ii/item/stock.twig', ['module_json' => $module_json, 'json' => $json]);
			} else {
				$stock = $config->twig->render('util/alert.twig', ['type' => 'danger', 'title' => "JSON Decode Error", 'iconclass' => 'fa fa-warning fa-2x', 'message' => $module_json->get_error()]);
			}

			$page->body = "<div class='row'>";
				$page->body .= $html->div('class=col-sm-2', $toolbar);
				$page->body .= $html->div('class=col-sm-10', $description.$itemdata.$stock);
			$page->body .= "</div>";

		} else {
			$page->headline = $page->title = "Item $itemID could not be found";
			$page->body = $config->twig->render('util/error-page.twig', ['title' => $page->title, 'msg' => "Check if the item ID is correct"]);
		}
	} else {
		$q = $input->get->q ? $input->get->text('q') : '';
		$query = ItemsearchQuery::create();
		$query->filterActive();
		$query->filterByOrigintype([Itemsearch::ORIGINTYPE_VENDOR, Itemsearch::ORIGINTYPE_ITEM]);

		if ($query->filterByItemid($q)->count()) {
			$query->groupby('itemid');
		} else {
			$query->clear();
			$query->filterActive();
			$query->filterByOrigintype([Itemsearch::ORIGINTYPE_VENDOR, Itemsearch::ORIGINTYPE_ITEM]);
			$query->where("MATCH(Itemsearch.itemid, Itemsearch.refitemid, Itemsearch.desc1, Itemsearch.desc2) AGAINST (? IN BOOLEAN MODE)", "*$q*");
			$query->groupby('itemid');
		}

		if ($query->count() == 1) {
			$item = $query->findOne();
			$session->redirect($page->get_itemURL($item->itemid));
		} else {
			$items = $query->paginate($input->pageNum, 10);
		}

		$page->searchURL = $page->url;
		$page->body = $config->twig->render('items/item-search.twig', ['page' => $page, 'items' => $items]);
	}

	include __DIR__ . "/basic-page.php";

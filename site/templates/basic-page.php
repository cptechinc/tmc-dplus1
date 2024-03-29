<?php
	$parents = $page->parents();
?>
<?php include('./_head.php'); ?>
	<div class="jumbotron bg-dark page-banner rounded-0 mb-3">
		<div class="container">
			<h1 class="display-4 text-light"><?= $page->get('pagetitle|headline|title') ; ?></h1>
		</div>
	</div>
	<div class="container">
		<nav aria-label="breadcrumb rounded-0">
			<ol class="breadcrumb">
				<?php foreach ($parents as $parent) : ?>
					<li class="breadcrumb-item">
						<i class="fa fa-list" aria-hidden="true"></i>
						<a href="<?= $parent->url; ?>"><?= $parent->title; ?></a>
					</li>
				<?php endforeach; ?>
				<li class="breadcrumb-item active" aria-current="page"><?= $page->title; ?></li>
			</ol>
		</nav>
	</div>
	<div class='container page pt-3'>
		<?= $page->body; ?>
	</div>
<?php include('./_foot.php'); ?>

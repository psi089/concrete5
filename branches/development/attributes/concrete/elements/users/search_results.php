<? defined('C5_EXECUTE') or die(_("Access Denied.")); ?> 

<div id="ccm-list-wrapper">
<?
	$userList->displaySummary();
	$txt = Loader::helper('text');
	$keywords = $_REQUEST['keywords'];
	$bu = REL_DIR_FILES_TOOLS_REQUIRED . '/users/search_results';
	
	if (count($users) > 0) { ?>	
		<table border="0" cellspacing="0" cellpadding="0" id="ccm-user-list" class="ccm-results-list">
		<tr>
			<th class="<?=$userList->getSearchResultsClass('uName')?>"><a href="<?=$userList->getSortByURL('uName', 'asc', $bu)?>"><?=t('Username')?></a></th>
			<th class="<?=$userList->getSearchResultsClass('uEmail')?>"><a href="<?=$userList->getSortByURL('uEmail', 'asc', $bu)?>"><?=t('Email Address')?></a></th>
			<th class="<?=$userList->getSearchResultsClass('uDateAdded')?>"><a href="<?=$userList->getSortByURL('uDateAdded', 'asc', $bu)?>"><?=t('Date Added')?></a></th>
			<th class="<?=$userList->getSearchResultsClass('uNumLogins')?>"><a href="<?=$userList->getSortByURL('uNumLogins', 'asc', $bu)?>"><?=t('# Logins')?></a></th>
			<? 
			$slist = UserAttributeKey::getColumnHeaderList();
			foreach($slist as $ak) { ?>
				<th class="<?=$userList->getSearchResultsClass($ak)?>"><a href="<?=$userList->getSortByURL($ak, 'asc', $bu)?>"><?=$ak->getAttributeKeyName()?></a></th>
			<? } ?>			
			<th class="ccm-search-add-column-header"><a href="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/users/customize_search_columns" id="ccm-search-add-column"><img src="<?=ASSETS_URL_IMAGES?>/icons/add.png" width="16" height="16" /></a></th>
		</tr>
	<?
		foreach($users as $ui) { 
			if (!isset($striped) || $striped == 'ccm-list-record-alt') {
				$striped = '';
			} else if ($striped == '') { 
				$striped = 'ccm-list-record-alt';
			}

			?>
		
			<tr class="ccm-list-record <?=$striped?>">
			
			<td><a href="<?=View::url('/dashboard/users/search?uID=' . $ui->getUserID())?>"><?=$txt->highlightSearch($ui->getUserName(), $keywords)?></a></td>
			<td><a href="mailto:<?=$ui->getUserEmail()?>"><?=$txt->highlightSearch($ui->getUserEmail(), $keywords)?></a></td>
			<td><?=date(t('M d, Y g:ia'), strtotime($ui->getUserDateAdded()))?></td>
			<td><?=$ui->getNumLogins()?></td>
			<? 
			$slist = UserAttributeKey::getColumnHeaderList();
			foreach($slist as $ak) { ?>
				<td><?
				$vo = $ui->getAttributeValueObject($ak);
				if (is_object($vo)) {
					print $vo->getValue('display');
				}
				?></td>
			<? } ?>		
			<td>&nbsp;</td>
			</tr>
			<?
		}

	?>
	
	</table>
	
	

	<? } else { ?>
		
		<div id="ccm-list-none"><?=t('No users found.')?></div>
		
	
	<? } 
	$userList->displayPaging($bu); ?>
	
</div>
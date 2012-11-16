<?php
	$id = String::uuid();
	$nomanage = isset($nomanage)?$nomanage:false;
?>
<div class="users index admin" id="<?php echo $id; ?>">
	<div class='title'><?php __('Users');?></div>
	<?php
		/* @var $this View */
		$_h = $this->Html->tableHeaders(array(
			$this->Paginator->sort('id'),
			$this->Paginator->sort('nickname'),
			$this->Paginator->sort('email'),
			$this->Paginator->sort('group_id'),
			$this->Paginator->sort('created'),
			$this->Paginator->sort('modified')
		));
		
		$_c = array();
		foreach($users as $u)
		{
			$__t = array(
				array($u['User']['id'], array('class'=>'id')),
				array($u['User']['nickname'], array('class'=>'nickname')),
				$u['User']['email'],
				$u['Group']['name'],
				$u['User']['created'],
				$u['User']['modified']
			);
			if(!$nomanage)
			{
				$__t = array_merge($__t, array(
					$this->Html->link(__('View', true), array('action' => 'view', $u['User']['id'])),
					$this->Html->link(__('Edit', true), array('action' => 'edit', $u['User']['id'])),
					$this->Html->link(__('Delete', true), array('action' => 'delete', $u['User']['id']), null, sprintf(__('Are you sure you want to delete # %s?', true), $u['User']['id'])))
				);
			}
			$_c[] = $__t;
		}
		$_c = $this->Html->tableCells($_c, array('class'=>'even'), array('class'=>'odd'));
		
		echo $this->Html->tag('table', $_h.$_c);
		echo $this->Html->div('paginator',
			$this->Paginator->counter(array(
			'format' => __('Page \%page\% of \%pages\%, showing \%current\% records out of \%count\% total, starting on record \%start\%, ending on \%end\%', true)
		)));
	?>
	<script type="text/javascript">
		var id = '<?php echo $id; ?>';
		$('div#'+id+' table td').click(function()
		{
			var parent = $(this).parent();
			var userId = $(parent).find('td.id').html();
			var nickname = $(parent).find('td.nickname').html();
			$(document).trigger('EVENT_USER_SELECTED', {"id": userId, "nickname": nickname});
		});
	</script>
	<div class="paging">
		<?php echo $this->Paginator->prev('<< ' . __('previous', true), array(), null, array('class'=>'disabled'));?>
	 | 	<?php echo $this->Paginator->numbers();?>
 |
		<?php echo $this->Paginator->next(__('next', true) . ' >>', array(), null, array('class' => 'disabled'));?>
	</div>
	<?php if(!$nomanage) { ?>
	<div class="actions">
		<h3><?php __('Actions'); ?></h3>
		<ul>
			<li><?php echo $this->Html->link(__('New User', true), array('action' => 'add')); ?></li>
			<li><?php echo $this->Html->link(__('List Groups', true), array('controller' => 'groups', 'action' => 'index')); ?> </li>
			<li><?php echo $this->Html->link(__('New Group', true), array('controller' => 'groups', 'action' => 'add')); ?> </li>
		</ul>
	</div>
	<?php } ?>
</div>
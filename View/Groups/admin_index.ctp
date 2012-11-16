<div class="groups index admin">
	<div class='title'><?php __('Groups');?></div>
	<?php
		/* @var $this View */
		$_h = $this->Html->tableHeaders(array(
			$this->Paginator->sort('id'),
			$this->Paginator->sort('name')
		));
		
		$_c = array();
		foreach($groups as $g)
		{
			$_c[] = array(
				$g['Group']['id'],
				$g['Group']['name'],
				$this->Html->link(__('View', true), array('action' => 'view', $g['Group']['id'])),
				$this->Html->link(__('Edit', true), array('action' => 'edit', $g['Group']['id'])),
				$this->Html->link(__('Delete', true), array('action' => 'delete', $g['Group']['id']), null, sprintf(__('Are you sure you want to delete # %s?', true), $g['Group']['id']))
			);
		}
		$_c = $this->Html->tableCells($_c, array('class'=>'even'), array('class'=>'odd'));
		
		echo $this->Html->tag('table', $_h.$_c);
	echo $this->Html->div('paginator',
		$this->Paginator->counter(array(
			'format' => __('Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%', true)
		))
	);
	?>
	<div class="paging">
		<?php echo $this->Paginator->prev('<< ' . __('previous', true), array(), null, array('class'=>'disabled'));?>
	 | 	<?php echo $this->Paginator->numbers();?>
 |
		<?php echo $this->Paginator->next(__('next', true) . ' >>', array(), null, array('class' => 'disabled'));?>
	</div>

	<div class="actions">
		<h3><?php __('Actions'); ?></h3>
		<ul>
			<li><?php echo $this->Html->link(__('New Group', true), array('action' => 'add')); ?></li>
		</ul>
	</div>
</div>
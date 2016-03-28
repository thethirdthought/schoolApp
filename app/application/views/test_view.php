
<table>
	<?php foreach($product_list as $data){?>
		<tr style="background:red"><td><?php echo $data['name']?></td><td><?php echo $data['price']?></td></tr>
	<?php } ?>
</table>
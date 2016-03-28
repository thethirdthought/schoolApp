
<div>
<?php echo anchor('category/managecategory', 'Manage Category', 'title="Manage Category"');?>
<?php echo anchor('category/viewcategory', 'View Category', 'title="View Category"');?>
<?php echo validation_errors(); ?>
<?php echo form_open('category/addcategory'); ?>

<script>
function validate(){
	var val=document.getElementById("category").value;
	if(val=="")	
		document.getElementById("id1").display="block";
	else
		document.forms[0].submit();
}
</script>

<fieldset>
<legend>ADD-CATEGORY</legend>
<form action="category/addcategory" method="post" onSubmit="validate();" id="form1">
	<table>
		<tr><td>Category Name</td><td><input type="text" name="category" id="category"  /></td><span id="id1" style="display:none;">enter category</span></tr>
		<tr><td>Parent Category</td><td><select name="p_id" >
		<option value="0">--Select--</option>
		<?php $result=mysql_query("select * from category");
		while($row=mysql_fetch_assoc($result)){
		?>
		<option value="<?php echo $row['id']?>"><?php echo $row['name']?></option>
		<?php } ?>
		</select></td></tr>
		<tr><td><input type="button" value="Add" onclick="validate();"></td></tr>
	</table>
</form>
</fieldset>

</div>
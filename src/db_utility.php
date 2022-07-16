<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Database Utility</title>
<style type="text/css">
.message{background: #ccc;border: solid 1px #999;padding: 14px 22px;margin-bottom: 10px;}
.message.info{background: #99f;border-color: #09f;}
.message.error{background:#f99;border-color:#f00;}
.container{background: #ccc;border: solid 1px #999;padding: 14px;}
div.view,div.add,div.edit,div.delete,div.sort{display: none;}
.container.is_viewing div.view,.container.is_adding div.add,.container.is_editing div.edit,.container.is_deleting div.delete,.container.is_sorting div.sort{display: block;}
.header{margin-bottom: 15px;font-size: 18px;}
.actions{margin: 15px 0;}
</style>
<script type="text/javascript">
var data={
	database:location.search.split("db=")[1].split("&")[0],
	table:location.search.split("table=")[1].split("&")[0]
};
var sections=["add","edit","delete","sort","view"];
var modes=["is_adding","is_editing","is_deleting","is_sorting","is_viewing"];
var apply=function(){
var a=document.querySelector(".container").className.split("container ")[1];
switch(a){
case "is_adding":
var b=document.querySelectorAll(".add input");
var c="";
for(var i=0;i<b.length;i++){c+=b[i].id+"=%22"+b[i].value+"%22";if(i<b.length-1)c+=",";}
location.href=location.pathname+"?db="+data.database+"&table="+data.table+"&action=add&set="+c;
break;
case "is_editing":
var b=document.querySelectorAll(".edit input");
var c="";
for(var i=1;i<b.length;i++){c+=b[i].id+"=%22"+b[i].value+"%22";if(i<b.length-1)c+=",";}
location.href=location.pathname+"?db="+data.database+"&table="+data.table+"&action=edit&row="+b[0].value+"&set="+c;
break;
case "is_deleting":
var b=document.querySelectorAll(".delete input");
location.href=location.pathname+"?db="+data.database+"&table="+data.table+"&action=delete&row="+b[0].value;
break;
case "is_sorting":
location.href=location.pathname+"?db="+data.database+"&table="+data.table+"&action=sort&column="+document.getElementById("column").value+"&order="+document.getElementById("order").value;
break;
}
};
addEventListener("load",function(){
var btns=document.getElementsByTagName("button");
for(var i=0;i<btns.length;i++)
btns[i].addEventListener("click",function(){
var a;
for(var j=0;j<sections.length;j++)if(this.className===sections[j]){a=j;break;}
else if(this.className==="cancel"){a=4;break;}else if(this.className==="apply"){apply();break;}
for(var k=0;k<modes.length;k++)document.querySelector(".container").classList.remove(modes[k]);
document.querySelector(".container").classList.add(modes[a]);
});
});
</script>
</head>
<body>
<?php
$host="localhost";
$database=$_GET["db"];
$user=$_GET["user"];
$password=$_GET["pw"];
if($database)$connect=new mysqli($host,$user,$password,$database);
else echo "<div class='message error'>Database is not set</div>";
$table=$_GET["table"];
if(!$table) echo "<div class='message error'>Table is not set</div>";
switch($_GET["action"]){
case "add":
$query3=$connect->query("insert into ".$table." set ".$_GET["set"]);
echo "<div class='message info'>Row was successfully added to table</div>";
break;
case "edit":
$query4=$connect->query("show keys from ".$table." where key_name='primary'");
$query3=$connect->query("update ".$table." set ".$_GET["set"]." where ".$query4->fetch_assoc()["Column_name"]."=".$_GET["row"]);
echo "<div class='message info'>Row was successfully edited in table</div>";
break;
case "delete":
$query4=$connect->query("show keys from ".$table." where key_name='primary'");
$query3=$connect->query("delete from ".$table." where ".$query4->fetch_assoc()["Column_name"]."=".$_GET["row"]);
echo "<div class='message info'>Row was successfully deleted from table</div>";
break;
}
?>
<div class="container<?php if($database&&$table) echo " is_viewing";?>">
<div class="view">
<div class="header">
<?php
if($database&&$table)
if($_GET["order"]=="asc") $order="ascending"; else $order="descending";
if($_GET["action"]=="sort") echo "Viewing table &quot;".$table."&quot; sorted by column &quot;".$_GET["column"]."&quot; in ".$order." order in database &quot;".$database."&quot;:";
else echo "Viewing table &quot;".$table."&quot; in database &quot;".$database."&quot;:";
?>
</div>
<table border="1"><tbody>
<?php
$query1=$connect->query("show columns from ".$table);
$columns=array();
$i=0;
echo "<tr>";
while($column=$query1->fetch_assoc()){
$columns[$i]=$column["Field"];
echo "<th>".$columns[$i]."</th>";
$i++;
}
echo "</tr>";
if($_GET["action"]=="sort") $query2=$connect->query("select * from ".$table." order by ".$_GET["column"]." ".$_GET["order"]);
else $query2=$connect->query("select * from ".$table);
while($row=$query2->fetch_assoc()){
echo "<tr>";
for($j=0;$j<count($columns);$j++) echo "<td>".$row[$columns[$j]]."</td>";
echo "</tr>";
}
?>
</tbody></table>
<div class="actions">
<button class="add">Add Row</button>
<button class="edit">Edit Row</button>
<button class="delete">Delete Row</button>
<button class="sort">Sort Table</button>
</div>
</div>
<div class="add">
<div class="header">
<?php
if($database&&$table) echo "Adding row in table &quot;".$table."&quot; in database &quot;".$database."&quot;:";
?>
</div>
<?php
for($k=1;$k<count($columns);$k++) echo "<p>".$columns[$k].":<input id='".$columns[$k]."'></p>";
?>
<div class="actions">
<button class="apply">Apply</button>
<button class="cancel">Cancel</button>
</div>
</div>
<div class="edit">
<div class="header">
<?php
if($database&&$table) echo "Editing row in table &quot;".$table."&quot; in database &quot;".$database."&quot;:";
?>
</div>
<?php
for($k=0;$k<count($columns);$k++) echo "<p>".$columns[$k].":<input id='".$columns[$k]."'></p>";
?>
<div class="actions">
<button class="apply">Apply</button>
<button class="cancel">Cancel</button>
</div>
</div>
<div class="delete">
<div class="header">
<?php
if($database&&$table) echo "Deleting row in table &quot;".$table."&quot; in database &quot;".$database."&quot;:";
?>
</div>
<?php
echo "<p>".$columns[0].":<input id='".$columns[0]."'></p>";
?>
<div class="actions">
<button class="apply">Apply</button>
<button class="cancel">Cancel</button>
</div>
</div>
<div class="sort">
<div class="header">
<?php
if($database&&$table) echo "Sorting table &quot;".$table."&quot; in database &quot;".$database."&quot;:";
?>
</div>
<p>Column:<select id="column">
<?php
for($k=0;$k<count($columns);$k++) echo "<option value='".$columns[$k]."'>".$columns[$k]."</option>";
?>
</select></p>
<p>Order by:<select id="order">
<option value="asc">Ascending</option>
<option value="desc">Descending</option>
</select></p>
<div class="actions">
<button class="apply">Apply</button>
<button class="cancel">Cancel</button>
</div>
</div>
</div>
</body>
</html>
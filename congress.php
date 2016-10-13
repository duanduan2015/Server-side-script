<html>
<?php
    $db = $_POST["database"];
    $cb = $_POST["chamber"];
    $kwn = $_POST["keywordName"];
    $kw = $_POST["keyword"];
?>
<head>
<script>
    function keywordSelect() {
        var database = document.getElementById("database");
        var checked = database.selectedIndex;
        var keyword = document.getElementById("keywordName");
        if (checked == 0) {
            keyword.innerHTML="Keyword*";
        } else if (checked == 1) {
            keyword.innerHTML="State/Representative*";
        } else if (checked == 2) {
            keyword.innerHTML="Committee ID*";
        } else if (checked == 3) {
            keyword.innerHTML="Bill ID*";
        } else if (checked == 4) {
            keyword.innerHTML="Amendment ID*";
        }
    }
    function clearForm() {
        document.getElementById("database").selectedIndex = 0;
        document.getElementById("senate").checked = false;
        document.getElementById("house").checked = false;
        document.getElementById("keywordName").innerHTML = "Keyword*";
        document.getElementById("keyword").value = "";
    }
    function checkForm() {
        var db = document.getElementById("database");
        var index = db.selectedIndex;
        var missing = [];
        if (index == 0) {
            missing.push("Congress Database");
        } 
        var senate = document.getElementById("senate");
        var house = document.getElementById("house");
        if (senate.checked == false && house.checked == false) {
            missing.push("Chamber");
        }
        var keyword = document.getElementById("keyword").value;
        if (keyword.length == 0) {
            if (index == 0) {
                missing.push("Keyword");
            } else if (index == 1) {
                missing.push("State/Representative");
            } else if (index == 2) {
                missing.push("Committee ID");
            } else if (index == 3) {
                missing.push("Bill ID");
            } else if (index == 4) {
                missing.push("Amendment ID");
            }
        }
        if (missing.length != 0) {
            msg = "Please enter the following missing information: ";
            var len = missing.length;
            for (var i = 0; i < len - 1; i++) {
                msg = msg + missing[i] + ", ";
            }
            msg = msg + missing[len - 1];
            alert(msg);
            return false;
        }
    }
</script>
<style>
fieldset { 
    display: block;
    margin: auto;
    padding-top: 0.35em;
    padding-bottom: 0.625em;
    padding-left: 0.75em;
    padding-right: 0.75em;
    border: 2px groove;
    width: 300px;
}
</style>
</head>
<body>
<h1 align="center">Congress Information Search</h1>
<form align="center" action="" method="POST" onsubmit="return checkForm()">
<fieldset>
<table>
<tr>
<td align="center">Congress Database</td>
<td align="center">
<select name="database" id="database" onclick="keywordSelect()">
<option value="default" <?php if($db != null && $db == "default") echo "selected";?>>Select your option</option>
<option value="legislators" <?php if($db != null && $db == "legislators") echo "selected";?>>Legislators</option>
<option value="committees" <?php if($db != null && $db == "committees") echo "selected";?>>Committees</option>
<option value="bills" <?php if($db != null && $db == "bills") echo "selected";?>>Bills</option>
<option value="amendments" <?php if($db != null && $db == "amendments") echo "selected";?>>Amendments</option>
</select></td></tr>
<tr>
<td align="center">Chamber</td>
<td align="center">
<input id="senate" type="radio" name="chamber" value="Senate" <?php if($cb != null && $cb == "Senate") echo "checked";?>>Senate</input>
<input id="house" type="radio" name="chamber" value="House" <?php if($cb != null && $cb == "House") echo "checked";?>>House</input>
</td></tr>
<tr>
<td id="keywordName" align="center" name="keywordName">Keyword*</td>
<td align="center"><input id="keyword" type="text" name="keyword" value="<?php if($kw != null) echo $kw;?>"></td></tr>
<tr>
<td></td>
<td>
<input type="submit" name="submit" value="Search">
<button type="button" name="clear" onclick="clearForm()")>Clear</button>
</td>
</tr>
<tr>
<td align="center" colspan="2">
<a href="http://sunlightfoundation.com" text-align="center">Powered by Sunlight Foundation</a>
</td>
</tr>
</table>
</fieldset>
</form>
<?php 
    if (isset($_POST["submit"])) { 
        $db = $_POST["database"];
        $cb = $_POST["chamber"];
        $kwn = $_POST["keywordName"];
        echo '<script type="text/javascript">keywordSelect()</script>';
        $kw = $_POST["keyword"];
        $apikey = "apikey=c8e8d23822424300b4043bb3ad752f57";
        $chamber = "chamber=" . $_POST["chamber"];
        $keyword = $_POST["keywordName"] . "=" . $_POST["keyword"];
        $database = $_POST["database"];
        $url = "http://congress.api.sunlightfoundation.com/";
        $opts = array(
            'https'=>array(
                'method'=>"GET",
                'header'=>"Accept-language: en\r\n"
            )
        );
        $context = stream_context_create($opts);
        $filePath = $url . $database . "?" . $chamber . "&" . $keyword . "&" . $apikey;
        $file = file_get_contents($filePath, false, $context);
        echo "<p> $filePath </p>";
        $_POST["submit"] = null;
    }
?>
</body>
<html>

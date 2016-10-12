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
        var keyword = document.getElementById("keyword").textContent;
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
<h1 align="center">Congress Information Search</h1>
<form align="center" action="" method="POST" onsubmit="return checkForm()">
<fieldset>
<table>
<tr>
<td align="center">Congress Database</td>
<td align="center">
<select id="database" onclick="keywordSelect()">
<option value="default">Select your option</option>
<option value="legislators">Legislators</option>
<option value="committees">Committees</option>
<option value="bills">Bills</option>
<option value="amendments">Amendments</option>
</select></td></tr>
<tr>
<td align="center">Chamber</td>
<td align="center">
<input id="senate" type="radio" name="chamber" value="Senate">Senate</input>
<input id="house" type="radio" name="chamber" value="House">House</input>
</td></tr>
<tr>
<td id="keywordName" align="center">Keyword*</td>
<td align="center"><input id="keyword" type="text" name="keyword" value=""></td></tr>
<tr>
<td></td>
<td>
<input type="submit" name="search" value="search">
<button type="button" name="clear" onclick="clearForm()")>Clear</button>
</td>
</tr>
</table>
</fieldset>
</form>
<?php
//Create a stream
    $opts = array(
        'https'=>array(
            'method'=>"GET",
            'header'=>"Accept-language: en\r\n"
        )
    );

    $context = stream_context_create($opts);

// Open the file using the HTTP headers set above
    $file = file_get_contents('http://congress.api.sunlightfoundation.com/legislators?chamber=house&state=WA&apikey=c8e8d23822424300b4043bb3ad752f57', false, $context);
    //echo $file;
?>

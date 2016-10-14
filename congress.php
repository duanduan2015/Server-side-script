<html>
<?php
    $db = $_POST["database"];
    $cb = $_POST["chamber"];
    $kwn = $_POST["keywordName"];
    $kw = $_POST["keyword"];
?>
<head>
<script>
    function displayDetails(index) {
        var display = document.getElementById("display");
        var id = "details".concat(index.toString());
        var details = document.getElementById(id);
        display.innerHTML = details.innerHTML;
    }
    function displayBills(index) {
        var display = document.getElementById("display");
        var id = "bills".concat(index.toString());
        var details = document.getElementById(id);
        display.innerHTML = details.innerHTML;
    }
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
        var display = document.getElementById("display");
        while (display.firstChild) {
                display.removeChild(display.firstChild);
        }
        document.getElementById("database").selectedIndex = 0;
        document.getElementById("senate").checked = false;
        document.getElementById("house").checked = false;
        document.getElementById("keywordName").innerHTML = "Keyword*";
        document.getElementById("keyword").value = "";
        //clear dynamic generated divs
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
.border {
    border: 2px solid black;
    border-collapse: collapse;
    text-align: center;
    margin-top:30px;
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
<a href="http://sunlightfoundation.com" text-align="center" target="_blank">Powered by Sunlight Foundation</a>
</td>
</tr>
</table>
</fieldset>
</form>
<?php 
    if (isset($_POST["submit"])) { 
        $db = $_POST["database"];
        $cb = $_POST["chamber"];
        echo '<script type="text/javascript">keywordSelect()</script>';
        $apikey = "apikey=c8e8d23822424300b4043bb3ad752f57";
        $database = strtolower($_POST["database"]);
        $chamber = "chamber=" . strtolower($_POST["chamber"]);
        $keywordName = null;
        $keyword = $_POST["keyword"];
        if ($database == "legislators") {
            $keywordName = "state";
            $states = getStatesTable();
            $keyword = $states[$keyword];
            if ($keyword == null) {
                $keyword = $_POST["keyword"];
                $keywordName = "query";
            }
        } else if ($database == "committees") {
            $keywordName = "committee_id";
        } else if ($database == "bills") {
            $keywordName = "bill_id";
        } else if ($database == "amendments") {
            $keywordName = "amendment_id";
        }
        $keyword = $keywordName . "=" . $keyword;
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
        $decode = json_decode($file, true);
        if ($decode["count"] == 0) {
            $message = "The API returned zero results for the request.";
            echo '<div id="display" align="center"><h2>' . $message . '</h2></div>';
        } else {
            $results = $decode["results"];
            $table = null;
            if ($database == "legislators") {
                $table = getLesgislatorsTable($results, $keyword, $decode["count"], $contex); 
            } else if ($database == "committees") {
                $table = getCommitteesTable($results, $keyword, $decode["count"], $contex);
            } else if ($database == "bills") {
                $table = getBillsTable($results, $keyword, $decode["count"], $contex);
            } else if ($database == "amendments") {
                $table = getAmendmentsTable($results, $keyword, $decode["count"], $contex);
            }
            echo $table;
        }
        $_POST["submit"] = null;
    }
    function getAmendmentsTable($results, $keyword, $length, $contex) {
        $chamber = "chamber=" . strtolower($_POST["chamber"]);
        $apikey = "apikey=c8e8d23822424300b4043bb3ad752f57";
        $url = "http://congress.api.sunlightfoundation.com/";
        $database = "amendments";
        $table = '<div id="display"><table class="border" align="center" width="70%"><tr class="border"><th>Amendment ID</th><th class="border">Amendment Type</th><th class="border">Chamber</th><th class="border">Introduce on</th></tr>';
        for ($i = 0; $i < $length; $i++) {
            if (count($results[$i]) == 0) {
                break;
            }
            $newRow = '<tr class="border"><td class="border">'.$results[$i]["amendment_id"] . '</td><td class="border">' . $results[$i]["amendment_type"] . '</td><td class="border">' . $results[$i]["chamber"] . '</td><td class="border">' . $results[$i]["introduced_on"] . '</td></tr>';
            $table = $table. $newRow;
        }
        $table = $table . '</table></div>';
        return $table;
    }
    function getBillsTable($results, $keyword, $length, $contex) {
        $chamber = "chamber=" . strtolower($_POST["chamber"]);
        $apikey = "apikey=c8e8d23822424300b4043bb3ad752f57";
        $url = "http://congress.api.sunlightfoundation.com/";
        $database = "bills";
        $table = '<div id="display"><table class="border" align="center" width="70%"><tr class="border"><th>Bill ID</th><th class="border">Short Title</th><th class="border">Chamber</th><th class="border">Details</th></tr>';
        for ($i = 0; $i < $length; $i++) {
            if (count($results[$i]) == 0) {
                break;
            }
            $detailsLink = '<a href="javascript:displayBills(' . $i . ');">View Details</a>';
            $GLOBALS[$i] = getBillsDetails($results[$i]);
            $detailsTable = '<div id="bills' . $i . '" style="display:none;">' . $GLOBALS[$i] . '</div>';
            echo $detailsTable;
            $newRow = '<tr class="border"><td class="border">'.$results[$i]["bill_id"] . '</td><td class="border">' . $results[$i]["short_title"] . '</td><td class="border">' . $results[$i]["chamber"] . '</td><td class="border">' . $detailsLink . '</td></tr>';
            $table = $table. $newRow;
        }
        $table = $table . '</table></div>';
        return $table;
    }
    function getBillsDetails($results) {
        $head = '<table class="border" width="70%" align="center">';
        $id = '<tr><td align="left" style="padding-left:150px;padding-top:40px;">Bill ID</td><td align="left" style="padding-top:40px;">' . $results["bill_id"] . '</td></tr>';
        $title = '<tr><td align="left" style="padding-left:150px;">Bill Title</td><td align="left" >' . $results["short_title"] . '</td></tr>';
        $sponsor = '<tr><td align="left" style="padding-left:150px;">Sponsor</td><td align="left" >' . $results["sponsor"]["title"]. ' ' . $results["sponsor"]["first_name"] . ' ' . $results["sponsor"]["last_name"] . '</td></tr>';
        $intro = '<tr><td align="left" style="padding-left:150px;">Introduced On</td><td align="left" >' . $results["introduced_on"] . '</td></tr>';
        $date = '<tr><td align="left" style="padding-left:150px;">Last action with date</td><td align="left" >' . $results["last_version"]["version_name"]. ', ' . $results["last_action_at"] . '</td></tr>';
        $url = '<tr><td align="left" style="padding-left:150px;padding-bottom:40px;">Bill URL</td><td align="left" style="padding-bottom:40px;"><a target="_blank" href="' . $results["last_version"]["urls"]["pdf"] . '">' . $results["short_title"] . '</a></td><tr>';
        $table = $head . $id . $title . $sponsor . $intro . $date . $url . '</table>';
        return $table;
    }
    function getCommitteesTable($results, $keyword, $length, $contex) {
        $chamber = "chamber=" . strtolower($_POST["chamber"]);
        $apikey = "apikey=c8e8d23822424300b4043bb3ad752f57";
        $url = "http://congress.api.sunlightfoundation.com/";
        $database = "committees";
        $table = '<div id="display"><table class="border" align="center" width="70%"><tr class="border"><th>Committee ID</th><th class="border">Committee Name</th><th class="border">Chamber</th></tr>';
        for ($i = 0; $i < $length; $i++) {
            if (count($results[$i]) == 0) {
                break;
            }
            $newRow = '<tr class="border"><td class="border">'.$results[$i]["committee_id"] . '</td><td class="border">' . $results[$i]["name"] . '</td><td class="border">' . $results[$i]["chamber"] . '</td></tr>';
            $table = $table. $newRow;
        }
        $table = $table . '</table></div>';
        return $table;
    }
    function getLesgislatorsTable($results, $keyword, $length, $contex) {
        $chamber = "chamber=" . strtolower($_POST["chamber"]);
        $apikey = "apikey=c8e8d23822424300b4043bb3ad752f57";
        $url = "http://congress.api.sunlightfoundation.com/";
        $database = "legislators";
        $table = '<div id="display"><table class="border" align="center" width="70%"><tr class="border"><th>Name</th><th class="border">State</th><th class="border">Chamber</th><th class="border">Details</th></tr>';
        for ($i = 0; $i < $length; $i++) {
            if (count($results[$i]) == 0) {
                break;
            }
            $id = "bioguide_id=" . $results[$i]["bioguide_id"];
            $link = $url . $database . "?" . $chamber . "&" . $keyword . "&" . $id . "&" . $apikey;
            $details = file_get_contents($link, false, $context);
            $detailsDecode = json_decode($details, true);
            $GLOBALS[$i] = getDetailsTable($detailsDecode);
            $detailsTable = '<div id="details' . $i . '" style="display:none;">' . $GLOBALS[$i] . '</div>';
            echo $detailsTable;
            $detailsLink = '<a href="javascript:displayDetails(' . $i . ');">View Details</a>';
            $newRow = '<tr class="border"><td  class="border">'.$results[$i]["first_name"] . ' ' . $results[$i]["last_name"] . '</td><td class="border">' . $results[$i]["state_name"] . '</td><td  class="border">' . $results[$i]["chamber"] . '</td><td class="border">' . $detailsLink . '</td></tr>';
            $table = $table. $newRow;
        }
        $table = $table . '</table></div>';
        return $table;
    }
    function getDetailsTable($decode) {
        $results = $decode["results"][0];
        $head = '<table class="border" width="70%" align="center">';
        $img = '<tr><td  align="center"colspan="2"><img style="padding-top:20px; padding-bottom:20px;" align="center" src="https://theunitedstates.io/images/congress/225x275/' . $results["bioguide_id"]. '.jpg"></td></tr>';
        $name = '<tr><td align="left" style="padding-left:250px;">Full Name</td><td align="left">' . $results["first_name"] . ' ' . $results["last_name"] . '</td></tr>';
        $term = '<tr><td align="left" style="padding-left:250px;">Term Ends On</td><td align="left" >' . $results["term_end"] . '</td></tr>';
        $website = '<tr><td align="left" style="padding-left:250px;">Website</td><td align="left" ><a target="_blank" href="' . $results["website"] . '">' . $results["website"] . '</a></td></tr>';
        $office = '<tr><td align="left" style="padding-left:250px;">Office</td><td align="left" >' . $results["office"]. '</td></tr>';
        $facebook = '<tr><td align="left" style="padding-left:250px;">Facebook</td><td align="left" ><a target="_blank" href="https://www.facebook.com/' . $results["facebook_id"] . '">' . $results["first_name"] . ' ' . $results["last_name"]. '</a></td><tr>';
        $twitter = '<tr><td align="left" style="padding-bottom:20px;padding-left:250px;">Twitter</td><td style="padding-bottom:20px;" align="left" ><a target="_blank" href="https://twitter.com/' . $results["twitter_id"] . '">' . $results["first_name"] . ' ' . $results["last_name"]. '</a></td><tr>';
        $table = $head . $img . $name . $term . $website . $office . $facebook . $twitter . '</table>';
        return $table;
    }
    function getStatesTable() {
        $states = array(
            "Alabama"=>"AL",
            "Alaska"=>"AK",
            "Arizona"=>"AZ",
            "Arkansas"=>"AR",
            "California"=>"CA",
            "Colorado"=>"CO",
            "Connecticut"=>"CT",
            "Delaware"=>"DE",
            "Florida"=>"FL",
            "Georgia"=>"GA",
            "Hawaii"=>"HI",
            "Idaho"=>"ID",
            "Illinois"=>"IL",
            "Indiana"=>"IN",
            "Iowa"=>"IA",
            "Kansas"=>"KS",
            "Kentucky"=>"KY",
            "Louisiana"=>"LA",
            "Maine"=>"ME",
            "Maryland"=>"MD",
            "Massachusetts"=>"MA",
            "Michigan"=>"MI",
            "Minnesota"=>"MN",
            "Mississippi"=>"MS",
            "Missouri"=>"MO",
            "Montana"=>"MT",
            "Nebraska"=>"NE",
            "Nevada"=>"NV",
            "New Hampshire"=>"NH",
            "New Jersey"=>"NJ",
            "New Mexico"=>"NM",
            "New York"=>"NY",
            "North Carolina"=>"NC",
            "North Dakota"=>"ND",
            "Ohio"=>"OH",
            "Oklahoma"=>"OK",
            "Oregon"=>"OR",
            "Pennsylvania"=>"PA",
            "Rhode Island"=>"RI",
            "South Carolina"=>"SC",
            "South Dakota"=>"SD",
            "Tennessee"=>"TN",
            "Texas"=>"TX",
            "Utah"=>"UT",
            "Vermont"=>"VT",
            "Virginia"=>"VA",
            "Washington"=>"WA",
            "West Virginia"=>"WV",
            "Wisconsin"=>"WI",
            "Wyoming"=>"WY",
        );
        return $states;
    }
?>
</body>
<html>

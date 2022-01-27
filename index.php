<?php
$curl = curl_init();
curl_setopt_array($curl, array(
  CURLOPT_URL => 'http://10.20.0.235/api/group/search_group',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'GET',
  CURLOPT_POSTFIELDS =>'{
    "all":"yes"
}',
  CURLOPT_HTTPHEADER => array(
    'Content-Type: application/json'
  ),
));
$response = curl_exec($curl);
curl_close($curl);
$result = json_decode($response);

$result_tmp = array();
foreach ($result->dataList as $key=>$row) {
    $result_tmp[] = $row;
}

function findByID($parent)
{
    global $result_tmp;
    $ans = array();
    foreach ($result_tmp as $key11 => $data11) {
        if ($data11->parent == $parent) {
            $ans[] = $data11;
            unset($result_tmp[$key11]);
        }
    }
    return $ans;
}
$data = "";
function getChildNode($parent)
{ 
    global $data;
    $result = findByID($parent);
    if (count($result)) {
        $data .= '"children": [';
    }
    foreach ($result as $key11 => $row) {
        $data .= '{"text": "' . $row->name . '","state": {"opened": true}';
        if ($row->gid != '') {
            $data .= ',';
            getChildNode($row->gid);
        }
        $data .= '},';
    }
    if (count($result)) {
        $data .= ']';
    }
}

getChildNode(0);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แผนผังองค์กร</title>
    <link rel="stylesheet" href="./src/style.min.css">
    <link rel="stylesheet" href="./src/bootstrap-glyphicons.css">
    <link rel="icon" type="image/x-icon" href="./60-thMed-logo.png">
    <style type="text/css">
        body {
            width: 80%;
            margin: 0 auto;
        }

        .li-style {
            color: darkred;
        }

        .a-style:hover {
            text-decoration: underline !important;
        }
    </style>
</head>

<body>
    <br />
    <b>แผนผังองค์กร</b> Search : <input type="text" id="search-input" class="search-input" /> <input type="button" value="ค้นหา" id="bt_search">
    <div id="jstree_demo"></div>
</body>
<script src="./src/jquery.min.js"></script>
<script src="./src/jstree.min.js"></script>
<script>
    $(function() {
        // Create an jstree instance
        $('#jstree_demo').jstree({ // config object start
            "core": { // core config object
                "mulitple": false, // disallow multiple selection  
                "animation": 100, // 200ms is default value
                "check_callback": true, // this make contextmenu plugin to work
                "themes": {
                    "variant": "medium",
                    "dots": false
                },
                "data": [
                    // The required JSON format for populating a tree
                    {
                        "text": "โรงพยาบาลมหาราชนครเชียงใหม่",
                        "state": {
                            "opened": true
                        },
                        "type": "demo",
                        <?php echo $data ?>
                    } // root node end, end of JSON
                ] // data core options end

            }, // core end
            // Types plugin
            "types": {
                "default": {
                    "icon": "glyphicon glyphicon-flash"
                },
                "demo": {
                    "icon": "glyphicon glyphicon-tasks"
                }
            },
            // config object for Checkbox plugin (declared below at plugins options)
            "checkbox": {
                "keep_selected_style": false, // default: false
                "three_state": true, // default: true
                "whole_node": true // default: true
            },
            "conditionalselect": function(node, event) {
                return false;
            },
            // injecting plugins
            "plugins": [
                "search",
                "checkbox",
                "contextmenu",
                // "dnd",
                // "massload",
                // "search",
                // "sort",
                // "state",
                "types",
                // Unique plugin has no options, it just prevents renaming and moving nodes
                // to a parent, which already contains a node with the same name.
                "unique",
                // "wholerow",
                // "conditionalselect",
                "changed"
            ],
            "search": {
                "case_sensitive": false,
                "show_only_matches": true
            }
        }); // config object end
        // Listen for events - example
        $('#jstree_demo_div').on("changed.jstree", function(e, data) {
            // changed.jstree is a event
            // console.log(data.selected);
            console.log('ds: ' + data.changed.deselected);
        });
    });

    $("#bt_search").click(function() {
        if ($("#search-input").val()) {
            $('#jstree_demo').jstree('search', $("#search-input").val());
        }
    });

    $(document).ready(function() {
        $(".search-input").keyup(function() {
            if ($("#search-input").val() == '') {
                var searchString = $(this).val();
                $('#jstree_demo').jstree('search', searchString);
            }
        });
    });
</script>

</html>
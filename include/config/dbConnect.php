<?php
// Connect with the database
$db = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Display error if failed to connect
if ($db->connect_errno) {
    printf("Connect failed: %s\n", $db->connect_error);
    exit();
}

// This is to display data from table
function get_table_data($tbl, $where = '', $order = '', $limit = '')
{
    global $db;

    $sql  = "select * from $tbl";
    if(!empty($where)) {
        $sql  .= " where $where";
    }

    if(!empty($order)) {
        $sql  .= " order by $order";
    }

    if(!empty($limit)) {
        $sql  .= " limit 0, $limit";
    }

    $res = mysqli_query($db, $sql) or die(mysqli_error($db));

    while($row = mysqli_fetch_object($res)) {
        $array[] = $row;
    }

    if(!empty($array)) {
        return $array;
    }
}

// insert table data
function insert_table_data($array3, $table_name)
{
    global $db;

    $query = "insert into " . $table_name . " set ";
    $count = count($array3);
    $count1 = 1;

    foreach ($array3 as $key => $value) {
        if($count == $count1) {
            $query.= $key.'='."'$value'";
        } else {
            $query.= $key.'='."'$value'".',';
        }
        $count1++;
    }

    if(mysqli_query($db, $query)) {
        return true;
    } else {
        return false;
    }
}

// get last insert id
function last_id()
{
    global $db;
    return mysqli_insert_id($db);
}

// update table data
function update_table_data($tbl, $columns, $where )
{
    global $db;
    $sql  = "update $tbl SET $columns where $where";
    $res  = mysqli_query($db, $sql) or die(mysqli_error($db));
    return 1;
}
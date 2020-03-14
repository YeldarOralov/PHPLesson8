<?php

function db_connect($database, $port = 3306) {

    $connection = mysqli_connect('localhost',
        'root',
        '',
        $database, $port);

    if ( mysqli_connect_errno() )
        die( mysqli_connect_error() );

    return $connection;
}

function db_checkOrDie($connection) {
    if (!$connection || mysqli_errno($connection))
        die( mysqli_error($connection) );
}

function db_select($table, $cols='*', array $where =[]) {

    if(is_array($cols)){
        $cols = implode(',', $cols);
    }

    $connection = db_getConnectionFromTable($table);
    $table = db_getTable($table);
    $query = "Select $cols from `$table`";

    if(count($where)>0){
        $query .=" WHERE";
        foreach ($where as $col=>$value) {
            $query .= " $col=" . db_escapeData($value, $connection);
        }
    }
    $result = mysqli_query($connection, $query);
    db_checkOrDie($connection);

    $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);
    mysqli_close($connection);
    return $rows;
}

function db_getConnectionFromTable($table){
    $table = explode('.', $table);
    return db_connect($table[0] ?? '');
}

function db_getTable($table){
    return explode('.', $table)[1] ?? '';
}

function db_escapeData($data, $connection){
    if(!is_bool($data) && !is_null($data)){
        $data = mysqli_real_escape_string($connection, $data);
    }

    if(is_numeric($data)){
        return $data;
    }
    else if(is_string($data)){
        return "'$data'";
    }
    else if(is_bool($data)){
        return $data ? 1 : 0;
    }
    else if(is_null($data)){
        return 'NULL';
    }

    die('Incorrect $data -> ' . $data);
}

function db_escapeWhere($where, $connection){
        if(count($where)>0){
            $query =" WHERE";
            foreach ($where as $col=>$value) {
                $query .= " $col=" . db_escapeData($value, $connection);
            }
            return $query;
        }
        return '';
}
function db_escapeSet($set, $connection){
    if(count($set)>0){
        $query =" SET";
        foreach ($set as $col=>$value) {
            $query .= " $col=" . db_escapeData($value, $connection);
        }
        return $query;
    }
    return '';
}

function db_insert($table, array $data){
    $connection = db_getConnectionFromTable($table);
    $table = db_getTable($table);

    $cols = array_keys($data);
    $cols = implode(',', $cols);

    $values = array_map(function ($value) use ($connection){
        return db_escapeData($value, $connection);
    }, $data);
    $values = implode(',', $values);
    $query = "INSERT INTO $table ($cols) VALUE ($values)";

    $result = mysqli_query($connection, $query);
    if($result == false){
        db_checkOrDie($connection);
    }
    mysqli_close($connection);
    return $result;


}

function db_update($table, array $colValues, array $where){
    $connection = db_getConnectionFromTable($table);
    $table = db_getTable($table);

    $query = "UPDATE $table";
    $query .= db_escapeSet($colValues, $connection);
    $query .= db_escapeWhere($where, $connection);

    $result = mysqli_query($connection, $query);
    if($result == false){
        db_checkOrDie($connection);
    }
    return $result;
}

function db_delete($table, array $where){
    $connection = db_getConnectionFromTable($table);
    $table = db_getTable($table);

    $query = "DELETE FROM $table";
    $query .= db_escapeWhere($where, $connection);

    $result = mysqli_query($connection, $query);
    if($result == false){
        db_checkOrDie($connection);
    }
    return $result;
}

function db_updateCount($table){
    $connection = db_getConnectionFromTable($table);
    $table = db_getTable($table);

    $query = "select counter FROM $table";
    $counter = mysqli_query($connection, $query);
    $counter = mysqli_fetch_all($counter, MYSQLI_ASSOC);
    $counter = $counter[0]["counter"];
    $counter++;
    $query = "UPDATE $table set counter = $counter";
    mysqli_query($connection, $query);
    db_checkOrDie($connection);
    return $counter;
}
<?php


class QueryFormer
{
    private static function buildWhere($q, &$query, &$args)
    {
        if (isset($q->where)) {
            $query .= ' WHERE ';
            $arr = array_map('trim', preg_split(
                        '/((?:and|or)?\s?[a-zA-Z.]+\s?(?:[><]=?|!?=))/',
                        $q->where,
                        -1,
                        PREG_SPLIT_DELIM_CAPTURE)
            );
            foreach ($arr as $key => $value) {
                if ($key % 2) {
                    $query .= " ? ";
                    $args[] = $value;
                } else {
                    $query .= $value;
                }
            }
        }
    }

    private static function buildSelect($q)
    {
        $args = [];
        $query = "SELECT ";
        if (isset($q->params))
            $query.= "`" .  implode("`,`",$q->params) . "`";
        else 
            $query .= '*';
        $query .= " FROM `{$q->table}`";
        if (isset($q->join)) {
            foreach ($q->join as $table =>$cond) {
                $query.= " join `$table` ON $cond";
            }
        }
        QueryFormer::buildWhere($q, $query, $args);
        if (isset($q->orderBy)) {
            $query .= " ORDER BY " . $q->orderBy;
        }
        if (isset($q->limit)) {
            $query .= " LIMIT $q->limit";
        }
        return [$query, $args];
    }

    private static function buildInsert($q)
    {
        if (!isset($q->params[0]))
            throw new Exception("Error: You must pass parameters!");
        $values =implode(",", array_fill(0, count($q->params[0]), "?"));
        $query = "INSERT INTO `{$q->table}` (`" . 
            implode("`,`", array_keys($q->params[0])) .
            "`) VALUES ($values)";
        $args = $q->params[0];
        return [$query, $args];
    }

    private static function buildUpdate($q)
    {
        if (!isset($q->params[0]))
            throw new Exception("Error: You must pass parameters!");
        $values =[];
        $args = $q->params[0];
        foreach ($q->params[0] as $key => $value)
            $values[] = "`$key` = ?";
        $query = "UPDATE `{$q->table}` SET " . implode(",", $values);
        QueryFormer::buildWhere($q, $query, $args);
        return [$query, $args];
    }

    private static function buildDelete($q)
    {
        $args = [];
        $query = "DELETE FROM `{$q->table}`";
        QueryFormer::buildWhere($q, $query, $args);
        return [$query, $args];
    }

    public static function buildQuery($q)
    {
        $possible_actions = [$q->select, $q->insert, $q->update, $q->delete];
        if (array_sum($possible_actions) != 1)
            throw new Exception("Error: Invalid syntax!");

        if ($possible_actions[0]) {// select
            return QueryFormer::buildSelect($q);
        }
        if ($possible_actions[1]) {// insert
            return QueryFormer::buildInsert($q);
        }
        if ($possible_actions[2]) {// update
            return QueryFormer::buildUpdate($q);
        } 
        return QueryFormer::buildDelete($q);
    }
}
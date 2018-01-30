<?php
require_once(LIB_PATH.DS."database.php");

class Comment {

    protected static $table_name = "comments";
    protected static $db_fields = array('id', 'photograph_id', 'created', 'author', 'body');    

    public $id;
    public $photograph_id;
    public $created;
    public $author;
    public $body;
    
    public static function make($photo_id, $author="Anonymous", $body="") {
        if (!empty($photo_id) && !empty($author) && !empty($body)) {
            $comment = new Comment();
            $comment->photograph_id = (int)$photo_id;
            $comment->created       = strftime("%Y-%m-%d %H:%M:%S", time());
            $comment->author        = $author;
            $comment->body          = $body;
            return $comment;
        } else {
            return false;
        }
    }

    public static function find_comments_on($photo_id=0) {
        global $database;
        $sql = "SELECT * FROM " . self::$table_name;
        $sql .= " WHERE photograph_id=" . $database->escape_value($photo_id);
        $sql .= " ORDER BY created ASC";
        return self::find_by_sql($sql);
    }

    // common database methods
    public static function find_all() {
        global $database;
        $sql = "SELECT * FROM " . self::$table_name;
        return self::find_by_sql($sql);
    }

    public static function find_by_id($id=0) {
        global $database;
        $sql = "SELECT * FROM " . self::$table_name . " WHERE id={$id} LIMIT 1";
        $result_array = self::find_by_sql($sql);
        // checks to see if the query returned a result or not
        return !empty($result_array) ? array_shift($result_array) : false;
    }

    public static function find_by_sql($sql="") {
        global $database;
        $result_set = $database->query($sql);
        // create a variable to hold an array of objects
        $object_array = array();
        // get each row from the table results obtained from the query
        while($row = $database->fetch_array($result_set)) {
            // convert the row into an object
            $object_array[] = self::instantiate($row);
        }
        return $object_array;
    }

    public static function count_all() {
        global $database;
        $sql = "SELECT COUNT(*) FROM " . self::$table_name;
        $result_set = $database->query($sql);
        $row = $database->fetch($result_set);
        return array_shift($row);
    }

    private static function instantiate($record) {
        // could check that $record exists and is an array
        // Simple, long-form approach:

        $object = new self();
  
        // $user->id         = $record['id'];
        // $user->username   = $record['username'];
        // $user->password   = $record['password'];
        // $user->first_name = $record['first_name'];
        // $user->last_name  = $record['last_name'];
        
        // More dynamic, short-form approach
        foreach($record as $attribute=>$value) {
            if ($object->has_attribute($attribute)) {
                $object->$attribute = $value;
            }
        }
        return $object;
    }

    private function has_attribute($attribute) {
        // get_object_vars returns an associative array with all attributes
        // (incl. private ones!) as the keys and their current values as the value

        $object_vars = $this->attributes();
        // We don't care about the value, we just want to know if the key exits
        // Will return true or false
        return array_key_exists($attribute, $object_vars);
    }

    protected function attributes() {
        // return an array of attribute names and their values
        $attributes = array();
        foreach(self::$db_fields as $field) {
            if(property_exists($this, $field)) {
                $attributes[$field] = $this->$field;
            }
        }
        return $attributes;
    }

    protected function sanitized_attributes() {
        global $database;
        $clean_attributes = array();
        // sanitize the values before submitting
        // Note: does not alter the actual value of each attribute

        foreach($this->attributes() as $key => $value) {
            $clean_attributes[$key] = $database->escape_value($value);
        }
        return $clean_attributes;
    }

    public function save() {
        // A new record won't have an id yet
        return (isset($this->id)) ? $this->update() : $this->create();
    }

    public function create() {
        global $database;
        // Don't forget your SQL syntax and good habits:
        // = INSERT INTO table (key, key) VALUES ('value', 'value')
        // - single-quotes around all values
        // - escape all values to prevent SQL injection
        $attributes = $this->sanitized_attributes();

        $sql  = "INSERT INTO " . self::$table_name . " (";
        $sql .= join(", ", array_keys($attributes));
        $sql .= ") VALUES ('";
        $sql .= join("', '", array_values($attributes));
        $sql .= "')";
        if ($database->query($sql)) {
            $this->id = $database->insert_id();
            return true;
        } else {
            return false;
        }
    }

    public function update() {
        global $database;
        // Don't forget your SQL syntax and good habits
        // - UPDATE table SET key='value', key = 'value' WHERE CONDITION
        // - single-quotes around all values
        // - escape all values to prevent SQL injection
        $attributes = $this->sanitized_attributes();
        $attribute_pairs = array();
        foreach($attriubes as $key => $value) {
            $attribute_pairs[] = "{$key}='{$value}'";
        }

        $sql  = "UPDATE ". self::$table_name . " SET ";
        $sql .= join(", ", $attribute_pairs);
        $sql .= " WHERE id = " . $database->escape_value($this->id);
        $database->query($sql);
        // NB: Unlike INSERT query where we test if the query is right,
        // with UPDATE we check for the number of affected rows rather

        return ($database->affected_rows() == 1) ? true : false;
    }

    public function delete() {
        global $database;
        // Don't forget your SQL syntax and good habits:
        // - DELETE FROM table WHERE condition LINIT 1
        // - escape all values to prevent SQL injection
        // = use LIMIT 1
        $sql = "DELETE FROM ". self::$table_name ." ";
        $sql .= "WHERE id = ". $database->escape_value($this->id);
        $sql .= " LIMIT 1";
        $database->query($sql);
        return ($database->affected_rows() == 1) ? true : false;
    }
}
?>
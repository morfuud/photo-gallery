<?php
require_once(LIB_PATH.DS.'database.php');

class Photograph {
    protected static $table_name = "photographs";
    protected static $db_fields = array('id', 'filename', 'type', 'size', 'caption');
    
    public $id;
    public $filename;
    public $type;
    public $size;
    public $caption;

    private $tmp_path;
    private $upload_dir = "images";
    public $errors = array();

    protected $upload_errors = array (

        UPLOAD_ERR_OK         => "No errors.",
        UPLOAD_ERR_INI_SIZE   => "Larger than upload_max_size",
        UPLOAD_ERR_FORM_SIZE  => "Larger than form MAX_FILE_SIZE",
        UPLOAD_ERR_PARTIAL    => "Partial upload",
        UPLOAD_ERR_NO_FILE    => "No file",
        UPLOAD_ERR_NO_TMP_DIR => "No temporary directory",
        UPLOAD_ERR_CANT_WRITE => "Can't write to disk",
        UPLOAD_ERR_EXTENSION  => "File upload stopped by extension"
    );

    // Pass in $_FILES['uploaded_file'] as an argument
    public function attach_file($file) {
        // Perform error checking on the form parameters
        if (!$file || empty($file) || !is_array($file)) {
            $this->errors[] = "No file was uploaded";
            return false;
        } elseif ($file['error'] != 0) {
            // error: report what PHP says went wrong
            $this->errors[] = $this->upload_errors[$file['error']];
            return false; 
        } else {
            // Set object attributes to the form parameters
            $this->temp_path = $file['tmp_name'];
            $this->filename = basename($file['name']);
            $this->type     = $file['type'];
            $this->size     = $file['size'];
            // Don't worry about saving anything to the database yet
            return true;
        }


    }

    public function save() {
        // A new record won't have an id yet
        if (isset($this->id)) {
            // Really just to update the caption
            $this->update();
        } else {
            // Make sure there are no errors

            // Can't save if there are pre-existing errors
            if (!empty($this->errors)) { return false; }

            // Make sure the caption is not too long for use
            if (strlen($this->caption) > 255) {
                
            }
            // Determine the target_path
            $target_path = SITE_ROOT.DS.'PUBLIC'.DS. $this->upload_dir 
            .DS. $this->filename;

            // Make sure a file doesn't already exist in the target location
            if (file_exists($target_path)) {
                $this->errors[] = "The file {$this->filename} already exists.";
                return false;
            }

            // Attempt to move the file
            if (move_uploaded_file($this->temp_path, $target_path)) {
                // success
                // Save a corresponding entry to the database
                if ($this->create()) {
                    // We are done with the temp_path, the file isn't there anymore.
                    unset($tmp_path);
                    return true;
                }
            } else {
                // failure
                $this->errors[] = "The file upload failed, possible due to incorrect
                permissions on the upload folder.";
                return false;
            }


        }
    }

    public function image_path() {
        return $this->upload_dir.DS.$this->filename;
    }

    public function size_as_text() {
        if ($this->size < 1024) {
            return "{$this->size} bytes";
        } elseif ($this->size < 1048576) {
            $size_kb = round($this->size/1024);
            return "{$size_kb} kB";
        } else {
            $size_mb = round($this->size/1048576, 1);
            return "{$isze_mb} MB";
        }
    }

    public function destroy() {
        // First remove the database entry
        if ($this->delete()) {
            // then remove the file
            $target_path = SITE_ROOT.DS."public".DS.$this->image_path();
            return unlink($target_path) ? true : false;
        } else {
            // database delete failed
            return false;
        }
    }

    public function comments() {
        return Comment::find_comments_on($this->id);
    }

    // common database methods
    public static function find_all() {
        global $database;
        $sql = "SELECT * FROM " . self::$table_name;
        return self::find_by_sql($sql);
    }

    public static function find_by_id($id=0) {
        global $database;
        $sql = "SELECT * FROM " . self::$table_name . " WHERE id=" . 
        $database->escape_value($id) . " LIMIT 1";
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
        $row = $database->fetch_array($result_set);
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

    // Replaced with a custom save
    // public function save() {
    //     // A new record won't have an id yet
    //     return (isset($this->id)) ? $this->update() : $this->create();
    // }

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
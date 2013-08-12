<?php 
class Icommerce_Db {
    
	static $_read, $_write;
    
	static function getDbRead(){
        if( !self::$_read ){
            self::$_read = Mage::getSingleton('core/resource')->getConnection('core_read');
        }
        return self::$_read;
	}
	
	 static function getDbWrite(){
        if( !self::$_write ){
            self::$_write = Mage::getSingleton('core/resource')->getConnection('core_write');
        }
        return self::$_write;
	}

	public static function tableExists( $table ){
		$rd = self::getDbRead();
		$r = $rd->query( "SHOW TABLES LIKE '$table'" );
		foreach( $r as $rr ){
			return true;
		}
		return false;
    }
    	public static function columnExists( $table, $column ){
		$rd = self::getDbRead();
		$r = $rd->query( "SHOW COLUMNS FROM `$table` LIKE '$column'" );
		foreach( $r as $rr ){
			return true;
		}
		return false;
	}
    
 public static function addColumn( $table, $column, $type, $length=null, $default_val=null, $enums=null ){
        return self::insertTableColumn( $table, $column, $type, $length, $default_val, $enums );
    }

	public static function insertTableColumn( $table, $column, $type, $length=null, $default_val=null, $enums=null ){
		$wr = self::getDbWrite();
		$sql = "ALTER TABLE `$table` ADD `$column` ";
		if( $type=="enum" ){
			if( is_array($enums) ){
				$enums = implode(",",$enums);
			}
			$sql .= "ENUM(" . $enums . ") ";
		}
        else {
			$sql .= $type . " ";
            if( $length ){
                $sql .= "($length) ";
            }
        }
		$sql .= "NOT NULL ";
		if( $default_val ) {
			if( is_string($default_val) ){
				$default_val = "'$default_val'";
			}
			$sql .= "DEFAULT $default_val ";
		}
        
		$r = $wr->query( $sql );
	}

    
}

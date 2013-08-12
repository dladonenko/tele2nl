<?php 

    class Icommerce_Log { 
    
        static function checkCreateDir( $log_dir ){
            $sl = strlen($log_dir);
            if( $sl && $log_dir[$sl-1]=='/' ){
                $log_dir = substr($log_dir,0,$sl-1);
            }
            if( !is_dir($log_dir) ){
                if( is_file($log_dir) ){
                    @unlink($log_dir);
                }
                if( !@mkdir( $log_dir ) ){
                    return null;
                }
            }
            return true;
        }
    
        // Return incrementing sequence number for a directory
        static function getNextSeqNo( $path, $ext="" ){
            if( !self::checkCreateDir($path) ){
                return null;
            }
            $full_path = $path."/seq_no".($ext?"-":"").$ext;
            $s = @file_get_contents( $full_path );
            //if( $s===FALSE ) return null;
            $no = (int)$s;
            if( !$no ) $no = 0;
            $r = @file_put_contents( $full_path, (int)(++$no) );
            if( $r===FALSE ) return null;
            return $no;
        }
        
        static function append( $log_path, $msg ){
            return self::appendToLog( $log_path, $msg );
        }
        
        static function appendToLog( $log_path, $msg ){
            try { 
                if( file_exists($log_path) ){   
                    $fp = fopen( $log_path, "a" );
                } else {
                    // Directory exists?
                    $p = strrpos( $log_path, "/" );
                    if( $p!==FALSE ){
                        $log_dir = substr( $log_path, 0, $p );
                        if( !self::checkCreateDir($log_dir) ){
                            return null;
                        }
                    }
                    $fp = fopen( $log_path, "w" );
                }
            } catch( Exception $e ) {
                return null;
            }
            if( !$fp ) return null;
            $sl = strlen($msg);
            if( !$sl || $msg[$sl-1]!=="\n" ){
                $msg .= "\n";
            }
            fwrite( $fp, "--- " . @date("r") . "---\n" );
            fwrite( $fp, $msg );
            fclose( $fp );
            return true;
        }

        static function writeSeqFile( $log_path, $ext, $item ){
            $sl = strlen($log_path);
            if( $sl>0 && $log_path[$sl-1]!='/' ) $log_path .= "/";
            $no = self::getNextSeqNo( $log_path, $ext );
            if( $no===FALSE ) return null;
            
            $fp = null;
            try {
                $fp = fopen( $log_path.$ext."-".$no.".log", "w" );
            } catch( Exception $e ){  }
            if( !$fp ) return null;
            
            $msg = $item;
            if( is_array($item) ){
                $msg = print_r($item,true);
            }
            fwrite( $fp, $msg );
            fclose( $fp );
            
            return true;
        }
        
        
    }
    
?>
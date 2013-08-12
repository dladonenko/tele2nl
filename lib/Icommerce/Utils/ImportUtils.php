<?php 

function breakupFullName( $nm, $empty_name="-" ){ 
    $parts = explode( " ", $nm );
    if( count($parts)<2 )
        return array( 'first' => $nm, 'middle'=>"", 'last'=>$empty_name );
    if( count($parts)==2 )
        return array( 'first' => $parts[0], 'middle'=>"", 'last'=>$parts[1] );
    if( count($parts)==3 )
        return array( 'first' => $parts[0], 'middle'=>$parts[1], 'last'=>$parts[2] );
    
    // Merge two adjacent parts until we have only 3
    while( count($parts)>3 ) {
        $ix_best1 = $ix_best2 = -1;
        $l_best = 1000;
        $ix_last = -1;
        foreach( $parts as $ix => $p ){
            if( $ix_last>=0 && strlen($parts[$ix_last])+strlen($p)<$l_best ){
                $l_best = strlen($parts[$ix_last]) + strlen($p);
                $ix_best1 = $ix_last;
                $ix_best2 = $ix;
            }
            $ix_last = $ix;
        }
        // Merge two
        $parts[$ix_best1] = $parts[$ix_best1] . " " . $parts[$ix_best2];
        unset( $parts[$ix_best2] );
    }
    
    // Renumber the array
    $ix = 0; 
    $parts2 = array();
	foreach( $parts as $k => $v ){
		$parts2[$ix++] = $v;
	}
    return array( 'first' => $parts2[0], 'middle' => $parts2[1], 'last' => $parts2[2] );
}

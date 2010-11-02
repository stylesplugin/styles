<?php
if ( !class_exists('FirePHP') ) {
	ob_start();
	include_once '../FirePHPCore/fb.php';
	
}

// Extensions get loaded by load_extensions inside PDStylesAdminController (passed the StyleGroup object)
// Extension is constructed
// Helpers are copied into the extension
// Extension gets added to StyleGroup object



// Group object should detect child object types and instantiate each appropriately

abstract class PDStyle_Group() {
	
	function __construct($args) {
		
		
	}
	
}


class UnitException extends Exception {}

abstract class Unit {
    function getComposite() {
        return null;
    }

    abstract function bombardStrength();
}

class Archer extends Unit {
    function bombardStrength() {
        return 4;
    }
}

class Cavalry extends Unit {
    function bombardStrength() {
        return 2;
    }
}

class LaserCanonUnit extends Unit {
    function bombardStrength() {
        return 44;
    }
}

abstract class CompositeUnit extends Unit {
    private $units = array();

    function getComposite() {
        return $this;
    }

    function units() {
        return $this->units;
    }

    function removeUnit( Unit $unit ) {
        $units = array();
        foreach ( $this->units as $thisunit ) {
            if ( $unit !== $thisunit ) {
                $units[] = $thisunit;
            }
        }
        $this->units = $units;
    }

    function addUnit( Unit $unit ) {
        if ( in_array( $unit, $this->units, true ) ) {
            return;
        }
        $this->units[] = $unit;
    }
}

class TroopCarrier extends CompositeUnit {
	
	function __construct() {
		// echo 'hi';
	}
	
    function addUnit( Unit $unit ) {
        if ( $unit instanceof Cavalry ) {
            throw new UnitException("Can't get a horse on the vehicle");
        }
        parent::addUnit( $unit );
    }

    function bombardStrength() {
        return 0;
    }
}

class Army extends CompositeUnit {

    function bombardStrength() {
        $ret = 0;
        foreach( $this->units() as $unit ) {
            $ret += $unit->bombardStrength();
        }
        return $ret;
    }
}

$main_army = new TroopCarrier();
$main_army->addUnit( new Archer() );
$main_army->addUnit( new LaserCanonUnit() );
// will throw error
// $main_army->addUnit( new Cavalry() );

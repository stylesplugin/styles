<?php
/**
 * @todo Write a master data manager / constructor
 * @todo save to DB interface
 * @todo read from DB on __construct
 * @todo save to XML interface
 */

class PD_Exception extends Exception {}

abstract class PD_UI {
	
	private $key;
	private $value;
	private $title;
	private $description;
	
	function __construct( $key, $args=array() ) {
		$this->key = $key;
		
		foreach ($args as $key => $val) {
			$this->$key = $val;
		}
		
		if (empty($this->title)) {
			$this->title = ucwords(str_replace('_', ' ', $this->key));
		}
		
	}
	
    function get_composite() {
        return null;
    }

	function the_title() {
		echo $this->title;
	}
	
	function the_description() {
		echo $this->description;
	}
	
	function the_value() {
		echo $this->value;
	}
	
    abstract function admin_output();
}

class PD_Slider extends PD_UI {
	
	public $unit = array(
		'start' => 'px',
		'end' => 'px',
		'css_after' => 'px',
		'css_before' => '',
	);
	
	public $min = 0;
	public $max = 100;

	/**
	 * Display slider form element for Admin page
	 *
	 * @todo Get $id and the_value from some global registry, based on hierarchy / saved styles (style consists of UI elements?)
	 **/
    function admin_output() {
        //if (empty($css[$key])) {
		//	$css[$key] = 0;
		//}
		
		$key = 'KEY';
		$css[$key] = 'VALUE';
		$id = 'ID';
		
		?>
		<tr valign="top">
			<th scope="row">
				<label for="<?php echo $id; ?>">
					<?php $this->the_title() ?>:
				</label>
			</th>
			<td>
				<input class="slider" type="text" name="<?php echo $id; ?>" id="<?php echo $id; ?>" value="<?php $this->the_value() ?>" />
				<?php $this->the_description(); ?>
			</td>
		</tr>
		<?php
    }
}

##########

echo '<pre>';
$whatever = new PD_Slider( 'some_key', array( 'value'=>'123' ) );
print_r($whatever);
$whatever->admin_output();

#	$main_army = new TroopCarrier();
#	$main_army->addUnit( new Archer() );
#	$main_army->addUnit( new LaserCanonUnit() );
#	// will throw error
#	$main_army->addUnit( new Cavalry() );


exit();

#########
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

    function addUnit( Unit $unit ) {
        if ( $unit instanceof Cavalry ) {
            throw new PD_Exception("Can't get a horse on the vehicle");
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
$main_army->addUnit( new Cavalry() );

<?php

class Drawing {
  private $command = "";
  private $allowed_commands = ["C", "L", "R", "B", "Q"];
  private $canvas_size = [];  
  private $canvas = [];  
    
  /**
  * function that listen CLI command input
  */
  public function cliListen() {
    while($this->command != "Q") {
      $options = [
                "C. Create canvas",
                "L. Line",
                "R. Rectangle",
                "B. Color",
                "Q. Quit"];
            echo "Available options: \n" .PHP_EOL;
            foreach($options as $option) {
                echo $option .PHP_EOL;
            }
      echo PHP_EOL . "Enter your command: ";
      $input = stream_get_line(STDIN, 512, PHP_EOL);
      echo $this->main($input);
    }
  }

    private function drawLine($prop, $pattern = "*") {
        $prop = array_map("intval", $prop); // Only  integer.
        if($prop[1] == $prop[3]) { 
            $line = range($prop[0], $prop[2]);
            foreach($line as $x)
                $this->canvas[$x][$prop[1]] = $pattern;
        } elseif($prop[0] == $prop[2]) { 
            $line = range($prop[1], $prop[3]);
            foreach($line as $y)
                $this->canvas[$prop[0]][$y] = $pattern;
        } else {
            return false;
        }
        return true;
    }

     private function colorFill($x, $y, $color) {
        if($x < 1 || $y < 1 || $x > $this->canvas_size[0] || $y > $this->canvas_size[1])
            return;

        if(isset($this->canvas[$x][$y]))
            return;

        $this->canvas[$x][$y] = $color; // Sets color
        $this->colorFill($x+1, $y, $color);
        $this->colorFill($x-1, $y, $color);
        $this->colorFill($x, $y+1, $color);
        $this->colorFill($x, $y-1, $color);

    }

  public function main($input) {
    $this->command = substr($input, 0, 1);

    $output = "";

    if(in_array($this->command, $this->allowed_commands)) {

    if($this->command != "Q") {
      $prop = explode(" ", substr($input, 2));

    if($this->command == "C") {
      $this->canvas = [];
      $this->canvas_size = array_slice($prop, 0, 2);
        if(count($prop) < 2) {
            $output = "Canvas size arguments must be 4" . PHP_EOL;
        }
    //if canvas had not been set
    } elseif(empty($this->canvas_size)) {
    $output = "Please type C for new canvas." . PHP_EOL;
     } elseif($this->command == "L") {
          if($this->canvas_size == "") {
               $output = "First create a canvas." .PHP_EOL;
    } else {
        if(!$this->drawLine($prop))
            $output = "Sorry only horizontal and vertical lines are supported at this time." .  PHP_EOL;
    }
      } elseif($this->command == "R") {
                $this->drawLine([$prop[0], $prop[1], $prop[2], $prop[1]]);
                $this->drawLine([$prop[0], $prop[3], $prop[2], $prop[3]]);
                $this->drawLine([$prop[0], $prop[1], $prop[0], $prop[3]]);
                $this->drawLine([$prop[2], $prop[1], $prop[2], $prop[3]]);
      } elseif($this->command == "B") {
                if($this->canvas_size !== "") {
              if(count($prop) < 3 && $this->canvas_size !== "") {
        $output = "Color arguments must be 3" . PHP_EOL;
        } else {
            $color = substr($prop[2], 0, 1);
            $this->colorFill($prop[0], $prop[1], $color); 
        }
        
    }

    }

              
                if($output == "") { 
                    for($r = 0; $r <= $this->canvas_size[1] + 1; $r++ ) {
                        for($c = 0; $c <= $this->canvas_size[0] + 1; $c++ ) {
                            if($r == 0 || $r == $this->canvas_size[1] + 1)
                                $output .= "-";
                            elseif($c == 0 || $c == $this->canvas_size[0] + 1)
                                $output .= "|";
                            elseif(isset($this->canvas[$c][$r]))
                                $output .= $this->canvas[$c][$r];
                            else
                                $output .= " ";
                        }
                        $output .= "\n";
                    }
                }
            }

        } else {
            $output = "Command $this->command do not exist. " . PHP_EOL;
        }

        return $output;
    }

}

?>
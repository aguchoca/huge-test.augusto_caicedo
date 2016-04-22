<?php

class Drawing {
    private $allowed_commands = ["C", "L", "R", "B", "Q"];
    private $canvas = [];  
    private $pixels = [];  
    private $command = "";
    
   /**
    * function that listen CLI input
    */
    public function cliListener() {
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
            echo "Enter your command: ";
            $input = stream_get_line(STDIN, 512, PHP_EOL);
            echo $this->main($input);
        }
    }

    /*
    *
    * @param    array       $args 
    * @param    string      $char
    * @return   boolean     True or false
    */
    private function drawLine($args, $char = "x") {
        $args = array_map("intval", $args); // make sure args are integers
        if($args[1] == $args[3]) { // horizontal line
            $line = range($args[0], $args[2]);
            foreach($line as $x)
                $this->pixels[$x][$args[1]] = $char;
        } elseif($args[0] == $args[2]) { // vertical line
            $line = range($args[1], $args[3]);
            foreach($line as $y)
                $this->pixels[$args[0]][$y] = $char;
        } else {
            return false;
        }
        return true;
    }

     private function floodFill($x, $y, $color) {
        if($x < 1 || $y < 1 || $x > $this->canvas[0] || $y > $this->canvas[1])
            return;

        if(isset($this->pixels[$x][$y]))
            return;

        $this->pixels[$x][$y] = $color;

        // http://en.wikipedia.org/wiki/Flood_fill - this fills in all directions with boundaries.
        $this->floodFill($x+1, $y, $color);
        $this->floodFill($x-1, $y, $color);
        $this->floodFill($x, $y+1, $color);
        $this->floodFill($x, $y-1, $color);

    }

    /**
    * Parse input string and render canvas
    *
    * @param    string      $input  raw, unparsed command
    * @return   string      multi line canvas grid OR input parse error message
    */
    public function main($input) {
        $this->command = substr($input, 0, 1);

        $output = "";

        if(in_array($this->command, $this->allowed_commands)) {
            
            //run only if Quit is not desired
            if($this->command != "Q") {
                $args = explode(" ", substr($input, 2));
                
                if($this->command == "C") {
                    $this->pixels = [];
                    $this->canvas = array_slice($args, 0, 2);
                    if(count($args) < 2) {
                    $output = "Canvas size arguments must be 4" . PHP_EOL;
                }
                //if canvas had not been set
                } elseif(empty($this->canvas)) {
                    $output = "Please type C for new canvas." . PHP_EOL;
                } elseif($this->command == "L") {
                    if($this->canvas == "") {
                        $output = "First create a canvas." .PHP_EOL;
                    } else {
                        if(!$this->drawLine($args))
                            $output = "Sorry only horizontal and vertical lines are supported at this time." .  PHP_EOL;
                    }
                } elseif($this->command == "R") {
                    $this->drawLine([$args[0], $args[1], $args[2], $args[1]]);
                    $this->drawLine([$args[0], $args[3], $args[2], $args[3]]);
                    $this->drawLine([$args[0], $args[1], $args[0], $args[3]]);
                    $this->drawLine([$args[2], $args[1], $args[2], $args[3]]);
                } elseif($this->command == "B") {
                    if($this->canvas !== "") {
                           if(count($args) < 3 && $this->canvas !== "") {
                        $output = "Color arguments must be 3" . PHP_EOL;
                        } else {
                            $color = substr($args[2], 0, 1);
                            $this->floodFill($args[0], $args[1], $color); 
                        }
                        
                    }
                    
                  }

                // echo the canvas
                if($output == "") { 
                    for($r = 0; $r <= $this->canvas[1] + 1; $r++ ) {
                        for($c = 0; $c <= $this->canvas[0] + 1; $c++ ) {
                            if($r == 0 || $r == $this->canvas[1] + 1)
                                $output .= "-";
                            elseif($c == 0 || $c == $this->canvas[0] + 1)
                                $output .= "|";
                            elseif(isset($this->pixels[$c][$r]))
                                $output .= $this->pixels[$c][$r];
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
<?php
    // FizzBuzz function that takes a number $n as a parameter
    function fizzBuzz($n) {
        // Loop from 1 to $n
        for ($i = 1; $i <= $n; $i++) {
            // Initialize the output string
            $output = '';
            
            // If $i is divisible by 3, add 'Fizz' to the output
            if ($i % 3 === 0) $output .= 'Fizz';
            
            // If $i is divisible by 5, add 'Buzz' to the output
            if ($i % 5 === 0) $output .= 'Buzz';
            
            // Print the output or the number if the output is empty
            echo $output ?: $i;
            echo "\n"; // New line for each output
        }
    }

    // Call the function with the desired value of N
    fizzBuzz(100);
?>
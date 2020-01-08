<?php

namespace App\Helpers;

class PasswordGenerator {

    private $letters;     
    private $numbers;
    private $array_length; 

    function __construct()
    {  
        $this->letters = array_merge(range('A', 'Z'), range('a', 'z'));
        $this->numbers = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9];
        $this->array_lenght = 8;

    }

    function generate_password()
    {
        $new_password_array = [];
        
        $total_number_of_letters = count($this->letters);
                
        for ($x = 0; $x <= $this->array_lenght / 2 - 1 ; $x++) {

            $random_letter_index = rand(0, $total_number_of_letters - 1);          
            array_push($new_password_array, $this->letters[$random_letter_index]);

        }

        $total_number_of_numbers = count($this->numbers);

        for ($x = 0; $x <= $this->array_lenght / 2 - 1 ; $x++) {

            $random_number_index = rand(0, $total_number_of_numbers - 1);          
            array_push($new_password_array, $this->numbers[$random_number_index]);

        }

        $new_password = implode($new_password_array);
        
        return $new_password;
    }  






}
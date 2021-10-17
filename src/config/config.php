<?php
    return [
        "mail_from" => env("MAIL_FROM_ADDRESS"),
        "app_name" => "ticket",
        "mail_from_name" => env("MAIL_FROM_NAME"),

        // This is the rout to your homepage or anypage you want when the user clicks home
        "home_route" => 'home',

        //This is the email address you get replys to when ticket is not assigned to an agent
        "ticket_admin_email_address" => '',
        
        //This is the email name you get replys to when ticket is not assigned to an agent
        "ticket_admin_email_name" => "Ticket Manager",

        
    ];
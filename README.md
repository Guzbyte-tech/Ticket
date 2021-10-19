# Guzbyte Ticket V1.0.0

This is is simple laravel ticket package system that manages your ticketing system in your platform. This packages is Laravel 5+ to 8.  This packages leverages on the Laravel default User and Authentication class. This packages has its own routes with prefix "/ticket".


## Description 

A ticketing system is **a management tool that processes and catalogs customer service requests**. Tickets, also known as cases or issues, need to be properly stored alongside relevant user information. This ticketing system is user-friendly for customer service representatives, managers, and administrators.

## Features
1. Guzbyte ticket three main users roles users, agents, and admins.
2. User can create new ticket, view, comment on ticket as well close their own ticket
3. Ticket allows the upload of attachment.
4. On creation of ticket it is automatically assigned to available agents.
5. Allows only one ticket admin
6. Admin role ticket users can create categories, priorities and agents.
7. Admin roles ticket users can also comment on ticket.
8. Ticket can be re-assigned to another agent.
9. Easy texteditor for easy customization of tciekts.
 
## Requirements
**First Make sure you have got this Laravel setup working:**
1. Laravel 5+
2. Bootstrap 4
3. Users table set up
4. Laravel email configuration.


## Installation
The installation process is very easy.
Step 1. Run the following code in terminal<br>
<code>composer require guzbyte/ticket</code> <br>
Step 2. After install, you have to add this line on your `config/app.php` in Service Providers section<br>
<code>Guzbyte\Ticket\TicketServiceProvider::class</code><br>
Step 3. **(only for laravel 8)** Goto Vendor/guzbyte/ticket/src/config/ticket.php  edit the $users variable to <br><br>
<code> 
	$user  = new App\Models\User;
</code>
<br>
<br>
Step 4. Publish the package
<code> 
	php artisan vendor:publish
</code>
<br>
<br>
**Note** You can edit this to match your Users Model namespace not just for Laravel 8<br>
Step 5. Make sure authentication scalffolding is already active.<br>
Step 6. Run Migration. <br><br>
<code> 
	php artisan migrate
</code>
<br>

Step 6. Register at least one or more user into the system.<br>
Step 7. Run the installation route. https://your-website.com/ticket/install<br>
Step 8. Enter the email you want to be the main adminstrator of the system.<br>
** THATS IT !!!**<br>

Default Routes<br>
Users: https://your-website.com/ticket<br>
Agents: https://your-website.com/ticket/agent<br>
Admin: https://your-website.com/ticket/admin<br>

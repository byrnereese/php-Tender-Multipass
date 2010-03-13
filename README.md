This PHP library is intended for use with Tender App, an online
support ticketing system, and makes the generation of a "multipass" 
token much easier. 

Multipass is a simple protocol by which single sign is easily
achieved with the Tender service. By encoding a user's personal
information in an encrypted multipass token, a web site can quickly
and easily establish a session on TenderApp for the associated user
without the need for that user to login, or even register for an 
account.

# Prerequisites

* Services_JSON - a PEAR module
* mcrypt - a PHP extension

# Example

The following example shows how to use this library:

    <?php
    require 'TenderMultipass.php';
    $mp = new TenderMultipass('mysite','a-long-api-key-in-hex');
    $mp->expires( 60 ); // expire in 60 minutes
    $token = $mp->as_string( array( 
      email     => 'byrne@majordojo.com',
      name      => 'Byrne Reese',
      unique_id => 1
    ) );
    ?>
    <script type="text/javascript" charset="utf-8">
    Tender = {
      hideToggle: true,
      sso: "$token",
      widgetToggles: $('.tender-help')
    }
    </script>
    <script src="https://yoursite.tenderapp.com/tender_widget.js" 
            type="text/javascript"></script>

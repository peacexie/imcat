<?php

   require_once ("config.php");
  
   //Call to SetExpressCheckout using the shopping parameters collected from the shopping form on index.php and few from config.php 

   $returnURL = RETURN_URL;
   $cancelURL = CANCEL_URL; 
   
   if(isset($_POST["PAYMENTREQUEST_0_ITEMAMT"]))
		$_POST["L_PAYMENTREQUEST_0_AMT0"]=$_POST["PAYMENTREQUEST_0_ITEMAMT"];
  
   if(!isset($_POST['Confirm']) && isset($_POST['checkout'])){

		if($_REQUEST["checkout"] || isset($_SESSION['checkout'])) {
			$_SESSION['checkout'] = $_POST['checkout'];
		}
	$_SESSION['post_value'] = $_POST;
	
	//Assign the Return and Cancel to the Session object for ExpressCheckout Mark
	$returnURL = RETURN_URL_MARK;
	$_SESSION['post_value']['RETURN_URL'] = $returnURL;
	$_SESSION['post_value']['CANCEL_URL'] = $cancelURL;
	$_SESSION['EXPRESS_MARK'] = 'ECMark';
   include('header.php');
?>
   <div class="span4">
   </div>
   <div class="span5">
            <!--Form containing item parameters and seller credentials needed for SetExpressCheckout Call-->
            <form class="form" action="paypal_ec_mark.php" method="POST">
               <div class="row-fluid">
                  <div class="span6 inner-span">
                        <p class="lead">Shipping Address</p>
                        <table>
                        <input type="hidden" name="L_PAYMENTREQUEST_0_AMT" value="<?php echo $_POST["PAYMENTREQUEST_0_AMT"]; ?>">
                        <tr><td width="30%">First Name</td><td><input type="text" name="L_PAYMENTREQUEST_FIRSTNAME" value="Alegra"></input></td></tr>
                        <tr><td>Last Name:</td><td><input type="text" name="L_PAYMENTREQUEST_LASTNAME" value="Valava"></input></td></tr>
                        <tr><td>Address</td><td><input type="text" name="PAYMENTREQUEST_0_SHIPTOSTREET" value="55 East 52nd Street"></input></td></tr>
                        <tr><td>Address 1</td><td><input type="text" name="PAYMENTREQUEST_0_SHIPTOSTREET2" value="21st Floor"></input></td></tr>
                        <tr><td>City:</td><td><input type="text" name="PAYMENTREQUEST_0_SHIPTOCITY" value="New York" ></input></td></tr>
                        <tr><td>State:</td><td><input type="text" name="PAYMENTREQUEST_0_SHIPTOSTATE" value="NY" ></input></td></tr>
                        <tr><td>Postal Code:</td><td><input type="text" name="PAYMENTREQUEST_0_SHIPTOZIP" value="10022" ></input></td></tr>
                        <tr><td>Country</td><td><select id="PAYMENTREQUEST_0_SHIPTOCOUNTRY" name="PAYMENTREQUEST_0_SHIPTOCOUNTRY">
							<option value="AF">Afghanistan</option>
							<option value="ZW">Zimbabwe</option>
							</select></td>
						</tr>
                        <tr><td>Telephone:</td><td><input type="text" name="PAYMENTREQUEST_0_SHIPTOPHONENUM" value="" maxlength="12"></input></td></tr>
						
                        <tr><td colspan="2"><p class="lead">Shipping Detail:</p></td></tr>
                        <tr><td>Shipping Type: </td><td><select name="shipping_method" id="shipping_method" style="width: 250px;" class="required-entry">
					<optgroup label="United Parcel Service" style="font-style:normal;">
					<option value="2.00">
					Worldwide Expedited - $2.00</option>
					<option value="3.00">
					Worldwide Express Saver - $3.00</option>
					</optgroup>
					<optgroup label="Flat Rate" style="font-style:normal;">
					<option value="0.00" selected>
					Fixed - $0.00</option>
					</optgroup>
					</select><br>
						</td></tr>
					<tr><td colspan="2"><p class="lead">Payment Methods:</p></td></tr>	
					<tr><td colspan="2">
						<input id="paypal_payment_option" value="paypal_express" type="radio" name="paymentMethod" title="PayPal Express Checkout" class="radio" checked>
						<img src="https://fpdbs.paypal.com/dynamicimageweb?cmd=_dynamic-image&amp;buttontype=ecmark&amp;locale=en_US" alt="Acceptance Mark" class="v-middle">&nbsp;
						<a href="https://www.paypal.com/us/cgi-bin/webscr?cmd=xpt/Marketing/popup/OLCWhatIsPayPal-outside" onclick="javascript:window.open('https://www.paypal.com/us/cgi-bin/webscr?cmd=xpt/Marketing/popup/OLCWhatIsPayPal-outside','olcwhatispaypal','toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=yes, ,left=0, top=0, width=400, height=350'); return false;">What is PayPal?</a>
					</td></tr>
					<tr><td colspan="2"><input id="p_method_paypal_express" value="credit_card" type="radio" name="paymentMethod" title="PayPal Express Checkout" class="radio" disabled>&nbsp;Credit Card</td></tr>
					<tr><td>&nbsp;</td></tr>

                        </table>
                        <input type="submit" id="placeOrderBtn" class="btn btn-primary btn-large" name="PlaceOrder" value="Place Order" />
                  </div>
               </div>
            </form>
   </div>
   <div class="span3">
   </div>
    <script src="//www.paypalobjects.com/api/checkout.js" async></script>
      <script type="text/javascript">
      window.paypalCheckoutReady = function () {
          paypal.checkout.setup('<?php echo($merchantID); ?>', {
              button: 'placeOrderBtn',
              environment: '<?php echo($env); ?>',
              condition: function () {
                      return document.getElementById('paypal_payment_option').checked === true;
                  }
          });
      };
      </script>
   <?php
   } else {

   $resArray = CallShortcutExpressCheckout ($_POST, $returnURL, $cancelURL);
   $ack = strtoupper($resArray["ACK"]);
   if($ack=="SUCCESS" || $ack=="SUCCESSWITHWARNING")  //if SetExpressCheckout API call is successful
   {
		RedirectToPayPal ( $resArray["TOKEN"] );
   } 
   else  
   {
   	//Display a user friendly Error on the page using any of the following error information returned by PayPal
   	$ErrorCode = urldecode($resArray["L_ERRORCODE0"]);
   	$ErrorShortMsg = urldecode($resArray["L_SHORTMESSAGE0"]);
   	$ErrorLongMsg = urldecode($resArray["L_LONGMESSAGE0"]);
   	$ErrorSeverityCode = urldecode($resArray["L_SEVERITYCODE0"]);
   	
   	echo "SetExpressCheckout API call failed. ";
   	echo "Detailed Error Message: " . $ErrorLongMsg;
   	echo "Short Error Message: " . $ErrorShortMsg;
   	echo "Error Code: " . $ErrorCode;
   	echo "Error Severity Code: " . $ErrorSeverityCode;
   }
   }
   
?>

Pod payment for services Sample
==================================
This is a simple php app that uses Podium services to buy something. For a simple process:
- User must login with Pod SSO in business application.
- Business follows itself by user token. (to issue invoice for a user, he must be a follower)
- Business issues invoice for the user with the items and prices.
- Business provides the payment link for user and redirects him for payment.
- User pays for invoice by Pod virtual account or by IPG.
- Business verifies payment after recieving payment info on redirect URL.
- Business closes invoice for settlement after a while. (when cancelation is not available)
- business cancels invoice if payed and not closed and money returns to user

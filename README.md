# org.civicoop.smsapi
SMS API for CiviCRM to send sms through the API

The entity for the SMS API is SMS and the action is Send.
Parameters for the api are specified below:
- contact_id: list of contacts IDs to create the PDF Letter (separated by ",")
- template_id: ID of the message template which will be used in the API. 
- provider_id: ID of the SMS provider
- from_contact_id: (**Not required**) the contact ID who is sending the sms

*It is not possible to specify your own message through the API.*

    

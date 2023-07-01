# org.civicoop.smsapi
SMS API for CiviCRM to send sms through the API

The entity for the SMS API is SMS and the action is Send.
Parameters for the api are specified below:
- contact_id: list of contacts IDs to create the PDF Letter (separated by ",")
- template_id: ID of the message template which will be used in the API.
- provider_id: ID of the SMS provider
- from_contact_id: (**Not required**) the contact ID who is sending the sms
- alternative_receiver_phone_number: enter a phone number who will receive the SMS, if empty the sms is send to the contact

# Known Issues

- It is not possible to specify your own message through the API.
- The API does not respect the Send SMS permission. That is intentional. An API empowers a developer to create new stuff, and that comes with new permission needs.
- This extension contains a CiviRules action, that is installed when the CiviRules extension is found. If you install CiviRules later you can add the action by running the following SQL script in the CiviCRM database.

````sql
INSERT INTO civirule_action (name, label, class_name, is_active)
VALUES('smsapi_send', 'Send SMS', 'CRM_Smsapi_CivirulesAction', 1);
````


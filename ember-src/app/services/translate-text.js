import Service from '@ember/service';
import ENV from "../config/environment";

const TRANSLATIONS = {
  "ENGLISH": {
    "name": "Name",
    "firstName": "First Name",
    "familyName": "Last Name",
    "email": "Email",
    "street1": "Street Address",
    "street2": "Street Address 2",
    "postalCode": "Postal Code",
    "city": "City",
    "state": "State",
    "chooseState": "Choose State",
    "country": "Country",
    "chooseCountry": "Choose Country",
    "address": "Address",
    "phoneNumber": "Phone Number",
    "fieldCantBeBlank": "This field can't be blank",
    "fieldIsTooShort": "This field is too short (minimum is 3 characters)",
    "fieldMustBeValidEmail": "This field must be a valid email address",
    "provideInfo": "Provide Info",
    "proceed": "Proceed",
    "selectAServicePlan": "Select a Service Plan",
    "selectedServicePlan": "Selected Service Plan",
    "viewOtherOptions": "View Other Options",
    "select": "Select",
    "selected": "Selected",
    "monthPeriod": "Month period",
    "recurring": "recurring",
    "currencySymbol": "$",
    "payment": "Payment",
    "reviewServiceSignupDetails": "Review your service signup details",
    "serviceDetails": "Service Details",
    "accountInfo": "Account Info",
    "submittingInformation": "Submitting Information",
    "errorWithRequest": "Sorry, there was an error with your request.",
    "confirmInformationAndSubmit": "Confirm Information and Submit",
    "signupComplete": "Signup Complete",
  }
}

export default Service.extend({
  key(key) {
    return TRANSLATIONS[ENV.APP.pluginTranslation][key];
  }
});
